<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

class VoidAuthorizationResponse extends Response
{
    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::AuthorizationVoidedSuccessfully->value;
    }
}