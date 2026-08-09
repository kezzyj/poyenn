<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get or create the cart for the logged-in customer.
     */
    public function getOrCreateCart(): ?Cart
    {
        $customer = Auth::guard('web')->user();

        if (!$customer) {
            return null;
        }

        return Cart::firstOrCreate(
            ['customer_id' => $customer->id],
            ['platform_id' => $customer->platform_id]
        );
    }

    /**
     * Get the current cart for the logged-in customer.
     */
    public function getCart(): ?Cart
    {
        $customer = Auth::guard('web')->user();

        if (!$customer) {
            return null;
        }

        return Cart::where('customer_id', $customer->id)
            ->with(['items.product.images' => fn($q) => $q->where('is_primary', true)])
            ->first();
    }

    /**
     * Add a product to the cart.
     */
    public function addItem(Product $product, int $quantity = 1): array
    {
        $cart = $this->getOrCreateCart();

        if (!$cart) {
            return ['success' => false, 'message' => 'You must be logged in to add to cart.'];
        }

        // Make sure product is from same platform as customer
        if ($product->platform_id !== $cart->platform_id) {
            return ['success' => false, 'message' => 'This product is not available.'];
        }

        if (!$product->is_active) {
            return ['success' => false, 'message' => 'This product is no longer available.'];
        }

        if (!$product->is_in_stock) {
            return ['success' => false, 'message' => 'This product is out of stock.'];
        }

        // Check if item already in cart — increment quantity
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            // Check stock
            if ($product->manage_stock && $newQuantity > $product->stock_quantity) {
                return [
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} available. You already have {$existingItem->quantity} in cart."
                ];
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price, // Refresh price in case it changed
            ]);
        } else {
            // Check stock for new item
            if ($product->manage_stock && $quantity > $product->stock_quantity) {
                return [
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} available."
                ];
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Added to cart!',
            'cart_count' => $this->getCartCount(),
        ];
    }

    /**
     * Update quantity for a cart item.
     */
    public function updateQuantity(CartItem $cartItem, int $quantity): array
    {
        if ($quantity < 1) {
            return $this->removeItem($cartItem);
        }

        $product = $cartItem->product;

        if (!$product || !$product->is_active) {
            $cartItem->delete();
            return ['success' => false, 'message' => 'Product no longer available — removed from cart.'];
        }

        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            return [
                'success' => false,
                'message' => "Only {$product->stock_quantity} available."
            ];
        }

        $cartItem->update([
            'quantity' => $quantity,
            'price' => $product->price,
        ]);

        return ['success' => true, 'message' => 'Cart updated.'];
    }

    /**
     * Remove an item from cart.
     */
    public function removeItem(CartItem $cartItem): array
    {
        $cartItem->delete();
        return ['success' => true, 'message' => 'Item removed from cart.'];
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->clear();
        }
    }

    /**
     * Get current cart count for header badge.
     */
    public function getCartCount(): int
    {
        $customer = Auth::guard('web')->user();

        if (!$customer) {
            return 0;
        }

        $cart = Cart::where('customer_id', $customer->id)->first();

        return $cart ? (int) $cart->items->sum('quantity') : 0;
    }

    /**
     * Validate cart before checkout — remove invalid items, refresh prices.
     */
    public function validateForCheckout(): array
    {
        $cart = $this->getCart();
        $issues = [];

        if (!$cart || $cart->items->isEmpty()) {
            return ['valid' => false, 'message' => 'Your cart is empty.', 'issues' => []];
        }

        foreach ($cart->items as $item) {
            if (!$item->product || !$item->product->is_active) {
                $issues[] = "{$item->product?->name} is no longer available and was removed.";
                $item->delete();
                continue;
            }

            // Refresh price
            if ($item->price != $item->product->price) {
                $issues[] = "Price for {$item->product->name} was updated.";
                $item->update(['price' => $item->product->price]);
            }

            // Check stock
            if ($item->product->manage_stock && $item->quantity > $item->product->stock_quantity) {
                if ($item->product->stock_quantity > 0) {
                    $issues[] = "{$item->product->name} quantity was reduced to {$item->product->stock_quantity} (max available).";
                    $item->update(['quantity' => $item->product->stock_quantity]);
                } else {
                    $issues[] = "{$item->product->name} is out of stock and was removed.";
                    $item->delete();
                }
            }
        }

        // Reload cart
        $cart->refresh();

        if ($cart->items->isEmpty()) {
            return ['valid' => false, 'message' => 'Your cart is empty after validation.', 'issues' => $issues];
        }

        return ['valid' => true, 'issues' => $issues, 'cart' => $cart];
    }
}