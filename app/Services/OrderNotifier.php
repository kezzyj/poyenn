<?php

namespace App\Services;

use App\Models\Order;

class OrderNotifier
{
    public function __construct(protected NotificationService $notifier) {}

    /**
     * Notify customer of an order status.
     */
    public function notifyCustomer(Order $order, string $status): void
    {
        $customer = $order->customer;
        if (!$customer || !$customer->email) {
            return;
        }

        $content = $this->getStatusContent($status, $order);
        if (!$content) {
            return;
        }

        $html = view('emails.order-status', [
            'heading' => $content['heading'],
            'message' => $content['message'],
            'order' => $order,
            'trackUrl' => route('shop.orders.track', [
                'order_number' => $order->order_number,
                'phone' => $order->delivery_phone,
            ]),
        ])->render();

        $this->notifier->sendEmail(
            $customer->email,
            $customer->full_name,
            $content['subject'],
            $html,
            $order,
            $customer->id
        );
    }

    /**
     * Notify admin of a new order.
     */
    public function notifyAdminNewOrder(Order $order): void
    {
        // Send to the from-address (your inbox) for now
        $adminEmail = config('services.brevo.from_address');

        $html = view('emails.order-status', [
            'heading' => 'New Order Received',
            'message' => "A new order has been placed by {$order->customer->full_name}. Review it in the admin panel.",
            'order' => $order,
            'trackUrl' => route('admin.orders.show', $order),
        ])->render();

        $this->notifier->sendEmail(
            $adminEmail,
            'Poyenn Admin',
            "New Order: {$order->order_number} (₦" . number_format($order->total_amount, 2) . ")",
            $html,
            $order,
            null
        );
    }

    /**
     * Map status to email content.
     */
    protected function getStatusContent(string $status, Order $order): ?array
    {
        $name = $order->customer->first_name;

        return match ($status) {
            'pending' => [
                'subject' => "Order Confirmed — {$order->order_number}",
                'heading' => "Thank you, {$name}!",
                'message' => "We've received your order and it's being processed. We'll keep you updated at every step.",
            ],
            'confirmed' => [
                'subject' => "Your Order is Confirmed — {$order->order_number}",
                'heading' => "Good news, {$name}!",
                'message' => "Your order has been confirmed and is being prepared for shipment.",
            ],
            'packed' => [
                'subject' => "Your Order is Packed — {$order->order_number}",
                'heading' => "Almost there, {$name}!",
                'message' => "Your order has been packed and is ready to be dispatched for delivery.",
            ],
            'out_for_delivery' => [
                'subject' => "Out for Delivery — {$order->order_number}",
                'heading' => "Your order is on the way, {$name}!",
                'message' => "Your order is now out for delivery. Our agent will reach you soon. Please keep your phone handy.",
            ],
            'delivered' => [
                'subject' => "Order Delivered — {$order->order_number}",
                'heading' => "Enjoy, {$name}!",
                'message' => "Your order has been delivered. Thank you for shopping with Poyenn. We hope to see you again soon!",
            ],
            'cancelled' => [
                'subject' => "Order Cancelled — {$order->order_number}",
                'heading' => "Order Cancelled",
                'message' => "Your order has been cancelled. If you have any questions or this was a mistake, please contact us.",
            ],
            default => null,
        };
    }
}