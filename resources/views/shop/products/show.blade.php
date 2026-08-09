@extends('shop.layouts.app')

@section('title', $product->meta_title ?: $product->name . ' — Poyenn')
@section('description', $product->meta_description ?: $product->short_description ?: 'Buy ' . $product->name . ' on Poyenn. Quality electronics delivered.')

@push('head')
    {{-- Product Schema for SEO --}}
    <script type="application/ld+json">
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : '',
            'description' => strip_tags($product->short_description ?: $product->description ?: ''),
            'offers' => [
                '@type' => 'Offer',
                'url' => url()->current(),
                'priceCurrency' => 'NGN',
                'price' => (string) $product->price,
                'availability' => $product->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ];

        if ($product->brand) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => $product->brand->name];
        }
    @endphp
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.products.index') }}" class="hover:text-indigo-600">Products</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.categories.show', $product->category->slug) }}" class="hover:text-indigo-600">{{ $product->category->name }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 truncate">{{ $product->name }}</span>
        </nav>

        {{-- Main Product Section --}}
            <div x-data="productDetail({{ $product->images->count() }})"
             x-init="initThumbs()"
             @resize.window="updateScrollState()"
             class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">

                {{-- Image Gallery --}}
                <div>
                    {{-- Main image --}}
                    <div class="aspect-square bg-white rounded-lg overflow-hidden mb-3 border border-gray-100 flex items-center justify-center">
                        @if($product->images->isNotEmpty())
                            @foreach($product->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->alt_text ?: $product->name }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     x-show="activeImage === {{ $index }}"
                                     x-cloak
                                     class="max-w-full max-h-full object-contain"
                                     style="image-rendering: auto;">
                            @endforeach
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Thumbnail carousel --}}
                    @if($product->images->count() > 1)
                        <div class="relative">
                            {{-- Left arrow --}}
                              <button type="button"
                                    x-show="canScrollLeft"
                                    x-cloak
                                    @click="scrollThumbs('left')"
                                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white border border-gray-300 rounded-full shadow flex items-center justify-center hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            {{-- Scrollable thumbnail track --}}
                            <div x-ref="thumbTrack"
                                 @scroll="updateScrollState()"   
                                 class="flex gap-2 overflow-x-auto scroll-smooth px-10"
                                 style="scrollbar-width: none; -ms-overflow-style: none;">
                                @foreach($product->images as $index => $image)
                                    <button type="button"
                                            @click="activeImage = {{ $index }}"
                                            :class="activeImage === {{ $index }} ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-gray-200'"
                                            class="flex-shrink-0 w-16 h-16 rounded border-2 overflow-hidden bg-white">
                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                             alt="{{ $image->alt_text ?: $product->name }}"
                                             loading="lazy"
                                             class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>

                            {{-- Right arrow --}}
                              <button type="button"
                                    x-show="canScrollRight"
                                    x-cloak
                                    @click="scrollThumbs('right')"
                                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white border border-gray-300 rounded-full shadow flex items-center justify-center hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div>
                    @if($product->brand)
                        <p class="text-sm text-indigo-600 font-medium mb-2">{{ $product->brand->name }}</p>
                    @endif

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>

                    @if($product->sku)
                        <p class="text-xs text-gray-500 mb-4">SKU: {{ $product->sku }}</p>
                    @endif

                    {{-- Price --}}
                    <div class="flex items-baseline space-x-3 mb-4">
                        <p class="text-3xl md:text-4xl font-bold text-gray-900">₦{{ number_format($product->price, 2) }}</p>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <p class="text-lg text-gray-400 line-through">₦{{ number_format($product->compare_price, 2) }}</p>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded">
                                Save {{ $product->discount_percentage }}%
                            </span>
                        @endif
                    </div>

                    {{-- Stock Status --}}
                    <div class="mb-4">
                        @if($product->is_in_stock)
                            <div class="inline-flex items-center text-green-700 text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                In Stock
                                @if($product->manage_stock && $product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="ml-2 text-orange-600">Only {{ $product->stock_quantity }} left!</span>
                                @endif
                            </div>
                        @else
                            <div class="inline-flex items-center text-red-700 text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                </svg>
                                Out of Stock
                            </div>
                        @endif
                    </div>

                    @if($product->short_description)
                        <p class="text-gray-700 mb-6">{{ $product->short_description }}</p>
                    @endif

                    {{-- Add to Cart --}}
                    @if($product->is_in_stock)
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center space-x-3">
                                <label class="text-sm font-medium text-gray-700">Quantity:</label>
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button type="button" @click="quantity > 1 ? quantity-- : null"
                                            class="px-3 py-2 text-gray-600 hover:bg-gray-100">−</button>
                                    <input type="number" x-model="quantity" min="1" max="{{ $product->stock_quantity }}"
                                           class="w-16 text-center border-0 focus:ring-0">
                                    <button type="button" @click="quantity++"
                                            class="px-3 py-2 text-gray-600 hover:bg-gray-100">+</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button type="button" @click="addToCart()"
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/>
                                    </svg>
                                    Add to Cart
                                </button>

                                <button type="button" @click="buyNow()"
                                        class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg transition">
                                    Buy Now
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-4 mb-6 text-center text-gray-600">
                            This product is currently out of stock
                        </div>
                    @endif

                    {{-- Trust Badges --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Genuine product with manufacturer warranty
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Fast nationwide delivery
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Pay online or on delivery
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs (Description / Specs) --}}
            <div x-data="{ tab: 'description' }" class="border-t border-gray-100">
                <div class="flex border-b border-gray-100">
                    <button @click="tab = 'description'"
                            :class="tab === 'description' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900'"
                            class="px-6 py-3 border-b-2 font-medium text-sm transition">
                        Description
                    </button>
                    @if($product->attributes->isNotEmpty())
                        <button @click="tab = 'specs'"
                                :class="tab === 'specs' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="px-6 py-3 border-b-2 font-medium text-sm transition">
                            Specifications
                        </button>
                    @endif
                </div>

                <div class="p-6">
                    <div x-show="tab === 'description'" x-cloak>
                        @if($product->description)
                            <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $product->description }}</div>
                        @else
                            <p class="text-gray-500 text-sm">No description provided.</p>
                        @endif
                    </div>

                    @if($product->attributes->isNotEmpty())
                        <div x-show="tab === 'specs'" x-cloak>
                            <table class="w-full max-w-2xl">
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($product->attributes as $attr)
                                        <tr>
                                            <td class="py-2 text-sm font-medium text-gray-600 w-1/3">{{ $attr->name }}</td>
                                            <td class="py-2 text-sm text-gray-900">{{ $attr->value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">You May Also Like</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $related)
                        @include('shop.partials.product-card', ['product' => $related])
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @push('scripts')
        <script>
            function productDetail(imageCount) {
                return {
                    activeImage: 0,
                    quantity: 1,
                    totalImages: imageCount,
                    isAdding: false,

                    async addToCart(redirectToCart = false) {
                        if (this.isAdding) return;
                        this.isAdding = true;

                        try {
                            const response = await fetch('{{ route('shop.cart.add', $product) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({ quantity: this.quantity }),
                            });

                            // If user not logged in, redirect to login
                            if (response.status === 401 || response.redirected) {
                                window.location.href = '{{ route('login') }}?redirect={{ url()->current() }}';
                                return;
                            }

                            const data = await response.json();

                            if (data.success) {
                                // Update cart badge in header
                                const badge = document.querySelector('header a[href="{{ route('shop.cart.index') }}"] span');
                                if (badge) {
                                    badge.textContent = data.cart_count;
                                } else {
                                    // Re-render the page so badge appears for first item
                                    window.location.reload();
                                }

                                if (redirectToCart) {
                                    window.location.href = '{{ route('shop.cart.index') }}';
                                } else {
                                    this.showToast(data.message, 'success');
                                }
                            } else {
                                this.showToast(data.message, 'error');
                            }
                        } catch (err) {
                            this.showToast('Something went wrong. Please try again.', 'error');
                        } finally {
                            this.isAdding = false;
                        }
                    },

                    buyNow() {
                        this.addToCart(true);
                    },

                     canScrollLeft: false,
                    canScrollRight: false,

                    initThumbs() {
                        this.$nextTick(() => {
                            this.updateScrollState();
                        });
                    },

                    updateScrollState() {
                        const track = this.$refs.thumbTrack;
                        if (!track) {
                            this.canScrollLeft = false;
                            this.canScrollRight = false;
                            return;
                        }
                        // Can scroll left if we've scrolled away from the start
                        this.canScrollLeft = track.scrollLeft > 5;
                        // Can scroll right if there's more content beyond the visible area
                        this.canScrollRight = (track.scrollLeft + track.clientWidth) < (track.scrollWidth - 5);
                    },

                    scrollThumbs(direction) {
                        const track = this.$refs.thumbTrack;
                        if (!track) return;
                        const amount = 160;
                        track.scrollBy({
                            left: direction === 'left' ? -amount : amount,
                            behavior: 'smooth'
                        });
                    },

                    showToast(message, type) {
                        const toast = document.createElement('div');
                        const colors = type === 'success' ? 'bg-green-600' : 'bg-red-600';
                        toast.className = `fixed bottom-4 right-4 ${colors} text-white px-4 py-3 rounded-lg shadow-lg z-50 text-sm font-medium`;
                        toast.textContent = message;
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);
                    }
                }
            }
        </script>
    @endpush

@endsection