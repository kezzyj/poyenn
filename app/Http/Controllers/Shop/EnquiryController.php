<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        Enquiry::create([
            'platform_id' => $platform->id,
            'product_id' => $product->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'location' => $validated['location'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        return back()->with('success', 'Your enquiry has been received. We\'ll get back to you shortly.');
    }
}