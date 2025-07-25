<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.url');
    }

    /**
     * Initialize a transaction
     */
    public function initializeTransaction(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->secretKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl.'/transaction/initialize', $data);

        return $response->json();
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction(string $reference)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/transaction/verify/' . $reference);
    
        if ($response->failed()) {
            return [
                'status' => false,
                'message' => 'Failed to verify transaction',
                'data' => null
            ];
        }
    
        return $response->json();
    }

    /**
     * Create a subscription plan
     */
    public function createPlan(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/plan', $data);

        return $response->json();
    }

    /**
     * Create a subscription
     */
    public function createSubscription(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/subscription', $data);

        return $response->json();
    }
}