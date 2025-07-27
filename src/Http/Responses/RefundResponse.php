<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://docs.payfort.com/docs/api/build/index.html#refund-operation-response
 */
class RefundResponse extends Response
{
    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::RefundSuccess->value;
    }

    public function merchantReference()
    {
        return $this->getOptionalAttribute('merchant_reference');
    }

    public function amount(): ?string
    {
        return $this->getOptionalAttribute('amount');
    }

    public function currency(): ?string
    {
        return $this->getOptionalAttribute('currency');
    }
}
