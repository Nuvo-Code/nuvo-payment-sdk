<?php

namespace NuvoPayment;

class Payment
{
    private HttpClient $http;

    public function __construct()
    {
        $this->http = new HttpClient();
    }

    /**
     * Create a new payment and return redirect URL
     */
    public function create(float $amount, string $currency = 'USD', array $redirectUrls = [], array $metadata = [])
    {
        $payload = [
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadata,
            'redirect_urls' => $redirectUrls,
        ];

        return $this->http->request('POST', '/api/v1/payments', [
            'json' => $payload
        ]);
    }

    /**
     * Retrieve payment details by ID
     */
    public function find(string $paymentId)
    {
        return $this->http->request('GET', "/api/v1/payments/{$paymentId}");
    }
}