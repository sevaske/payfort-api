<?php

namespace Sevaske\PayfortApi\Exceptions;

class PayfortInvalidRequestException extends PayfortRequestException
{
    public function __construct(string $message = 'Invalid request.', array $payload = [])
    {
        parent::__construct($message);
        $this->context['payload'] = $payload;
    }
}
