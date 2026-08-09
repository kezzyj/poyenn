<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryRate;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a complete order from a cart + checkout data.
     */
    public function createOrder(
        Customer $customer,
        Cart $cart,
        Address $address,
        DeliveryZone $zone,
        DeliveryRate $rate,
        string $paymentMethod
    ): Order {
                $order = DB::transaction(function () use ($customer, $cart, $address, $zone, $rate, $paymentMethod) {
            // Calculate totals
            $subtotal = (float) $cart->subtotal;
            $deliveryFee = (float) $rate->price;
            $discountAmount = 0;
            $totalAmount = $subtotal + $deliveryFee - $discountAmount;

            // Create the order
            $order = Order::create([
                'platform_id' => $customer->platform_id,
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'delivery_zone_id' => $zone->id,
                'delivery_rate_id' => $rate->id,
                'delivery_recipient_name' => $address->recipient_name,
                'delivery_phone' => $address->phone,
                'delivery_address_line_1' => $address->address_line_1,
                'delivery_address_line_2' => $address->address_line_2,
                'delivery_city' => $address->city,
                'delivery_state' => $address->state,
                'delivery_landmark' => $address->landmark,
            ]);

            // Snapshot cart items as order items
            foreach ($cart->items as $item) {
                $product = $item->product;

                $primaryImage = $product->images->where('is_primary', true)->first()
                    ?? $product->images->first();

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_image' => $primaryImage?->image_path,
                    'unit_price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                // Decrement product stock and increment sales count
                $product->decrementStock($item->quantity);
            }

            // Create payment record (pending)
            Payment::create([
                'platform_id' => $customer->platform_id,
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $totalAmount,
                'currency' => 'NGN',
                'status' => 'pending',
            ]);

            // Create delivery record (unassigned yet — admin will assign agent later)
            Delivery::create([
                'platform_id' => $customer->platform_id,
                'order_id' => $order->id,
                'delivery_agent_id' => null,
                'status' => 'assigned',
                'assigned_at' => null,
            ]);

            // Log initial status in history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Order placed by customer',
                'changed_by_type' => 'customer',
                'changed_by_id' => $customer->id,
            ]);

             // Clear the cart
            $cart->clear();

            return $order;
        });

        // Send notifications (after transaction commits)
        try {
            $notifier = app(\App\Services\OrderNotifier::class);
            $notifier->notifyCustomer($order, 'pending');
            $notifier->notifyAdminNewOrder($order);
        } catch (\Throwable $e) {
            \Log::error('Order notification failed: ' . $e->getMessage());
        }

        return $order;
    }
}