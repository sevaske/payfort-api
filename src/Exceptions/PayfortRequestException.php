<?php

namespace Sevaske\PayfortApi\Exceptions;

use Throwable;

class PayfortRequestException extends PayfortException
{
    public function __construct(string $message = "An error occurred while processing the payfort request.", array $payload = [])
    {
        $context = $payload ? ['payload' => $payload] : [];
        parent::__construct($message, $context);
    }
}
