<?php

namespace Sevaske\PayfortApi\Traits;

use Sevaske\PayfortApi\Interfaces\CredentialInterface;

trait HasCredentialTrait
{
    protected CredentialInterface $credential;

    public function credential(): CredentialInterface
    {
        return $this->credential;
    }
}