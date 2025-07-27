<?php

namespace Sevaske\PayfortApi\Exceptions;

class PayfortRequestException extends PayfortException
{
    public function __construct(string $message = '', array $payload = [], ?\Throwable $previous = null)
    {
        $context = $payload ? ['payload' => $payload] : [];
        parent::__construct($message, $context, (int) $previous?->getCode(), $previous);
    }
}
