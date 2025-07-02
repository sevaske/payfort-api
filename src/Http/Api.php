<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Client\ClientInterface;
use Sevaske\PayfortApi\Enums\PayfortApiEndpointEnum;
use Sevaske\PayfortApi\Enums\PayfortEnvironmentEnum;
use Sevaske\PayfortApi\Enums\PayfortPaymentEciEnum;
use Sevaske\PayfortApi\Exceptions\PayfortException;
use Sevaske\PayfortApi\Exceptions\PayfortRequestException;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;
use Sevaske\PayfortApi\Http\Responses\CheckStatusResponse;
use Sevaske\PayfortApi\Http\Responses\CreateTokenResponse;
use Sevaske\PayfortApi\Http\Responses\RecurringResponse;
use Sevaske\PayfortApi\Http\Responses\RefundResponse;
use Sevaske\PayfortApi\Http\Responses\UpdateTokenResponse;
use Sevaske\PayfortApi\Http\Responses\VoidAuthorizationResponse;
use Sevaske\PayfortApi\Interfaces\CredentialInterface;
use Sevaske\PayfortApi\Interfaces\HasCredentialInterface;
use Sevaske\PayfortApi\Traits\HasCredential;
use Sevaske\PayfortApi\Traits\RequestBuilder;

class Api implements HasCredentialInterface
{
    use HasCredential;
    use RequestBuilder;

    /**
     * Initialize the API request with an HTTP client and credentials.
     *
     * @param PayfortEnvironmentEnum|string $environment The environment to make requests (production|sandbox).
     * @param ClientInterface $httpClient The HTTP client for sending requests.
     * @param CredentialInterface $credential The credential instance for authentication and signing requests.
     */
    public function __construct(
        PayfortEnvironmentEnum|string $environment,
        ClientInterface $httpClient, 
        CredentialInterface $credential,
    )
    {
        if (is_string($environment)) {
            $environment = PayfortEnvironmentEnum::from($environment);
        }

        $this->baseUrl = $environment->url();
        $this->httpClient = $httpClient;
        $this->credential = $credential;
    }

    /**
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#check-status-request
     *
     * @throws PayfortRequestException
     * @throws PayfortResponseException
     * @throws PayfortSignatureException
     * @throws PayfortException
     */
    public function checkStatus(
        ?string $merchantReference,
        ?int $fortId = null,
        string $language = 'en',
        array $extra = [],
        string|bool|null $returnThirdPartyResponseCodes = null,
        callable|array|string|null $callback = null,
    ): mixed {
        if (is_bool($returnThirdPartyResponseCodes)) {
            $returnThirdPartyResponseCodes = $returnThirdPartyResponseCodes ? 'YES' : 'NO';
        }

        $requestPayload = $this->signRequestPayload([
            'query_command' => PayfortApiEndpointEnum::CheckStatus->value,
            'language' => $language,
            ...$this->filterPayload([
                'merchant_reference' => $merchantReference,
                'fort_id' => $fortId,
                'return_third_party_response_codes' => $returnThirdPartyResponseCodes,
            ]),
            ...$extra,
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new CheckStatusResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }

    /**
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#recurring-request
     *
     * @throws PayfortRequestException
     * @throws PayfortSignatureException
     * @throws PayfortResponseException
     */
    public function recurring(
        string $merchantReference,
        string $tokenName,
        string $customerEmail,
        int $amount,
        string $currency = 'SAR',
        string $language = 'en',
        array  $extra = [],
        callable|array|string|null $callback = null,
    ): mixed {
        $requestPayload = $this->signRequestPayload([
            'command' => PayfortApiEndpointEnum::Purchase->value,
            'eci' => PayfortPaymentEciEnum::Recurring->value,
            'language' => $language,
            'merchant_reference' => $merchantReference,
            'token_name' => $tokenName,
            'customer_email' => $customerEmail,
            'amount' => $amount,
            'currency' => $currency,
            ...$extra,
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new RecurringResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }

    /**
     * @see https://docs.payfort.com/docs/api/build/index.html#refund-operation-request
     *
     * @throws PayfortRequestException
     * @throws PayfortSignatureException
     * @throws PayfortResponseException
     */
    public function refund(
        ?string $merchantReference,
        int $amount,
        string $currency = 'SAR',
        string $language = 'en',
        ?int $fortId = null,
        ?string $maintenanceReference = null,
        ?string $orderDescription = null,
        callable|array|string|null $callback = null,
    ): mixed {
        $requestPayload = $this->signRequestPayload([
            'command' => PayfortApiEndpointEnum::Refund->value,
            'language' => $language,
            'amount' => $amount,
            'currency' => $currency,
            ...$this->filterPayload([
                'merchant_reference' => $merchantReference,
                'maintenance_reference' => $maintenanceReference,
                'fort_id' => $fortId,
                'order_description' => $orderDescription,
            ]),
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new RefundResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }

    /**
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#create-new-token-service
     *
     * @throws PayfortRequestException
     * @throws PayfortSignatureException
     * @throws PayfortResponseException
     */
    public function createToken(
        string $cardNumber,
        string $expiryDate,
        string $returnUrl,
        string $language = 'en',
        ?string $currency = null,
        ?string $tokenName = null,
        ?string $cardHolderName = null,
        callable|array|string|null $callback = null,
    ): mixed {
        $requestPayload = $this->signRequestPayload([
            'service_command' => PayfortApiEndpointEnum::CreateToken->value,
            'language' => $language,
            'card_number' => $cardNumber,
            'expiry_date' => $expiryDate,
            'return_url' => $returnUrl,
            ...$this->filterPayload([
                'currency' => $currency,
                'token_name' => $tokenName,
                'card_holder_name' => $cardHolderName,
            ]),
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new CreateTokenResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }

    /**
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#update-token-service
     *
     * @throws PayfortRequestException
     * @throws PayfortSignatureException
     * @throws PayfortResponseException
     */
    public function updateToken(
        string $merchantReference,
        string $tokenName,
        string $language = 'en',
        ?string $cardHolderName = null,
        ?string $currency = null,
        ?string $newTokenName = null,
        string|bool|null $tokenStatus = null,
        callable|array|string|null $callback = null,
    ): mixed {
        if (is_bool($tokenStatus)) {
            $tokenStatus = $tokenStatus ? 'ACTIVE' : 'INACTIVE';
        }

        $requestPayload = $this->signRequestPayload([
            'service_command' => PayfortApiEndpointEnum::UpdateToken->value,
            'language' => $language,
            'merchant_reference' => $merchantReference,
            'token_name' => $tokenName,
            ...$this->filterPayload([
                'currency' => $currency,
                'card_holder_name' => $cardHolderName,
                'token_status' => $tokenStatus,
                'new_token_name' => $newTokenName,
            ]),
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new UpdateTokenResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }

    /**
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#void-authorization-operation-request
     *
     * @throws PayfortRequestException
     * @throws PayfortResponseException
     * @throws PayfortSignatureException
     */
    public function voidAuthorization(
        ?string $merchantReference,
        ?int $fortId,
        string $language = 'en',
        ?string $orderDescription = null,
        callable|array|string|null $callback = null,
    ): mixed {

        $requestPayload = $this->signRequestPayload([
            'command' => PayfortApiEndpointEnum::VoidAuthorization->value,
            'language' => $language,
            ...$this->filterPayload([
                'merchant_reference' => $merchantReference,
                'fort_id' => $fortId,
                'order_description' => $orderDescription,
            ]),
        ]);
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new VoidAuthorizationResponse($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $requestPayload, $response);
        }

        return $response;
    }
}
