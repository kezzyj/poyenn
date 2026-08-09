<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\FlutterwaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(protected FlutterwaveService $flutterwave) {}

    /**
     * Customer returns here after paying on Flutterwave.
     */
    public function callback(Request $request): RedirectResponse
    {
        $status = $request->query('status');
        $txRef = $request->query('tx_ref');
        $transactionId = $request->query('transaction_id');

        // Find the payment by our stored reference
        $payment = Payment::where('flutterwave_ref', $txRef)->first();

        if (!$payment) {
            return redirect()->route('shop.home')
                ->with('error', 'Payment record not found.');
        }

        $order = $payment->order;

        // Security — make sure the logged-in customer owns this order
        if (auth()->check() && $order->customer_id !== auth()->id()) {
            abort(403);
        }

        // If customer cancelled on Flutterwave
        if ($status === 'cancelled' || !$transactionId) {
            $payment->markAsFailed('Payment cancelled by customer');

            return redirect()->route('shop.orders.show', $order)
                ->with('error', 'Payment was cancelled. You can try again from this page.');
        }

        // Verify the transaction with Flutterwave
        $verification = $this->flutterwave->verifyPayment($transactionId);

        if (!$verification['success']) {
            $payment->markAsFailed('Verification failed: ' . ($verification['message'] ?? 'unknown'));

            return redirect()->route('shop.orders.show', $order)
                ->with('error', 'We could not verify your payment. If you were charged, contact support.');
        }

        $transactionData = $verification['data'];

        // Validate the transaction matches our order
        if (!$this->flutterwave->isValidTransaction($transactionData, $order)) {
            $payment->markAsFailed('Transaction validation failed', $transactionData);

            return redirect()->route('shop.orders.show', $order)
                ->with('error', 'Payment validation failed. If you were charged, contact support.');
        }

        // SUCCESS — mark everything paid
        DB::transaction(function () use ($payment, $order, $transactionData, $transactionId) {
            $payment->update([
                'flutterwave_tx_id' => $transactionId,
                'flutterwave_payment_type' => $transactionData['payment_type'] ?? null,
            ]);

            $payment->markAsSuccessful($transactionData);

            $order->update(['payment_status' => 'paid']);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'note' => 'Payment received via Flutterwave',
                'changed_by_type' => 'system',
                'changed_by_id' => null,
            ]);
        });

        return redirect()->route('shop.orders.show', $order)
            ->with('success', 'Payment successful! Your order is confirmed.');
    }

    /**
     * Retry payment for an unpaid order.
     */
    public function retry(\App\Models\Order $order): RedirectResponse
    {
        abort_if($order->customer_id !== auth()->id(), 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('shop.orders.show', $order)
                ->with('error', 'This order is already paid.');
        }

        if ($order->payment_method !== 'flutterwave') {
            return redirect()->route('shop.orders.show', $order);
        }

        $result = $this->flutterwave->initializePayment($order, route('shop.payment.callback'));

        if ($result['success']) {
            return redirect()->away($result['payment_link']);
        }

        return redirect()->route('shop.orders.show', $order)
            ->with('error', $result['message'] ?? 'Could not start payment. Please try again.');
    }
}