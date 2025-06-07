<?php

namespace Sevaske\PayfortApi\Exceptions;

use Throwable;

class PayfortInvalidRequestException extends PayfortRequestException
{
    public function __construct(string $message = "Invalid request.", array $payload = [])
    {
        parent::__construct($message, 0, null);
        $this->context['payload'] = $payload;
    }
}