<?php

namespace Sevaske\PayfortApi\Http\Responses;

use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Http\Response;

/**
 * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#check-status-response
 */
class CheckStatusResponse extends Response
{
    /**
     * Determines if the check status request was successful.
     *
     * @return bool True if successful, false otherwise.
     */
    public function success(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::CheckStatusSuccess->value;
    }

    /**
     * Gets the total authorized amount for the order.
     *
     * Maximum length: 10 digits.
     *
     * @return null|string The authorized amount.
     */
    public function authorizedAmount(): ?string
    {
        return $this->getOptionalAttribute('authorized_amount');
    }

    /**
     * Gets the total captured amount for the order.
     *
     * Maximum length: 10 digits.
     *
     * @return null|string The captured amount.
     */
    public function capturedAmount(): ?string
    {
        return $this->getOptionalAttribute('captured_amount');
    }

    /**
     * Gets the total refunded amount for the order.
     *
     * Maximum length: 10 digits.
     *
     * @return null|string The refunded amount.
     */
    public function refundedAmount(): ?string
    {
        return $this->getOptionalAttribute('refunded_amount');
    }
}