<?php

namespace Sevaske\PayfortApi;

use Sevaske\PayfortApi\Interfaces\CredentialInterface;
use Sevaske\PayfortApi\Interfaces\HasCredentialInterface;
use Sevaske\PayfortApi\Interfaces\MerchantInterface;
use Sevaske\PayfortApi\Traits\HasCredentialTrait;

class Merchant implements MerchantInterface, HasCredentialInterface
{
    use HasCredentialTrait;

    public function __construct(CredentialInterface $credential)
    {
        $this->credential = $credential;
    }
}