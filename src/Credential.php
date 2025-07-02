<?php

namespace Sevaske\PayfortApi;

use Sevaske\PayfortApi\Interfaces\CredentialInterface;

class Credential implements CredentialInterface
{
    public function __construct(
        private readonly string $merchantIdentifier,
        private readonly string $accessCode,
        private readonly string $shaRequestPhrase,
        private readonly string $shaResponsePhrase,
        private readonly string $shaType = 'sha256',
    ) {}

    /**
     * Get the merchant identifier.
     */
    public function merchantIdentifier(): string
    {
        return $this->merchantIdentifier;
    }

    /**
     * Get the access token for authentication.
     */
    public function accessCode(): string
    {
        return $this->accessCode;
    }

    /**
     * Get the SHA request phrase.
     */
    public function shaRequestPhrase(): string
    {
        return $this->shaRequestPhrase;
    }

    /**
     * Get the SHA response phrase.
     */
    public function shaResponsePhrase(): string
    {
        return $this->shaResponsePhrase;
    }

    /**
     * Get the hashing algorithm type.
     */
    public function shaType(): string
    {
        return $this->shaType;
    }
}
