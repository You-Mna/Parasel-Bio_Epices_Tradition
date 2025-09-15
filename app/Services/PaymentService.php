<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    protected $provider;
    protected $config;

    public function __construct($provider = 'feda_pay')
    {
        $this->provider = $provider;
        $this->config = config("services.{$provider}");
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment(array $paymentData)
    {
        try {
            switch ($this->provider) {
                case 'feda_pay':
                    return $this->initializeFedaPayPayment($paymentData);
                case 'flutterwave':
                    return $this->initializeFlutterwavePayment($paymentData);
                case 'orange_money':
                    return $this->initializeOrangeMoneyPayment($paymentData);
                default:
                    throw new Exception("Payment provider {$this->provider} not supported");
            }
        } catch (Exception $e) {
            Log::error("Payment initialization failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment(string $transactionId)
    {
        try {
            switch ($this->provider) {
                case 'feda_pay':
                    return $this->verifyFedaPayPayment($transactionId);
                case 'flutterwave':
                    return $this->verifyFlutterwavePayment($transactionId);
                case 'orange_money':
                    return $this->verifyOrangeMoneyPayment($transactionId);
                default:
                    throw new Exception("Payment provider {$this->provider} not supported");
            }
        } catch (Exception $e) {
            Log::error("Payment verification failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * FedaPay Payment Implementation
     */
    private function initializeFedaPayPayment(array $paymentData)
    {
        $baseUrl = $this->config['environment'] === 'live' 
            ? 'https://api.fedapay.com' 
            : 'https://api-sandbox.fedapay.com';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_key'],
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/v1/transactions", [
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'XOF',
            'description' => $paymentData['description'],
            'callback_url' => $paymentData['callback_url'],
            'customer' => [
                'firstname' => $paymentData['customer']['firstname'],
                'lastname' => $paymentData['customer']['lastname'],
                'email' => $paymentData['customer']['email'],
                'phone_number' => $paymentData['customer']['phone_number'],
            ],
            'payment_method' => [
                'type' => 'mobile_money',
                'provider' => $paymentData['provider'], // mtn, moov, orange
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'transaction_id' => $data['transaction']['id'],
                'status' => $data['transaction']['status'],
                'payment_url' => $data['transaction']['payment_url'] ?? null,
                'reference' => $data['transaction']['reference'],
            ];
        }

        throw new Exception('FedaPay payment initialization failed: ' . $response->body());
    }

    private function verifyFedaPayPayment(string $transactionId)
    {
        $baseUrl = $this->config['environment'] === 'live' 
            ? 'https://api.fedapay.com' 
            : 'https://api-sandbox.fedapay.com';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_key'],
        ])->get("{$baseUrl}/v1/transactions/{$transactionId}");

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'status' => $data['transaction']['status'],
                'amount' => $data['transaction']['amount'],
                'currency' => $data['transaction']['currency'],
                'reference' => $data['transaction']['reference'],
            ];
        }

        throw new Exception('FedaPay payment verification failed: ' . $response->body());
    }

    /**
     * Flutterwave Payment Implementation
     */
    private function initializeFlutterwavePayment(array $paymentData)
    {
        $baseUrl = $this->config['environment'] === 'live' 
            ? 'https://api.flutterwave.com' 
            : 'https://api.flutterwave.com/v3';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['secret_key'],
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/payments", [
            'tx_ref' => $paymentData['reference'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'XOF',
            'redirect_url' => $paymentData['callback_url'],
            'payment_options' => 'mobilemoney',
            'customer' => [
                'email' => $paymentData['customer']['email'],
                'phone_number' => $paymentData['customer']['phone_number'],
                'name' => $paymentData['customer']['firstname'] . ' ' . $paymentData['customer']['lastname'],
            ],
            'customizations' => [
                'title' => 'Parasel Bio',
                'description' => $paymentData['description'],
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'transaction_id' => $data['data']['id'],
                'status' => $data['data']['status'],
                'payment_url' => $data['data']['link'],
                'reference' => $data['data']['tx_ref'],
            ];
        }

        throw new Exception('Flutterwave payment initialization failed: ' . $response->body());
    }

    private function verifyFlutterwavePayment(string $transactionId)
    {
        $baseUrl = $this->config['environment'] === 'live' 
            ? 'https://api.flutterwave.com' 
            : 'https://api.flutterwave.com/v3';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['secret_key'],
        ])->get("{$baseUrl}/transactions/{$transactionId}/verify");

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'status' => $data['data']['status'],
                'amount' => $data['data']['amount'],
                'currency' => $data['data']['currency'],
                'reference' => $data['data']['tx_ref'],
            ];
        }

        throw new Exception('Flutterwave payment verification failed: ' . $response->body());
    }

    /**
     * Orange Money Payment Implementation
     */
    private function initializeOrangeMoneyPayment(array $paymentData)
    {
        // Orange Money API implementation would go here
        // This is a placeholder for the actual implementation
        throw new Exception('Orange Money integration not implemented yet');
    }

    private function verifyOrangeMoneyPayment(string $transactionId)
    {
        // Orange Money verification implementation would go here
        throw new Exception('Orange Money verification not implemented yet');
    }

    /**
     * Get supported payment providers
     */
    public static function getSupportedProviders()
    {
        return [
            'feda_pay' => 'FedaPay',
            'flutterwave' => 'Flutterwave',
            'orange_money' => 'Orange Money',
        ];
    }

    /**
     * Get provider configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Check if provider is configured
     */
    public function isConfigured()
    {
        return !empty($this->config['api_key']) || !empty($this->config['secret_key']);
    }
}


