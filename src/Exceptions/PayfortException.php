<?php

namespace Sevaske\PayfortApi\Exceptions;

use Sevaske\PayfortApi\Interfaces\PayfortExceptionInterface;

class PayfortException extends \Exception implements PayfortExceptionInterface
{
    public function __construct(string $message = "", protected array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function withContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function context(): array
    {
        return $this->context;
    }
}