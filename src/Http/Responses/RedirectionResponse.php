<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#redirection-response
 */
class RedirectionResponse extends Response
{
    public function status(): ?string
    {
        return $this->getOptionalAttribute('status');
    }

    public function cardHolderName(): ?string
    {
        return $this->getOptionalAttribute('card_holder_name');
    }

    public function cardNumber(): ?string
    {
        return $this->getOptionalAttribute('card_number');
    }

    public function expiryDate(): ?string
    {
        return $this->getOptionalAttribute('expiry_date');
    }

    public function agreementId(): ?string
    {
        return $this->getOptionalAttribute('agreement_id');
    }

    public function issuerCountry(): ?string
    {
        return $this->getOptionalAttribute('issuer_country');
    }

    public function issuerName(): ?string
    {
        return $this->getOptionalAttribute('issuer_name');
    }
}
