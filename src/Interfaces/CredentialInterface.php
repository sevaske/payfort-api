<?php

namespace Sevaske\PayfortApi\Interfaces;

/**
 * Represents merchant credential.
 */
interface CredentialInterface
{
    /**
     * Get the merchant identifier.
     */
    public function merchantIdentifier(): string;

    /**
     * Get the access token for authentication.
     */
    public function accessCode(): string;

    /**
     * Get the SHA request phrase.
     */
    public function shaRequestPhrase(): string;

    /**
     * Get the SHA response phrase.
     */
    public function shaResponsePhrase(): string;

    /**
     * Get the hashing algorithm type.
     */
    public function shaType(): string;
}
