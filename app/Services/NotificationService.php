<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected string $apiKey;
    protected string $fromAddress;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->fromAddress = config('services.brevo.from_address');
        $this->fromName = config('services.brevo.from_name');
    }

    /**
     * Send an email via Brevo HTTP API and log it.
     */
    public function sendEmail(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent,
        ?Order $order = null,
        ?int $customerId = null
    ): bool {
        // Create a pending log entry first
        $log = NotificationLog::create([
            'platform_id' => $order?->platform_id ?? 1,
            'customer_id' => $customerId,
            'order_id' => $order?->id,
            'channel' => 'email',
            'recipient' => $toEmail,
            'subject' => $subject,
            'message' => $htmlContent,
            'status' => 'pending',
            'provider' => 'laravel_mail',
        ]);

        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromAddress,
                ],
                'to' => [
                    ['email' => $toEmail, 'name' => $toName],
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ]);

            if ($response->successful()) {
                $log->markAsSent($response->json());
                return true;
            }

            $log->markAsFailed('Brevo returned error', $response->json());
            Log::error('Brevo email failed', ['response' => $response->json(), 'to' => $toEmail]);
            return false;

        } catch (\Throwable $e) {
            $log->markAsFailed($e->getMessage());
            Log::error('Email send exception: ' . $e->getMessage());
            return false;
        }
    }
}