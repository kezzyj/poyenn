@extends('shop.layouts.app')

@section('title', $category->meta_title ?: $category->name . ' — Poyenn')
@section('description', $category->meta_description ?: 'Shop ' . $category->name . ' on Poyenn. Quality products, fast delivery.')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.products.index') }}" class="hover:text-indigo-600">Products</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $category->name }}</span>
        </nav>

        {{-- Category Header --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center space-x-4">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}"
                         class="w-20 h-20 rounded-lg object-cover">
                @endif
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-gray-600 mt-1">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Redirect to products with category filter --}}
        <div class="text-center">
            <a href="{{ route('shop.products.index', ['category' => $category->slug]) }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                Browse all {{ $category->name }} products
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

@endsection