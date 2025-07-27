<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#redirection-response
 */
class PurchaseResponse extends RedirectionResponse
{
    public function success(): bool
    {
        return $this->status() === PayfortStatusEnum::PurchaseSuccess->value;
    }
}