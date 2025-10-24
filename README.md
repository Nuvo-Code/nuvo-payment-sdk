# Nuvo Payment SDK

## Usage
```php
require 'vendor/autoload.php';

use NuvoPayment\NuvoPayment;

$nuvo = new NuvoPayment();

$payment = $nuvo->payments->create(
    amount: 49.99,
    currency: 'USD',
    redirectUrls: [
        'success' => 'https://example.com/payment/success',
        'cancel' => 'https://example.com/payment/cancel',
    ],
    metadata: ['order_id' => 'ORD-12345']
);

if (!empty($payment['redirect_url'])) {
    echo "Redirect user to: " . $payment['redirect_url'];
} else {
    echo "Error: " . json_encode($payment);
}

$status = $nuvo->payments->find('pay_123');
print_r($status);
```