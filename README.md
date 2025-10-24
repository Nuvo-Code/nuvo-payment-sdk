# Nuvo Payment SDK

A simple, production-ready PHP SDK for integrating with the Nuvo Payment API. Easily create payments, retrieve payment status, and handle redirects with minimal setup.

## Features

- ✅ Simple, intuitive API
- ✅ Environment-based configuration
- ✅ Automatic error handling with structured responses
- ✅ Full test coverage with PHPUnit
- ✅ Guzzle HTTP client with timeout and retry support
- ✅ PSR-4 autoloading
- ✅ Production-ready and Packagist-compatible

## Installation

Install via Composer:

```bash
composer require nuvocode/nuvo-payment-sdk
```

## Configuration

### 1. Create `.env` file

Copy `.env.example` to `.env` and fill in your credentials:

```bash
cp .env.example .env
```

### 2. Set Environment Variables

Edit `.env` with your Nuvo Payment API credentials:

```env
NUVO_PAYMENT_BASE_URL=https://payment.nuvocode.com
NUVO_CLIENT_ID=your-client-id
NUVO_CLIENT_SECRET=your-client-secret
```

## Usage

### Basic Setup

```php
<?php
require 'vendor/autoload.php';

use NuvoPayment\NuvoPayment;

// Initialize the SDK (loads .env automatically)
$nuvo = new NuvoPayment();
```

### Create a Payment

```php
$payment = $nuvo->payments->create(
    amount: 49.99,
    currency: 'USD',
    redirectUrls: [
        'success' => 'https://example.com/payment/success',
        'cancel' => 'https://example.com/payment/cancel',
    ],
    metadata: ['order_id' => 'ORD-12345']
);

if (!isset($payment['error'])) {
    // Success - redirect user to payment page
    header('Location: ' . $payment['redirect_url']);
    exit;
} else {
    // Handle error
    echo "Payment creation failed: " . $payment['message'];
}
```

### Retrieve Payment Status

```php
$status = $nuvo->payments->find('pay_123');

if (!isset($status['error'])) {
    echo "Payment Status: " . $status['status'];
    echo "Amount: " . $status['amount'] . " " . $status['currency'];
} else {
    echo "Error: " . $status['message'];
}
```

### Error Handling

All API responses follow a consistent structure:

**Success Response:**
```php
[
    'status' => 'success',
    'payment_id' => 'pay_123',
    'redirect_url' => 'https://checkout.stripe.com/pay/cs_test_abc',
    'provider' => 'stripe'
]
```

**Error Response:**
```php
[
    'error' => true,
    'status' => 401,
    'message' => 'Unauthorized - Invalid credentials'
]
```

## Testing

### Run Tests

```bash
composer test
```

### Run Tests with Coverage

```bash
composer test-coverage
```

This generates an HTML coverage report in the `coverage/` directory.

### Test Structure

Tests are located in `tests/PaymentTest.php` and include:

- ✅ Environment loading
- ✅ Payment creation with valid payload
- ✅ Payment creation with metadata
- ✅ Payment status retrieval
- ✅ Error handling (401, 500)
- ✅ SDK initialization

All tests use Guzzle's `MockHandler` for isolated, fast testing without real API calls.

## API Reference

### `NuvoPayment::__construct(string $envPath = __DIR__ . '/../')`

Initialize the SDK. Automatically loads `.env` file if it exists.

**Parameters:**
- `$envPath` (string): Path to directory containing `.env` file

**Example:**
```php
$nuvo = new NuvoPayment(__DIR__ . '/../');
```

### `Payment::create(float $amount, string $currency = 'USD', array $redirectUrls = [], array $metadata = [])`

Create a new payment.

**Parameters:**
- `$amount` (float): Payment amount
- `$currency` (string): Currency code (default: 'USD')
- `$redirectUrls` (array): Redirect URLs with 'success' and 'cancel' keys
- `$metadata` (array): Additional metadata (optional)

**Returns:** Array with payment details or error information

**Example:**
```php
$response = $nuvo->payments->create(
    49.99,
    'USD',
    [
        'success' => 'https://example.com/success',
        'cancel' => 'https://example.com/cancel'
    ],
    ['order_id' => 'ORD-12345']
);
```

### `Payment::find(string $paymentId)`

Retrieve payment details by ID.

**Parameters:**
- `$paymentId` (string): Payment ID to retrieve

**Returns:** Array with payment details or error information

**Example:**
```php
$payment = $nuvo->payments->find('pay_123');
```

## Project Structure

```
nuvo-payment-sdk/
├── src/
│   ├── NuvoPayment.php           # Main SDK class
│   ├── Payment.php               # Payment service
│   ├── HttpClient.php            # HTTP client wrapper
│   └── NuvoPaymentException.php  # Custom exception class
├── tests/
│   └── PaymentTest.php           # PHPUnit tests
├── .env.example                  # Environment template
├── composer.json                 # Composer configuration
├── phpunit.xml                   # PHPUnit configuration
└── README.md                     # This file
```

## Requirements

- PHP >= 8.1
- Composer
- guzzlehttp/guzzle ^7.0
- vlucas/phpdotenv ^5.5

## Development

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
composer test
```

### Code Quality

The SDK follows PSR-12 coding standards and includes comprehensive PHPUnit tests.

## License

MIT

## Support

For issues, questions, or contributions, please visit the repository or contact support@nuvocode.com