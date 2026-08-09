@extends('shop.layouts.app')

@section('title', 'My Orders — Poyenn')

@section('content')

    <div class="max-w-4xl mx-auto px-4 py-8">

        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">My Orders</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">My Orders</h1>

        @if($orders->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No orders yet</h3>
                <p class="text-sm text-gray-500 mb-6">Your order history will appear here.</p>
                <a href="{{ route('shop.products.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($orders as $order)
                    <a href="{{ route('shop.orders.show', $order) }}"
                       class="block bg-white rounded-lg border border-gray-200 p-4 hover:border-indigo-300 transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-bold text-gray-900">{{ $order->order_number }}</p>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 text-xs rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Placed {{ $order->created_at->diffForHumans() }}</p>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700">{{ $order->items->count() }} item(s)</p>
                            <p class="text-base font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

@endsection