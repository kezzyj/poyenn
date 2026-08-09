<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index(): View
    {
        $cart = $this->cartService->getCart();
        return view('shop.cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $result = $this->cartService->addItem($product, $validated['quantity']);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse|RedirectResponse
    {
        // Make sure this item belongs to the logged-in customer
        abort_if($cartItem->cart->customer_id !== auth()->id(), 403);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $result = $this->cartService->updateQuantity($cartItem, $validated['quantity']);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(Request $request, CartItem $cartItem): JsonResponse|RedirectResponse
    {
        abort_if($cartItem->cart->customer_id !== auth()->id(), 403);

        $result = $this->cartService->removeItem($cartItem);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();
        return back()->with('success', 'Cart cleared.');
    }
}