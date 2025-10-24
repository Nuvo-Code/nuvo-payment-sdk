<?php

namespace NuvoPayment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient
{
    private Client $client;
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim($_ENV['NUVO_PAYMENT_BASE_URL'], '/');
        $this->clientId = $_ENV['NUVO_CLIENT_ID'];
        $this->clientSecret = $_ENV['NUVO_CLIENT_SECRET'];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
        ]);
    }

    public function request(string $method, string $uri, array $options = [])
    {
        try {
            $response = $this->client->request($method, $uri, array_merge_recursive($options, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->clientSecret,
                    'X-Client-ID' => $this->clientId,
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
        }
    }
}