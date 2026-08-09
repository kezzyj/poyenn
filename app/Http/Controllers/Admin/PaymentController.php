<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $query = Payment::where('platform_id', $platformId)
            ->with(['order.customer']);

        // Search by order number or Flutterwave reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('flutterwave_ref', 'like', "%{$search}%")
                  ->orWhere('flutterwave_tx_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total_collected' => Payment::where('platform_id', $platformId)
                ->where('status', 'successful')
                ->sum('amount'),
            'pending' => Payment::where('platform_id', $platformId)
                ->where('status', 'pending')
                ->sum('amount'),
            'failed_count' => Payment::where('platform_id', $platformId)
                ->where('status', 'failed')
                ->count(),
            'successful_count' => Payment::where('platform_id', $platformId)
                ->where('status', 'successful')
                ->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($payment->platform_id !== $platformId, 403);

        $payment->load(['order.customer', 'order.items']);

        return view('admin.payments.show', compact('payment'));
    }
}