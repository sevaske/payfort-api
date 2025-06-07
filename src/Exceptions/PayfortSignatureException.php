<?php

namespace Sevaske\PayfortApi\Exceptions;

class PayfortSignatureException extends PayfortException
{
    public function __construct(
        string $message,
        array $payload,
        string $expectedSignature,
        string $actualSignature,
        string $shaPhrase,
        string $shaType = 'sha256',
    )
    {
        parent::__construct($message, [
            'payload' => $payload,
            'expected_signature' => $expectedSignature,
            'actual_signature' => $actualSignature,
            'sha_phrase' => $shaPhrase,
            'sha_type' => $shaType,
        ]);
    }

    public function getPayload(): array
    {
        return $this->context['payload'] ?? [];
    }

    public function getExpectedSignature(): string
    {
        return $this->context['expected_signature'] ?? '';
    }

    public function getActualSignature(): string
    {
        return $this->context['actual_signature'] ?? '';
    }

    public function getShaPhrase(): string
    {
        return $this->context['sha_phrase'] ?? '';
    }

    public function getShaType(): string
    {
        return $this->context['sha_type'] ?? '';
    }
}