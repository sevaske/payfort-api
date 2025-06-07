<?php

namespace Sevaske\PayfortApi\Exceptions;

use Sevaske\PayfortApi\Interfaces\PayfortExceptionInterface;

class PayfortException extends \Exception implements PayfortExceptionInterface
{
    public function __construct(string $message = "", protected array $context = [])
    {
        parent::__construct($message);
    }

    public function withContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}