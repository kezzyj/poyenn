@extends('shop.layouts.app')

@section('title', request('search') ? 'Search: ' . request('search') . ' — Poyenn' : 'All Products — Poyenn')
@section('description', 'Browse our full catalogue of quality electronics from trusted brands.')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Products</span>
        </nav>

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    All Products
                @endif
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ $products->total() }} products found</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- Sidebar Filters --}}
            <aside class="lg:col-span-1">
                <div x-data="{ filtersOpen: false }">

                    {{-- Mobile filter toggle --}}
                    <button @click="filtersOpen = !filtersOpen"
                            class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                        <span class="font-medium text-gray-900">Filters</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </button>

                    <div :class="{ 'hidden': !filtersOpen }" class="lg:!block space-y-4">

                        <form method="GET" action="{{ route('shop.products.index') }}" id="filter-form">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            {{-- Categories --}}
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <h3 class="font-semibold text-gray-900 mb-3 text-sm">Category</h3>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="category" value=""
                                               {{ !request('category') ? 'checked' : '' }}
                                               onchange="document.getElementById('filter-form').submit()"
                                               class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">All Categories</span>
                                    </label>
                                    @foreach($categories as $cat)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="category" value="{{ $cat->slug }}"
                                                   {{ request('category') === $cat->slug ? 'checked' : '' }}
                                                   onchange="document.getElementById('filter-form').submit()"
                                                   class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $cat->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Brands --}}
                            @if($brands->isNotEmpty())
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <h3 class="font-semibold text-gray-900 mb-3 text-sm">Brand</h3>
                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                        @foreach($brands as $brand)
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" name="brands[]" value="{{ $brand->slug }}"
                                                       {{ in_array($brand->slug, (array) request('brands', [])) ? 'checked' : '' }}
                                                       onchange="document.getElementById('filter-form').submit()"
                                                       class="text-indigo-600 focus:ring-indigo-500 rounded">
                                                <span class="ml-2 text-sm text-gray-700">{{ $brand->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Price Range --}}
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <h3 class="font-semibold text-gray-900 mb-3 text-sm">Price Range (₦)</h3>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                           placeholder="Min" min="0"
                                           class="px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                           placeholder="Max" min="0"
                                           class="px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                </div>
                                <button type="submit"
                                        class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded">
                                    Apply Price
                                </button>
                            </div>

                            {{-- In Stock --}}
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="in_stock" value="1"
                                           {{ request('in_stock') ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()"
                                           class="text-indigo-600 focus:ring-indigo-500 rounded">
                                    <span class="ml-2 text-sm font-medium text-gray-900">In stock only</span>
                                </label>
                            </div>

                            @if(request()->hasAny(['category', 'brands', 'min_price', 'max_price', 'in_stock']))
                                <a href="{{ route('shop.products.index') . (request('search') ? '?search=' . request('search') : '') }}"
                                   class="block text-center w-full px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded">
                                    Clear All Filters
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="lg:col-span-3">

                {{-- Sort Bar --}}
                <div class="bg-white rounded-lg border border-gray-200 p-3 mb-4 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-semibold">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $products->total() }}</span>
                    </p>

                    <form method="GET" class="flex items-center space-x-2">
                        {{-- Preserve other filters --}}
                        @foreach(request()->except('sort', 'page') as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <label class="text-sm text-gray-600 hidden sm:block">Sort:</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                            <option value="best_sellers" {{ request('sort') === 'best_sellers' ? 'selected' : '' }}>Best Sellers</option>
                        </select>
                    </form>
                </div>

                {{-- Products Grid --}}
                @if($products->isEmpty())
                    <div class="bg-white rounded-lg border border-gray-200 p-16 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">No products found</h3>
                        <p class="text-sm text-gray-500 mb-4">Try adjusting your filters or search query.</p>
                        <a href="{{ route('shop.products.index') }}"
                           class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded">
                            View All Products
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($products as $product)
                            @include('shop.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection