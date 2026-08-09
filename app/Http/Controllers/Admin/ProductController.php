<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $query = Product::where('platform_id', $platformId)
            ->with(['category', 'brand', 'images' => fn($q) => $q->where('is_primary', true)]);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock
        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('manage_stock', true);
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', 0)
                    ->where('manage_stock', true);
            } elseif ($request->stock === 'in') {
                $query->where(function ($q) {
                    $q->where('manage_stock', false)
                        ->orWhere('stock_quantity', '>', 0);
                });
            }
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        // For filter dropdowns
        $categories = Category::where('platform_id', $platformId)->active()->orderBy('name')->get();
        $brands = Brand::where('platform_id', $platformId)->active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create(): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $categories = Category::where('platform_id', $platformId)->active()->orderBy('name')->get();
        $brands = Brand::where('platform_id', $platformId)->active()->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required_with:attributes.*.value|string|max:100',
            'attributes.*.value' => 'required_with:attributes.*.name|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $platformId) {
            // Prepare product data
            $productData = collect($validated)->except(['images', 'attributes'])->toArray();
            $productData['platform_id'] = $platformId;
            $productData['slug'] = $this->generateUniqueSlug($validated['name'], $platformId);
            $productData['is_active'] = $request->boolean('is_active', true);
            $productData['is_featured'] = $request->boolean('is_featured');
            $productData['manage_stock'] = $request->boolean('manage_stock', true);

            $product = Product::create($productData);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            // Handle attributes
            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $index => $attr) {
                    if (!empty($attr['name']) && !empty($attr['value'])) {
                        $product->attributes()->create([
                            'name' => $attr['name'],
                            'value' => $attr['value'],
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($product->platform_id !== $platformId, 403);

        $product->load(['images' => fn($q) => $q->orderBy('sort_order'), 'attributes' => fn($q) => $q->orderBy('sort_order')]);

        $categories = Category::where('platform_id', $platformId)->active()->orderBy('name')->get();
        $brands = Brand::where('platform_id', $platformId)->active()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($product->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
            'primary_image_id' => 'nullable|exists:product_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required_with:attributes.*.value|string|max:100',
            'attributes.*.value' => 'required_with:attributes.*.name|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $product, $platformId) {
            // Update product
            $productData = collect($validated)->except(['images', 'attributes', 'primary_image_id', 'delete_images'])->toArray();

            if ($validated['name'] !== $product->name) {
                $productData['slug'] = $this->generateUniqueSlug($validated['name'], $platformId, $product->id);
            }

            $productData['is_active'] = $request->boolean('is_active');
            $productData['is_featured'] = $request->boolean('is_featured');
            $productData['manage_stock'] = $request->boolean('manage_stock', true);

            $product->update($productData);

            // Delete selected images
            if (!empty($validated['delete_images'])) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $validated['delete_images'])
                    ->get();

                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                $maxSortOrder = $product->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $maxSortOrder + 1 + $index,
                        'is_primary' => false,
                    ]);
                }
            }

            // Handle primary image
            if (!empty($validated['primary_image_id'])) {
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductImage::where('id', $validated['primary_image_id'])
                    ->where('product_id', $product->id)
                    ->update(['is_primary' => true]);
            } else {
                // Make sure at least one image is primary
                if ($product->images()->where('is_primary', true)->doesntExist() && $product->images()->exists()) {
                    $product->images()->orderBy('sort_order')->first()->update(['is_primary' => true]);
                }
            }

            // Replace attributes
            $product->attributes()->delete();
            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $index => $attr) {
                    if (!empty($attr['name']) && !empty($attr['value'])) {
                        $product->attributes()->create([
                            'name' => $attr['name'],
                            'value' => $attr['value'],
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($product->platform_id !== $platformId, 403);

        // Delete all images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($product->platform_id !== $platformId, 403);

        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', 'Product status updated.');
    }

    private function generateUniqueSlug(string $name, int $platformId, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('platform_id', $platformId)
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}