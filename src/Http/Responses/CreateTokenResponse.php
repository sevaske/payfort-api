<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#create-new-token-service
 */
class CreateTokenResponse extends Response
{
    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::TokenCreatedSuccessfully->value;
    }

    public function tokenName(): ?string
    {
        return $this->getOptionalAttribute('token_name');
    }

    public function expiryDate(): bool
    {
        return $this->getOptionalAttribute('expiry_date');
    }

    /**
     * @return string|null The masked credit card’s number.
     *  Only the MEEZA payment option takes 19 digits card number.
     *  AMEX payment option takes 15 digits card number.
     *  Otherwise, they take 16 digits card number
     */
    public function cardNumber(): ?string
    {
        return $this->getOptionalAttribute('card_number');
    }
}