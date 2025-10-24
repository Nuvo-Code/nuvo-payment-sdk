<?php

namespace NuvoPayment\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use NuvoPayment\HttpClient;
use NuvoPayment\NuvoPayment;
use NuvoPayment\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    protected function setUp(): void
    {
        // Set up environment variables for testing
        $_ENV['NUVO_PAYMENT_BASE_URL'] = 'https://api.test.com';
        $_ENV['NUVO_CLIENT_ID'] = 'test-client-id';
        $_ENV['NUVO_CLIENT_SECRET'] = 'test-client-secret';
    }

    /**
     * Test that .env loading works
     */
    public function testEnvLoading(): void
    {
        $nuvo = new NuvoPayment(__DIR__ . '/../');
        $this->assertInstanceOf(Payment::class, $nuvo->payments);
    }

    /**
     * Test that create() builds valid JSON payload and handles 200 response
     */
    public function testCreatePaymentSuccess(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'success',
            'provider' => 'stripe',
            'payment_id' => 'pay_123',
            'redirect_url' => 'https://checkout.stripe.com/pay/cs_test_abc'
        ]));

        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Create a Payment instance with mocked client
        $payment = new Payment($client);

        $response = $payment->create(49.99, 'USD', [
            'success' => 'https://example.com/success',
            'cancel' => 'https://example.com/cancel'
        ]);

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('pay_123', $response['payment_id']);
        $this->assertStringContainsString('checkout.stripe.com', $response['redirect_url']);
    }

    /**
     * Test that create() includes metadata
     */
    public function testCreatePaymentWithMetadata(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'success',
            'payment_id' => 'pay_456',
            'redirect_url' => 'https://checkout.stripe.com/pay/cs_test_def'
        ]));

        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $payment = new Payment($client);

        $response = $payment->create(99.99, 'USD', [
            'success' => 'https://example.com/success',
            'cancel' => 'https://example.com/cancel'
        ], [
            'order_id' => 'ORD-12345',
            'customer_id' => 'cust_789'
        ]);

        $this->assertIsArray($response);
        $this->assertEquals('pay_456', $response['payment_id']);
    }

    /**
     * Test that find() correctly sends GET request and parses JSON
     */
    public function testFindPaymentSuccess(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'completed',
            'payment_id' => 'pay_123',
            'amount' => 49.99,
            'currency' => 'USD',
            'provider' => 'stripe'
        ]));

        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $payment = new Payment($client);
        $response = $payment->find('pay_123');

        $this->assertIsArray($response);
        $this->assertEquals('completed', $response['status']);
        $this->assertEquals('pay_123', $response['payment_id']);
        $this->assertEquals(49.99, $response['amount']);
    }

    /**
     * Test error handling for 401 Unauthorized
     */
    public function testCreatePaymentUnauthorized(): void
    {
        $response401 = new Response(401, [], json_encode([
            'error' => 'Unauthorized',
            'message' => 'Invalid credentials'
        ]));

        $exception = new ClientException('Unauthorized', new Request('POST', '/api/v1/payments'), $response401);
        $mock = new MockHandler([$exception]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $payment = new Payment($client);
        $response = $payment->create(49.99, 'USD');

        $this->assertIsArray($response);
        $this->assertTrue($response['error']);
        $this->assertEquals(401, $response['status']);
    }

    /**
     * Test error handling for 500 Server Error
     */
    public function testCreatePaymentServerError(): void
    {
        $response500 = new Response(500, [], json_encode([
            'error' => 'Internal Server Error',
            'message' => 'Something went wrong'
        ]));

        $exception = new ClientException('Server Error', new Request('POST', '/api/v1/payments'), $response500);
        $mock = new MockHandler([$exception]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $payment = new Payment($client);
        $response = $payment->create(49.99, 'USD');

        $this->assertIsArray($response);
        $this->assertTrue($response['error']);
        $this->assertEquals(500, $response['status']);
    }

    /**
     * Test that Payment class is properly initialized
     */
    public function testPaymentInitialization(): void
    {
        $payment = new Payment();
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /**
     * Test NuvoPayment initialization
     */
    public function testNuvoPaymentInitialization(): void
    {
        $nuvo = new NuvoPayment();
        $this->assertInstanceOf(Payment::class, $nuvo->payments);
    }
}

