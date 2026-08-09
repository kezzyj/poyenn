<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $agent = auth('agent')->user();

        // Stats
        $stats = [
            'assigned' => Delivery::where('delivery_agent_id', $agent->id)->where('status', 'assigned')->count(),
            'in_progress' => Delivery::where('delivery_agent_id', $agent->id)->whereIn('status', ['picked_up', 'in_transit'])->count(),
            'completed_today' => Delivery::where('delivery_agent_id', $agent->id)
                ->where('status', 'delivered')
                ->whereDate('delivered_at', today())
                ->count(),
            'completed_total' => Delivery::where('delivery_agent_id', $agent->id)
                ->where('status', 'delivered')
                ->count(),
        ];

        // Active deliveries (not yet delivered or failed)
        $activeDeliveries = Delivery::where('delivery_agent_id', $agent->id)
            ->whereIn('status', ['assigned', 'picked_up', 'in_transit'])
            ->with(['order.customer', 'order.items'])
            ->latest('assigned_at')
            ->get();

        return view('agent.dashboard', compact('stats', 'activeDeliveries'));
    }

    public function show(Delivery $delivery): View
    {
        $agent = auth('agent')->user();
        abort_if($delivery->delivery_agent_id !== $agent->id, 403);

        $delivery->load(['order.customer', 'order.items', 'order.statusHistory']);

        return view('agent.delivery-show', compact('delivery'));
    }

    public function history(): View
    {
        $agent = auth('agent')->user();

        $completedDeliveries = Delivery::where('delivery_agent_id', $agent->id)
            ->whereIn('status', ['delivered', 'failed'])
            ->with(['order'])
            ->latest('updated_at')
            ->paginate(15);

        return view('agent.history', compact('completedDeliveries'));
    }

    /**
     * Mark as picked up from warehouse.
     */
    public function pickUp(Delivery $delivery): RedirectResponse
    {
        $agent = auth('agent')->user();
        abort_if($delivery->delivery_agent_id !== $agent->id, 403);

        if ($delivery->status !== 'assigned') {
            return back()->with('error', 'Cannot pick up this delivery in its current state.');
        }

        DB::transaction(function () use ($delivery, $agent) {
            $delivery->markAsPickedUp();

            OrderStatusHistory::create([
                'order_id' => $delivery->order_id,
                'status' => $delivery->order->status,
                'note' => "Package picked up by {$agent->name}",
                'changed_by_type' => 'delivery_agent',
                'changed_by_id' => $agent->id,
            ]);
        });

        return back()->with('success', 'Delivery picked up. Mark as in transit when you start moving.');
    }

    /**
     * Mark as in transit.
     */
    public function inTransit(Delivery $delivery): RedirectResponse
    {
        $agent = auth('agent')->user();
        abort_if($delivery->delivery_agent_id !== $agent->id, 403);

        if ($delivery->status !== 'picked_up') {
            return back()->with('error', 'Mark as picked up first.');
        }

        DB::transaction(function () use ($delivery, $agent) {
            $delivery->markAsInTransit();

            // Also update the order to "out for delivery"
            $delivery->order->update(['status' => 'out_for_delivery']);

            OrderStatusHistory::create([
                'order_id' => $delivery->order_id,
                'status' => 'out_for_delivery',
                'note' => "Out for delivery with {$agent->name}",
                'changed_by_type' => 'delivery_agent',
                'changed_by_id' => $agent->id,
            ]);
         });

        try {
            app(\App\Services\OrderNotifier::class)->notifyCustomer($delivery->order->fresh(), 'out_for_delivery');
        } catch (\Throwable $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Marked as in transit. Drive safely.');
    }

    /**
     * Mark as delivered with optional photo proof.
     */
    public function deliver(Request $request, Delivery $delivery): RedirectResponse
    {
        $agent = auth('agent')->user();
        abort_if($delivery->delivery_agent_id !== $agent->id, 403);

        if (!in_array($delivery->status, ['picked_up', 'in_transit'])) {
            return back()->with('error', 'Cannot mark this as delivered.');
        }

        $validated = $request->validate([
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($delivery, $agent, $request, $validated) {
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $proofPath = $request->file('proof_image')->store('proof-of-delivery', 'public');
            }

            $delivery->markAsDelivered($proofPath);

            $order = $delivery->order;
            $orderUpdates = [
                'status' => 'delivered',
                'delivered_at' => now(),
            ];

            // Auto-mark COD as paid
            if ($order->is_cod && $order->payment_status === 'pending') {
                $orderUpdates['payment_status'] = 'paid';

                if ($order->latestPayment) {
                    $order->latestPayment->markAsSuccessful([
                        'collected_by_agent_id' => $agent->id,
                        'collected_at' => now()->toDateTimeString(),
                    ]);
                }
            }

            $order->update($orderUpdates);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'delivered',
                'note' => $validated['note'] ?? "Delivered by {$agent->name}" . ($order->is_cod ? ' (cash collected)' : ''),
                'changed_by_type' => 'delivery_agent',
                'changed_by_id' => $agent->id,
            ]);
         });

        try {
            app(\App\Services\OrderNotifier::class)->notifyCustomer($delivery->order->fresh(), 'delivered');
        } catch (\Throwable $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        return redirect()->route('agent.dashboard')
            ->with('success', 'Delivery complete. Great work!');
    }

    /**
     * Mark as failed delivery with reason.
     */
    public function fail(Request $request, Delivery $delivery): RedirectResponse
    {
        $agent = auth('agent')->user();
        abort_if($delivery->delivery_agent_id !== $agent->id, 403);

        $validated = $request->validate([
            'failure_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($delivery, $agent, $validated) {
            $delivery->markAsFailed($validated['failure_reason']);

            $delivery->order->update(['status' => 'failed_delivery']);

            OrderStatusHistory::create([
                'order_id' => $delivery->order_id,
                'status' => 'failed_delivery',
                'note' => "Failed delivery by {$agent->name}: " . $validated['failure_reason'],
                'changed_by_type' => 'delivery_agent',
                'changed_by_id' => $agent->id,
            ]);
        });

        return redirect()->route('agent.dashboard')
            ->with('success', 'Delivery marked as failed. Admin has been notified.');
    }
}