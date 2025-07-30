<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#recurring-response
 */
class RecurringResponse extends Response
{
    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::PurchaseSuccess;
    }
}
