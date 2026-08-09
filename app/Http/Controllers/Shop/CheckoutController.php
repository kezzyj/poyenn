<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRate;
use App\Models\DeliveryZone;
use App\Models\Platform;
use App\Services\AddressService;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected AddressService $addressService
    ) {}

    public function index(): View|RedirectResponse
    {
        $customer = auth()->user();

        // Validate cart before showing checkout
        $validation = $this->cartService->validateForCheckout();

        if (!$validation['valid']) {
            return redirect()->route('shop.cart.index')
                ->with('error', $validation['message']);
        }

        $cart = $validation['cart'];

        // Show any validation issues as flash messages
        if (!empty($validation['issues'])) {
            session()->flash('warning', implode(' ', $validation['issues']));
        }

        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        $addresses = $this->addressService->getAddresses($customer);
        $defaultAddress = $this->addressService->getDefaultAddress($customer);

        $deliveryZones = DeliveryZone::where('platform_id', $platform->id)
            ->where('is_active', true)
            ->with(['activeRates'])
            ->orderBy('state')
            ->orderBy('name')
            ->get();

        return view('shop.checkout.index', compact(
            'cart',
            'addresses',
            'defaultAddress',
            'deliveryZones'
        ));
    }

    /**
     * Add a new address inline during checkout.
     */
    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['label'] = $validated['label'] ?? 'Home';
        $validated['is_default'] = $request->boolean('is_default');

        $this->addressService->create(auth()->user(), $validated);

        return redirect()->route('shop.checkout.index')
            ->with('success', 'Address added successfully.');
    }

    /**
     * AJAX endpoint to get delivery rates for a selected zone.
     */
    public function getDeliveryRates(DeliveryZone $deliveryZone): JsonResponse
    {
        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        if ($deliveryZone->platform_id !== $platform->id) {
            return response()->json(['rates' => []]);
        }

        $rates = $deliveryZone->activeRates()->orderBy('price')->get()->map(fn($rate) => [
            'id' => $rate->id,
            'name' => $rate->name,
            'price' => (float) $rate->price,
            'price_formatted' => '₦' . number_format($rate->price, 2),
            'estimated_days_label' => $rate->estimated_days_label,
            'description' => $rate->description,
        ]);

        return response()->json(['rates' => $rates]);
    }

    /**
     * Place the order.
     */
    public function placeOrder(Request $request, \App\Services\OrderService $orderService): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'delivery_rate_id' => 'required|exists:delivery_rates,id',
            'payment_method' => 'required|in:flutterwave,cash_on_delivery',
        ]);

        $customer = auth()->user();

        // Validate cart
        $cartValidation = $this->cartService->validateForCheckout();
        if (!$cartValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $cartValidation['message'],
                'redirect' => route('shop.cart.index'),
            ]);
        }

        $cart = $cartValidation['cart'];

        // Verify address belongs to this customer
        $address = \App\Models\Address::where('id', $validated['address_id'])
            ->where('customer_id', $customer->id)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Invalid address.']);
        }

        // Verify zone & rate belong to platform & match
        $zone = \App\Models\DeliveryZone::where('id', $validated['delivery_zone_id'])
            ->where('platform_id', $customer->platform_id)
            ->first();

        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Invalid delivery zone.']);
        }

        $rate = \App\Models\DeliveryRate::where('id', $validated['delivery_rate_id'])
            ->where('delivery_zone_id', $zone->id)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            return response()->json(['success' => false, 'message' => 'Invalid delivery rate.']);
        }

        // Create the order
        try {
            $order = $orderService->createOrder(
                $customer,
                $cart,
                $address,
                $zone,
                $rate,
                $validated['payment_method']
            );
        } catch (\Throwable $e) {
            \Log::error('Order creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Could not place order. Please try again.',
            ]);
        }

        // Route based on payment method
         // Route based on payment method
        if ($validated['payment_method'] === 'flutterwave') {
            $flutterwave = app(\App\Services\FlutterwaveService::class);

            $redirectUrl = route('shop.payment.callback');

            $result = $flutterwave->initializePayment($order, $redirectUrl);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment...',
                    'redirect' => $result['payment_link'],
                ]);
            }

            // Payment init failed — order exists but unpaid
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Could not start payment. Your order is saved — try paying again from My Orders.',
                'redirect' => route('shop.orders.show', $order),
            ]);
        }

        // Cash on Delivery — straight to confirmation
        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'redirect' => route('shop.orders.show', $order),
        ]);
    }
}