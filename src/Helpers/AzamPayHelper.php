<?php

namespace Webkul\AzamPay\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzamPayHelper
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $appName;
    protected string $apiUrl;
    protected string $authUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->clientId     = core()->getConfigData('sales.payment_methods.azampay.azampay_client_id')
            ?? env('AZAMPAY_CLIENT_ID');

        $this->clientSecret = core()->getConfigData('sales.payment_methods.azampay.azampay_api_key')
            ?? env('AZAMPAY_CLIENT_SECRET');

        $this->appName      = core()->getConfigData('sales.payment_methods.azampay.title')
            ?? env('AZAMPAY_APP_NAME', 'My Store');

        $this->sandbox      = (bool) (core()->getConfigData('sales.payment_methods.azampay.sandbox')
            ?? env('AZAMPAY_SANDBOX', true));

        if ($this->sandbox) {
            $this->apiUrl  = 'https://sandbox.azampay.co.tz';
            $this->authUrl = 'https://authenticator-sandbox.azampay.co.tz';
        } else {
            $this->apiUrl  = 'https://api.azampay.co.tz';
            $this->authUrl = 'https://authenticator.azampay.co.tz';
        }
    }

    /**
     * Get access token from AzamPay.
     */
    protected function getAccessToken(): string
    {
        $payload = [
            'appName'      => $this->appName,
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
        ];

        Log::info('AzamPay Auth Payload:', $payload);

        $response = Http::asJson()
            ->timeout(60)               // Total timeout
            ->connectTimeout(15)        // Connection timeout
            ->retry(2, 3000)            // Retry 2 times with 3s delay
            ->post($this->authUrl . '/api/v1/authorize', $payload);

        Log::info('AzamPay Auth Response:', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if ($response->successful() && isset($response['data']['accessToken'])) {
            return $response['data']['accessToken'];
        }

        throw new \Exception('AzamPay Auth Error [' . $response->status() . ']: ' . $response->body());
    }

    /**
     * Create a hosted checkout session and get the redirect URL.
     */
    public function createCheckoutSession(array $paymentData): string
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'appName'            => $this->appName,
            'clientId'           => $this->clientId,
            'vendorId'           => $paymentData['vendor_id'],
            'vendorName'         => $paymentData['vendor_name'],
            'language'           => $paymentData['language'] ?? 'en',
            'currency'           => $paymentData['currency'],
            'externalId'         => $paymentData['order_id'],
            'requestOrigin'      => $paymentData['request_origin'],
            'redirectFailURL'    => $paymentData['fail_url'],
            'redirectSuccessURL' => $paymentData['success_url'],
            'amount'             => $paymentData['amount'],
            'cart'               => $paymentData['cart'],
        ];

        Log::info('AzamPay Checkout Payload:', $payload);

        $response = Http::withToken($accessToken)
            ->asJson()
            ->timeout(60)
            ->connectTimeout(15)
            ->retry(2, 3000)
            ->post($this->apiUrl . '/api/v1/checkoutPages/postCheckout', $payload);

        Log::info('AzamPay Checkout Response:', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if ($response->successful() && is_string($response->body())) {
            return trim($response->body(), '"');
        }

        throw new \Exception('AzamPay Checkout Error [' . $response->status() . ']: ' . $response->body());
    }
}
