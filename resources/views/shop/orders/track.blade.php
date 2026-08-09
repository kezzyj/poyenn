@extends('shop.layouts.app')

@section('title', 'Track Your Order — Poyenn')

@section('content')

    <div class="max-w-3xl mx-auto px-4 py-8">

        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Track Order</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Track Your Order</h1>

        {{-- Lookup Form --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <p class="text-sm text-gray-600 mb-4">Enter your order number and the phone number you used at checkout to view your order status.</p>

            <form method="GET" action="{{ route('shop.orders.track') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Order Number</label>
                    <input type="text" name="order_number" value="{{ request('order_number') }}" required
                           placeholder="e.g. POY-2026-ABC123"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="{{ request('phone') }}" required
                           placeholder="e.g. 08012345678"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg">
                        Track Order
                    </button>
                </div>
            </form>
        </div>

        @if($errors->any() || ($error ?? null))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6 text-sm">
                {{ $error ?? $errors->first() }}
            </div>
        @endif

        {{-- Order Found --}}
        @if(isset($order) && $order)

            {{-- Status Banner --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Order Number</p>
                        <h2 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h2>
                        <p class="text-xs text-gray-500 mt-1">Placed {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-2 text-sm font-semibold rounded-lg bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                    $statusSteps = ['pending', 'confirmed', 'packed', 'out_for_delivery', 'delivered'];
                    $currentIndex = array_search($order->status, $statusSteps);
                    if ($currentIndex === false) $currentIndex = -1;
                @endphp

                @if(!in_array($order->status, ['cancelled', 'failed_delivery']))
                    <div class="flex items-center justify-between mt-6">
                        @foreach($statusSteps as $i => $step)
                            <div class="flex-1 flex items-center {{ $i === count($statusSteps) - 1 ? '' : 'after:flex-1 after:h-1 after:bg-gray-200 after:ml-2 after:mr-2' }}
                                        {{ $i <= $currentIndex && $i < count($statusSteps) - 1 ? 'after:!bg-green-500' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                                                {{ $i <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        @if($i < $currentIndex)
                                            ✓
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-xs mt-1 text-center text-gray-600 hidden sm:block">
                                        {{ ucwords(str_replace('_', ' ', $step)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Delivery Info --}}
            @if($order->delivery?->agent && in_array($order->status, ['out_for_delivery']))
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">
                            {{ strtoupper(substr($order->delivery->agent->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-indigo-900">{{ $order->delivery->agent->name }}</p>
                            <p class="text-xs text-indigo-700">Your delivery agent</p>
                        </div>
                        <a href="tel:{{ $order->delivery->agent->phone }}"
                           class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded">
                            Call
                        </a>
                    </div>
                </div>
            @endif

            {{-- Items --}}
            <div class="bg-white rounded-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="px-6 py-3 flex items-center space-x-3">
                            @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}" class="w-12 h-12 rounded object-cover">
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ₦{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">₦{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-right">
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-lg font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</p>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Order Updates</h3>
                <div class="space-y-3">
                    @foreach($order->statusHistory as $event)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</p>
                                @if($event->note)
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $event->note }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Delivery Address --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">Delivery To</h3>
                <p class="text-sm font-medium text-gray-900">{{ $order->delivery_recipient_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->delivery_phone }}</p>
                <p class="text-sm text-gray-600">
                    {{ $order->delivery_address_line_1 }}{{ $order->delivery_address_line_2 ? ', ' . $order->delivery_address_line_2 : '' }},
                    {{ $order->delivery_city }}, {{ $order->delivery_state }}
                </p>
            </div>

        @endif

    </div>

@endsection