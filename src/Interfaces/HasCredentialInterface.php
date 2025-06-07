<?php

namespace Sevaske\PayfortApi\Interfaces;

/**
 * Defines a contract for retrieving credential.
 */
interface HasCredentialInterface
{
    /**
     * Get the credential instance.
     *
     * @return CredentialInterface The credential instance.
     */
    public function credential(): CredentialInterface;
}
