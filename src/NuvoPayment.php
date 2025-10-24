<?php

namespace NuvoPayment;

use Dotenv\Dotenv;

class NuvoPayment
{
    public Payment $payments;

    public function __construct(string $envPath = __DIR__ . '/../')
    {
        if (file_exists($envPath . '/.env')) {
            Dotenv::createImmutable($envPath)->load();
        }

        $this->payments = new Payment();
    }
}