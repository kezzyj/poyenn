<a href="{{ route('shop.products.show', $product->slug) }}"
   class="group bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">

    <div class="relative aspect-square bg-gray-50 overflow-hidden">
        @if($product->images->first())
            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        @if($product->compare_price && $product->compare_price > $product->price)
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                -{{ $product->discount_percentage }}%
            </span>
        @endif

        @if(!$product->is_in_stock)
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <span class="bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded">Out of Stock</span>
            </div>
        @endif
    </div>

    <div class="p-3">
        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2 min-h-[40px] group-hover:text-indigo-600">
            {{ $product->name }}
        </h3>

        <div class="flex items-center space-x-2">
            <p class="text-base font-bold text-gray-900">₦{{ number_format($product->price, 0) }}</p>
            @if($product->compare_price && $product->compare_price > $product->price)
                <p class="text-xs text-gray-400 line-through">₦{{ number_format($product->compare_price, 0) }}</p>
            @endif
        </div>
    </div>
</a>