<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#update-token-service
 */
class UpdateTokenResponse extends Response
{

    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::TokenUpdatedSuccessfully->value;
    }

    public function tokenName(): ?string
    {
        return $this->getOptionalAttribute('token_name');
    }

    public function tokenStatus(): ?bool
    {
        return match($this->getOptionalAttribute('token_status')) {
            'ACTIVE' => true,
            'INACTIVE' => false,
            'default' => null,
        };
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

    /**
     * @return string|null Issuer account type: MASTERCARD, VISA, AMEX
     */
    public function cardBrand(): ?string
    {
        return $this->getOptionalAttribute('card_brand');
    }

    /**
     * @return string|null The first 6 digits of the card number.
     *  If the card number for MEEZA was of length 19 then the card bin will be the first 8 digits.
     */
    public function cardBin(): ?string
    {
        return $this->getOptionalAttribute('card_bin');
    }
}