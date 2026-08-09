<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        // Featured categories
        $featuredCategories = Category::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Featured products
        $featuredProducts = Product::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->latest()
            ->limit(8)
            ->get();

        // New arrivals
        $newArrivals = Product::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->latest()
            ->limit(8)
            ->get();

        // On sale (where compare_price > price)
        $onSale = Product::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->limit(8)
            ->get();

        return view('shop.pages.home', compact('featuredCategories', 'featuredProducts', 'newArrivals', 'onSale'));
    }
}