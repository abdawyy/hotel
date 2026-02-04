<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;
    protected string $currency;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->currency = config('services.paypal.currency', 'USD');
        
        $mode = config('services.paypal.mode', 'sandbox');
        $this->baseUrl = $mode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Get PayPal access token
     */
    protected function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('PayPal Auth Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal Auth Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a PayPal order
     */
    public function createOrder(float $amount, string $bookingNumber, string $description = null): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $bookingNumber,
                            'description' => $description ?? "Payment for booking {$bookingNumber}",
                            'amount' => [
                                'currency_code' => $this->currency,
                                'value' => number_format($amount, 2, '.', ''),
                            ],
                        ],
                    ],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'landing_page' => 'NO_PREFERENCE',
                        'user_action' => 'PAY_NOW',
                        'return_url' => route('paypal.capture'),
                        'cancel_url' => route('paypal.cancel'),
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal Create Order Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal Create Order Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Capture a PayPal order
     */
    public function captureOrder(string $orderId): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->contentType('application/json')
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal Capture Order Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal Capture Order Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(string $orderId): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal Get Order Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal Get Order Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refund a captured payment
     */
    public function refundPayment(string $captureId, ?float $amount = null): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return null;
        }

        try {
            $data = [];
            if ($amount) {
                $data['amount'] = [
                    'currency_code' => $this->currency,
                    'value' => number_format($amount, 2, '.', ''),
                ];
            }

            $response = Http::withToken($accessToken)
                ->contentType('application/json')
                ->post("{$this->baseUrl}/v2/payments/captures/{$captureId}/refund", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal Refund Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal Refund Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the client ID for frontend
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get the currency
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
