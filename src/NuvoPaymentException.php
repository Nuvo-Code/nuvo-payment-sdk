<?php

namespace NuvoPayment;

class NuvoPaymentException extends \Exception
{
    private int $httpStatus;
    private array $errorDetails;

    public function __construct(
        string $message = '',
        int $httpStatus = 0,
        array $errorDetails = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->httpStatus = $httpStatus;
        $this->errorDetails = $errorDetails;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }

    public function toArray(): array
    {
        return [
            'error' => true,
            'status' => $this->httpStatus,
            'message' => $this->getMessage(),
            'details' => $this->errorDetails,
        ];
    }
}

