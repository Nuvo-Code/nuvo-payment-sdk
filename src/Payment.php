<?php

namespace NuvoPayment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Payment
{
    private HttpClient $http;
    private ?Client $mockClient;

    public function __construct(?Client $mockClient = null)
    {
        $this->mockClient = $mockClient;
        if ($mockClient === null) {
            $this->http = new HttpClient();
        }
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

        if ($this->mockClient !== null) {
            return $this->makeMockRequest('POST', '/api/v1/payments', [
                'json' => $payload
            ]);
        }

        return $this->http->request('POST', '/api/v1/payments', [
            'json' => $payload
        ]);
    }

    /**
     * Retrieve payment details by ID
     */
    public function find(string $paymentId)
    {
        if ($this->mockClient !== null) {
            return $this->makeMockRequest('GET', "/api/v1/payments/{$paymentId}");
        }

        return $this->http->request('GET', "/api/v1/payments/{$paymentId}");
    }

    /**
     * Make a request using the mock client (for testing)
     */
    private function makeMockRequest(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->mockClient->request($method, $uri, array_merge_recursive($options, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer test-client-secret',
                    'X-Client-ID' => 'test-client-id',
                    'X-Provider' => $options['headers']['X-Provider'] ?? 'stripe'
                ]
            ]));
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            return [
                'error' => true,
                'status' => $response ? $response->getStatusCode() : 500,
                'message' => $response ? $response->getBody()->getContents() : $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }
}