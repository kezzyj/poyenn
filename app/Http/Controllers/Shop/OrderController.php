<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        // Security — only the order owner can view it
        abort_if($order->customer_id !== auth()->id(), 403);

        $order->load(['items', 'statusHistory', 'deliveryZone', 'deliveryRate', 'latestPayment']);

        return view('shop.orders.show', compact('order'));
    }

    public function index(): View
    {
        $orders = Order::where('customer_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    /**
     * Public tracking page — no login required, but must know order number AND phone.
     */
    public function track(\Illuminate\Http\Request $request)
    {
        $orderNumber = $request->input('order_number');
        $phone = $request->input('phone');

        // Show empty tracking form
        if (!$orderNumber || !$phone) {
            return view('shop.orders.track', ['order' => null]);
        }

        // Look up the order — must match BOTH order number and delivery phone
        $order = \App\Models\Order::where('order_number', $orderNumber)
            ->where('delivery_phone', $phone)
            ->with([
                'items',
                'statusHistory' => fn($q) => $q->orderBy('created_at'),
                'deliveryZone',
                'deliveryRate',
                'delivery.agent',
            ])
            ->first();

        if (!$order) {
            return view('shop.orders.track', [
                'order' => null,
                'error' => 'No order found with that order number and phone combination.',
            ]);
        }

        return view('shop.orders.track', compact('order'));
    }
}