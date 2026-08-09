@extends('shop.layouts.app')

@section('title', 'Your Cart — Poyenn')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Your Cart</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Your Cart</h1>

        @if(!$cart || $cart->items->isEmpty())
            {{-- Empty Cart --}}
            <div class="bg-white rounded-lg border border-gray-200 p-16 text-center">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-1">Your cart is empty</h2>
                <p class="text-sm text-gray-500 mb-6">Add some products to get started.</p>
                <a href="{{ route('shop.products.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-3">
                    @foreach($cart->items as $item)
                        <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-start space-x-4">

                            {{-- Image --}}
                            <a href="{{ route('shop.products.show', $item->product->slug) }}" class="flex-shrink-0">
                                @if($item->product->images->first())
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg object-cover">
                                @else
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            {{-- Info & Controls --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('shop.products.show', $item->product->slug) }}"
                                           class="text-sm sm:text-base font-medium text-gray-900 hover:text-indigo-600 line-clamp-2">
                                            {{ $item->product->name }}
                                        </a>
                                        @if($item->product->brand)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->product->brand->name }}</p>
                                        @endif
                                        <p class="text-sm font-semibold text-gray-900 mt-1">₦{{ number_format($item->price, 2) }}</p>
                                    </div>

                                    {{-- Remove --}}
                                    <form method="POST" action="{{ route('shop.cart.remove', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a2 2 0 012-2h2a2 2 0 012 2v3"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Quantity + Subtotal --}}
                                <div class="flex items-center justify-between mt-3">
                                    <form method="POST" action="{{ route('shop.cart.update', $item) }}" class="flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                                class="w-8 h-8 border border-gray-300 rounded text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">−</button>
                                        <span class="px-3 text-sm font-semibold">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                                class="w-8 h-8 border border-gray-300 rounded text-gray-600 hover:bg-gray-50">+</button>
                                    </form>

                                    <p class="text-sm font-bold text-gray-900">
                                        ₦{{ number_format($item->subtotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Cart Actions --}}
                    <div class="flex items-center justify-between pt-3">
                        <a href="{{ route('shop.products.index') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            ← Continue shopping
                        </a>

                        <form method="POST" action="{{ route('shop.cart.clear') }}"
                              onsubmit="return confirm('Remove all items from cart?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Clear cart
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Summary Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 lg:sticky lg:top-24">
                        <h2 class="font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Items ({{ $cart->items_count }})</span>
                                <span class="text-gray-900 font-medium">₦{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Delivery</span>
                                <span class="text-gray-500 text-xs">Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-900 font-semibold">Subtotal</span>
                                <span class="text-xl font-bold text-gray-900">₦{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Delivery fee added at checkout</p>
                        </div>

                        <a href="{{ route('shop.checkout.index') }}"
                           class="block w-full mt-6 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold rounded-lg transition">
                            Proceed to Checkout
                        </a>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            Secure checkout with Flutterwave
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection