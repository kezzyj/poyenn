@extends('shop.layouts.app')

@section('title', 'Poyenn — Quality Electronics, Delivered Across Nigeria')
@section('description', 'Shop premium electronics from LG, Hisense, Scanfrost and more. Fast delivery, quality guaranteed, secure payments.')

@section('content')

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="text-indigo-200 text-sm uppercase tracking-wider mb-3">Quality Electronics</p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                        Premium Brands.<br>
                        <span class="text-yellow-300">Honest Prices.</span>
                    </h1>
                    <p class="text-lg text-indigo-100 mb-6">
                        Shop top-quality electronics from trusted brands. Fast delivery across Nigeria.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('shop.products.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-yellow-300 hover:text-indigo-900 transition">
                            Shop Now
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="#new-arrivals"
                           class="inline-flex items-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition">
                            New Arrivals
                        </a>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="relative">
                        <div class="absolute inset-0 bg-yellow-300 rounded-full blur-3xl opacity-20"></div>
                        <div class="relative grid grid-cols-2 gap-4">
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20">
                                <p class="text-3xl font-bold">100+</p>
                                <p class="text-sm text-indigo-100">Quality Products</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20 mt-8">
                                <p class="text-3xl font-bold">36</p>
                                <p class="text-sm text-indigo-100">States Covered</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20">
                                <p class="text-3xl font-bold">24/7</p>
                                <p class="text-sm text-indigo-100">Customer Support</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20 mt-8">
                                <p class="text-3xl font-bold">100%</p>
                                <p class="text-sm text-indigo-100">Genuine Products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust Bar --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Fast Delivery</p>
                        <p class="text-xs text-gray-500">Nationwide</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Secure Checkout</p>
                        <p class="text-xs text-gray-500">Flutterwave</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Cash on Delivery</p>
                        <p class="text-xs text-gray-500">Available</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Real Support</p>
                        <p class="text-xs text-gray-500">Always available</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Categories --}}
    @if($featuredCategories->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Shop By Category</h2>
                        <p class="text-sm text-gray-500 mt-1">Find what you need, fast</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($featuredCategories as $category)
                        <a href="{{ route('shop.categories.show', $category->slug) }}"
                           class="group bg-gray-50 hover:bg-indigo-50 rounded-lg p-4 text-center transition border border-gray-100 hover:border-indigo-200">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     loading="lazy"
                                     class="w-16 h-16 mx-auto rounded-lg object-cover mb-2">
                            @else
                                <div class="w-16 h-16 mx-auto bg-white rounded-lg flex items-center justify-center mb-2 text-2xl font-bold text-indigo-600 border border-gray-100">
                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                </div>
                            @endif
                            <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-700">{{ $category->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Featured Products --}}
    @if($featuredProducts->isNotEmpty())
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Featured Products</h2>
                        <p class="text-sm text-gray-500 mt-1">Hand-picked quality electronics</p>
                    </div>
                    <a href="{{ route('shop.products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($featuredProducts as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- On Sale --}}
    @if($onSale->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">🔥 On Sale</h2>
                        <p class="text-sm text-gray-500 mt-1">Limited time offers</p>
                    </div>
                    <a href="{{ route('shop.products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($onSale as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- New Arrivals --}}
    @if($newArrivals->isNotEmpty())
        <section id="new-arrivals" class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">New Arrivals</h2>
                        <p class="text-sm text-gray-500 mt-1">Latest additions to our catalogue</p>
                    </div>
                    <a href="{{ route('shop.products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($newArrivals as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA Banner --}}
    <section class="py-12 bg-gradient-to-r from-yellow-400 to-orange-500">
        <div class="max-w-7xl mx-auto px-4 text-center text-white">
            <h2 class="text-2xl md:text-3xl font-bold mb-3">Need Help Choosing?</h2>
            <p class="text-yellow-100 mb-6">Our team is always available to help you find the right product</p>
            <a href="tel:08012345678"
               class="inline-flex items-center px-6 py-3 bg-white text-orange-600 font-semibold rounded-lg hover:bg-orange-50 transition">
                Call 08012345678
            </a>
        </div>
    </section>

@endsection