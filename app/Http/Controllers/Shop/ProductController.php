<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        $query = Product::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->with(['images' => fn($q) => $q->where('is_primary', true), 'brand']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $category = Category::where('platform_id', $platform->id)
                ->where('slug', $request->category)
                ->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by brand (can be multiple)
        if ($request->filled('brands')) {
            $brandSlugs = is_array($request->brands) ? $request->brands : [$request->brands];
            $brandIds = Brand::where('platform_id', $platform->id)
                ->whereIn('slug', $brandSlugs)
                ->pluck('id');
            $query->whereIn('brand_id', $brandIds);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // In stock only
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'best_sellers':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        // For filter sidebar
        $categories = Category::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $brands = Brand::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('shop.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product): View
    {
        $platform = Platform::where('slug', 'poyenn')->firstOrFail();
        abort_if($product->platform_id !== $platform->id || !$product->is_active, 404);

        $product->load(['images' => fn($q) => $q->orderBy('sort_order'), 'attributes' => fn($q) => $q->orderBy('sort_order'), 'category', 'brand']);

        // Track views
        $product->incrementViews();

        // Related products from same category
        $relatedProducts = Product::where('platform_id', $platform->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->limit(4)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts'));
    }
}