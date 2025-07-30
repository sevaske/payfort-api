<?php

namespace Sevaske\PayfortApi\Exceptions;

class PayfortEnvironmentException extends PayfortException
{
    public function __construct($message, string $environment, ?\Exception $previous = null)
    {
        parent::__construct(
            message: $message,
            context: ['environment' => $environment],
            previous: $previous
        );
    }
}
