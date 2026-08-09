<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('flutterwave.secret_key');
        $this->baseUrl = config('flutterwave.base_url');
    }

    /**
     * Initialize a payment — returns the Flutterwave hosted payment link.
     */
    public function initializePayment(Order $order, string $redirectUrl): array
    {
        $customer = $order->customer;

        // Generate a unique transaction reference
        $txRef = 'POY-' . $order->id . '-' . Str::upper(Str::random(8));

        // Store the tx_ref on the payment record so we can verify later
        $payment = $order->latestPayment;
        $payment->update(['flutterwave_ref' => $txRef]);

        $payload = [
            'tx_ref' => $txRef,
            'amount' => (float) $order->total_amount,
            'currency' => 'NGN',
            'redirect_url' => $redirectUrl,
            'customer' => [
                'email' => $customer->email,
                'phonenumber' => $customer->phone,
                'name' => $customer->full_name,
            ],
            'customizations' => [
                'title' => 'Poyenn',
                'description' => 'Payment for order ' . $order->order_number,
            ],
            'meta' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->post($this->baseUrl . '/payments', $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'payment_link' => $data['data']['link'],
                ];
            }

            Log::error('Flutterwave init failed', ['response' => $data, 'order' => $order->id]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Could not initialize payment.',
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave init exception: ' . $e->getMessage(), ['order' => $order->id]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable. Please try again.',
            ];
        }
    }

    /**
     * Verify a payment using the transaction ID from Flutterwave.
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'data' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Verification failed.',
                'data' => $data['data'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave verify exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Could not verify payment.',
            ];
        }
    }

    /**
     * Validate that a verified transaction matches our order.
     */
    public function isValidTransaction(array $transactionData, Order $order): bool
    {
        return ($transactionData['status'] ?? '') === 'successful'
            && (float) ($transactionData['amount'] ?? 0) >= (float) $order->total_amount
            && ($transactionData['currency'] ?? '') === 'NGN';
    }
}