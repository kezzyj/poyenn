@extends('shop.layouts.app')

@section('title', 'Order ' . $order->order_number . ' — Poyenn')

@section('content')

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Success Banner --}}
        @if($order->is_pending && $order->payment_status === 'pending' && $order->is_cod)
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Order placed successfully!</h2>
                        <p class="text-sm text-gray-600">Pay with cash when your order is delivered.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Order Header --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Order Number</p>
                    <h1 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                        {{ $order->status_label }}
                    </span>
                    <span class="px-3 py-1 text-xs font-semibold rounded {{ $order->is_paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        Payment {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>

            <div class="text-sm text-gray-600">
                <p>Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                <p>Payment method: <span class="font-medium text-gray-900">{{ $order->latestPayment->method_label ?? ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span></p>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Order Items</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="px-6 py-4 flex items-center space-x-4">
                        @if($item->product_image)
                            <img src="{{ asset('storage/' . $item->product_image) }}"
                                 class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg"></div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                            @if($item->product_sku)
                                <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ $item->quantity }} × ₦{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <p class="text-sm font-bold text-gray-900">₦{{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Two Column Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- Delivery Address --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Delivery Address</h3>
                <div class="text-sm text-gray-700 space-y-0.5">
                    <p class="font-medium">{{ $order->delivery_recipient_name }}</p>
                    <p>{{ $order->delivery_phone }}</p>
                    <p>{{ $order->delivery_address_line_1 }}</p>
                    @if($order->delivery_address_line_2)
                        <p>{{ $order->delivery_address_line_2 }}</p>
                    @endif
                    <p>{{ $order->delivery_city }}, {{ $order->delivery_state }}</p>
                    @if($order->delivery_landmark)
                        <p class="text-xs text-gray-500 pt-1">Landmark: {{ $order->delivery_landmark }}</p>
                    @endif
                </div>
            </div>

            {{-- Order Totals --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">₦{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery</span>
                        <span class="font-medium">₦{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-700">
                            <span>Discount</span>
                            <span>− ₦{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-100 pt-2 flex justify-between text-base font-bold">
                        <span>Total</span>
                        <span>₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Status Timeline --}}
        @if($order->statusHistory->isNotEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Order Tracking</h3>
                <div class="space-y-3">
                    @foreach($order->statusHistory as $event)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $event->status_label }}</p>
                                @if($event->note)
                                    <p class="text-xs text-gray-500">{{ $event->note }}</p>
                                @endif
                                <p class="text-xs text-gray-400">{{ $event->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions --}}

        {{-- Pay Now (for unpaid Flutterwave orders) --}}
        @if($order->payment_method === 'flutterwave' && $order->payment_status !== 'paid' && $order->status !== 'cancelled')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-yellow-900">Payment Pending</p>
                        <p class="text-xs text-yellow-700">Complete your payment to confirm this order.</p>
                    </div>
                    <form method="POST" action="{{ route('shop.payment.retry', $order) }}">
                        @csrf
                        <button type="submit"
                                class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg whitespace-nowrap">
                            Pay ₦{{ number_format($order->total_amount, 2) }} Now
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('shop.products.index') }}"
               class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold rounded-lg">
                Continue Shopping
            </a>
            <a href="{{ route('shop.orders.index') }}"
               class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 text-center font-semibold rounded-lg">
                View My Orders
            </a>
        </div>

    </div>

@endsection