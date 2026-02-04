<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $currency;
    protected string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret_key');
        $this->publicKey = config('services.stripe.public_key');
        $this->currency = strtolower(config('services.stripe.currency', 'usd'));
    }

    /**
     * Create a Payment Intent
     */
    public function createPaymentIntent(float $amount, string $bookingNumber, array $metadata = []): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->asForm()
                ->post("{$this->baseUrl}/payment_intents", [
                    'amount' => $this->convertToCents($amount),
                    'currency' => $this->currency,
                    'description' => "Payment for booking {$bookingNumber}",
                    'metadata' => array_merge([
                        'booking_number' => $bookingNumber,
                    ], $metadata),
                    'automatic_payment_methods' => ['enabled' => 'true'],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Stripe Create Payment Intent Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Stripe Create Payment Intent Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve a Payment Intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Stripe Retrieve Payment Intent Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Stripe Retrieve Payment Intent Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a refund
     */
    public function createRefund(string $paymentIntentId, ?float $amount = null): ?array
    {
        try {
            $data = ['payment_intent' => $paymentIntentId];
            
            if ($amount) {
                $data['amount'] = $this->convertToCents($amount);
            }

            $response = Http::withBasicAuth($this->secretKey, '')
                ->asForm()
                ->post("{$this->baseUrl}/refunds", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Stripe Create Refund Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Stripe Create Refund Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert amount to cents (Stripe uses smallest currency unit)
     */
    protected function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Convert cents to dollars
     */
    public function convertFromCents(int $cents): float
    {
        return $cents / 100;
    }

    /**
     * Get the public key for frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Get the currency
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $sigHeader): bool
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        
        if (!$webhookSecret) {
            return false;
        }

        try {
            $elements = explode(',', $sigHeader);
            $timestamp = null;
            $signature = null;

            foreach ($elements as $element) {
                [$key, $value] = explode('=', $element, 2);
                if ($key === 't') {
                    $timestamp = $value;
                } elseif ($key === 'v1') {
                    $signature = $value;
                }
            }

            if (!$timestamp || !$signature) {
                return false;
            }

            $signedPayload = "{$timestamp}.{$payload}";
            $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Verification Error: ' . $e->getMessage());
            return false;
        }
    }
}
