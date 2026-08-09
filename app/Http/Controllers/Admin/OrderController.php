<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $query = Order::where('platform_id', $platformId)
            ->with(['customer', 'items']);

        // Search by order number, customer name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by order status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Quick stats
        $stats = [
            'pending' => Order::where('platform_id', $platformId)->where('status', 'pending')->count(),
            'confirmed' => Order::where('platform_id', $platformId)->where('status', 'confirmed')->count(),
            'out_for_delivery' => Order::where('platform_id', $platformId)->where('status', 'out_for_delivery')->count(),
            'delivered' => Order::where('platform_id', $platformId)->where('status', 'delivered')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($order->platform_id !== $platformId, 403);

        $order->load([
            'customer',
            'items',
            'statusHistory' => fn($q) => $q->orderBy('created_at'),
            'payments',
            'delivery.agent',
            'deliveryZone',
            'deliveryRate',
        ]);

        $availableAgents = DeliveryAgent::where('platform_id', $platformId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact('order', 'availableAgents'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($order->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,packed,out_for_delivery,delivered,failed_delivery,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $admin = auth('admin')->user();

        DB::transaction(function () use ($order, $validated, $admin) {
            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            $updateData = ['status' => $newStatus];

            // Set timestamps for specific statuses
            if ($newStatus === 'confirmed' && !$order->confirmed_at) {
                $updateData['confirmed_at'] = now();
            }
            if ($newStatus === 'delivered' && !$order->delivered_at) {
                $updateData['delivered_at'] = now();
            }
            if ($newStatus === 'cancelled' && !$order->cancelled_at) {
                $updateData['cancelled_at'] = now();
            }

            $order->update($updateData);

            // Log to status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'note' => $validated['note'] ?? "Status changed from {$oldStatus} to {$newStatus} by admin",
                'changed_by_type' => 'admin',
                'changed_by_id' => $admin->id,
            ]);

            // If delivered + COD, mark payment as successful
            if ($newStatus === 'delivered' && $order->is_cod && $order->payment_status === 'pending') {
                $order->update(['payment_status' => 'paid']);

                if ($order->latestPayment) {
                    $order->latestPayment->markAsSuccessful([
                        'collected_by' => 'delivery_agent',
                        'collected_at' => now()->toDateTimeString(),
                    ]);
                }
            }
         });

        // Send status notification to customer
        try {
            app(\App\Services\OrderNotifier::class)->notifyCustomer($order->fresh(), $validated['status']);
        } catch (\Throwable $e) {
            \Log::error('Status notification failed: ' . $e->getMessage());
        }

        return back()->with('success', "Order status updated to {$validated['status']}.");
    }

    /**
     * Assign delivery agent.
     */
    public function assignAgent(Request $request, Order $order): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($order->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
        ]);

        $agent = DeliveryAgent::where('id', $validated['delivery_agent_id'])
            ->where('platform_id', $platformId)
            ->firstOrFail();

        $delivery = $order->delivery;

        if (!$delivery) {
            return back()->with('error', 'No delivery record found for this order.');
        }

        DB::transaction(function () use ($delivery, $agent, $order) {
            $delivery->update([
                'delivery_agent_id' => $agent->id,
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'note' => "Assigned to delivery agent: {$agent->name}",
                'changed_by_type' => 'admin',
                'changed_by_id' => auth('admin')->id(),
            ]);
        });

        return back()->with('success', "Order assigned to {$agent->name}.");
    }

    /**
     * Mark COD payment as collected.
     */
    public function markPaid(Order $order): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($order->platform_id !== $platformId, 403);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Order is already marked as paid.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['payment_status' => 'paid']);

            if ($order->latestPayment) {
                $order->latestPayment->markAsSuccessful([
                    'manually_marked_by_admin' => auth('admin')->id(),
                    'marked_at' => now()->toDateTimeString(),
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'note' => 'Payment manually marked as received by admin',
                'changed_by_type' => 'admin',
                'changed_by_id' => auth('admin')->id(),
            ]);
        });

        return back()->with('success', 'Payment marked as received.');
    }

    /**
     * Add internal admin note.
     */
    public function addNote(Request $request, Order $order): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($order->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $order->update(['admin_notes' => $validated['admin_notes']]);

        return back()->with('success', 'Note updated.');
    }
}