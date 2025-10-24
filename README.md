# Nuvo Payment SDK

A simple PHP SDK for the Nuvo Payment API.

## Installation

```bash
composer require nuvocode/nuvo-payment-sdk
```

## Setup

Copy `.env.example` to `.env` and add your credentials:

```env
NUVO_PAYMENT_BASE_URL=https://api.sample-payment.com
NUVO_CLIENT_ID=your-client-id
NUVO_CLIENT_SECRET=your-client-secret
```

## Quick Start

```php
use NuvoPayment\NuvoPayment;

$nuvo = new NuvoPayment();

// Create a payment
$payment = $nuvo->payments->create(49.99, 'USD', [
    'success' => 'https://example.com/success',
    'cancel' => 'https://example.com/cancel'
]);

if (!isset($payment['error'])) {
    header('Location: ' . $payment['redirect_url']);
} else {
    echo "Error: " . $payment['message'];
}

// Get payment status
$status = $nuvo->payments->find('pay_123');
```

## API Methods

### `create(float $amount, string $currency = 'USD', array $redirectUrls = [], array $metadata = [])`

Create a new payment and get a redirect URL.

```php
$payment = $nuvo->payments->create(
    49.99,
    'USD',
    [
        'success' => 'https://example.com/success',
        'cancel' => 'https://example.com/cancel'
    ],
    ['order_id' => 'ORD-12345']
);
```

### `find(string $paymentId)`

Retrieve payment details by ID.

```php
$status = $nuvo->payments->find('pay_123');
```

## Response Format

**Success:**
```php
[
    'status' => 'success',
    'payment_id' => 'pay_123',
    'redirect_url' => 'https://checkout.stripe.com/...',
    'provider' => 'stripe'
]
```

**Error:**
```php
[
    'error' => true,
    'status' => 401,
    'message' => 'Unauthorized'
]
```

## Testing

```bash
# Run tests
composer test

# Run with coverage report
composer test-coverage
```

## Requirements

- PHP >= 8.1
- guzzlehttp/guzzle ^7.0
- vlucas/phpdotenv ^5.5

## License

MIT