<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Client\ClientInterface;
use Sevaske\PayfortApi\Enums\PayfortApiEndpointEnum;
use Sevaske\PayfortApi\Enums\PayfortEnvironmentEnum;
use Sevaske\PayfortApi\Enums\PayfortPaymentEciEnum;
use Sevaske\PayfortApi\Enums\PayfortPaymentOptionEnum;
use Sevaske\PayfortApi\Enums\PayfortPurchaseCommandEnum;
use Sevaske\PayfortApi\Enums\PayfortRecurringModeEnum;
use Sevaske\PayfortApi\Exceptions\PayfortException;
use Sevaske\PayfortApi\Exceptions\PayfortRequestException;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;
use Sevaske\PayfortApi\Http\Responses\AuthorizationResponse;
use Sevaske\PayfortApi\Http\Responses\CheckStatusResponse;
use Sevaske\PayfortApi\Http\Responses\CreateTokenResponse;
use Sevaske\PayfortApi\Http\Responses\PurchaseResponse;
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
     * @param  PayfortEnvironmentEnum|string  $environment  The environment to make requests (production|sandbox).
     * @param  ClientInterface  $httpClient  The HTTP client for sending requests.
     * @param  CredentialInterface  $credential  The credential instance for authentication and signing requests.
     */
    public function __construct(
        PayfortEnvironmentEnum|string $environment,
        ClientInterface $httpClient,
        CredentialInterface $credential,
    ) {
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
        string|bool|null $returnThirdPartyResponseCodes = null,
        array $extra = [],
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
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
        array $extra = [],
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
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

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
        }

        return $response;
    }

    /**
     * @throws PayfortRequestException
     * @throws PayfortResponseException
     * @throws PayfortSignatureException
     */
    public function purchase(
        string $merchantReference,
        int $amount,
        string $currency,
        string $customerEmail,
        string $language = 'en',
        ?string $tokenName = null,
        PayfortPaymentOptionEnum|string|null $paymentOption = null,
        ?string $settlementReference = null,
        ?string $orderDescription = null,
        ?string $statementDescriptor = null,
        ?string $customerIp = null,
        ?string $customerName = null,
        ?bool $rememberMe = null,
        ?string $phoneNumber = null,
        ?string $returnUrl = null,
        ?string $agreementId = null,
        PayfortRecurringModeEnum|string|null $recurringMode = null,
        ?string $recurringTransactionsCount = null,
        ?string $recurringExpiryDate = null,
        ?string $recurringDaysBetweenPayments = null,
        array $extra = [],
        callable|array|string|null $callback = null,
    ) {
        $requestPayload = $this->prepareRedirectionPayload(
            PayfortPurchaseCommandEnum::Purchase,
            $merchantReference,
            $amount,
            $currency,
            $customerEmail,
            $language,
            $tokenName,
            $paymentOption,
            $settlementReference,
            $orderDescription,
            $statementDescriptor,
            $customerIp,
            $customerName,
            $rememberMe,
            $phoneNumber,
            $returnUrl,
            $agreementId,
            $recurringMode,
            $recurringTransactionsCount,
            $recurringExpiryDate,
            $recurringDaysBetweenPayments,
            $extra,
        );
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new PurchaseResponse($rawResponse);

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
        }

        return $response;
    }

    /**
     * @throws PayfortRequestException
     * @throws PayfortResponseException
     * @throws PayfortSignatureException
     */
    public function authorization(
        string $merchantReference,
        int $amount,
        string $currency,
        string $customerEmail,
        string $language = 'en',
        ?string $tokenName = null,
        PayfortPaymentOptionEnum|string|null $paymentOption = null,
        ?string $settlementReference = null,
        ?string $orderDescription = null,
        ?string $statementDescriptor = null,
        ?string $customerIp = null,
        ?string $customerName = null,
        ?bool $rememberMe = null,
        ?string $phoneNumber = null,
        ?string $returnUrl = null,
        ?string $agreementId = null,
        PayfortRecurringModeEnum|string|null $recurringMode = null,
        ?string $recurringTransactionsCount = null,
        ?string $recurringExpiryDate = null,
        ?string $recurringDaysBetweenPayments = null,
        array $extra = [],
        callable|array|string|null $callback = null,
    ) {
        $requestPayload = $this->prepareRedirectionPayload(
            PayfortPurchaseCommandEnum::Authorization,
            $merchantReference,
            $amount,
            $currency,
            $customerEmail,
            $language,
            $tokenName,
            $paymentOption,
            $settlementReference,
            $orderDescription,
            $statementDescriptor,
            $customerIp,
            $customerName,
            $rememberMe,
            $phoneNumber,
            $returnUrl,
            $agreementId,
            $recurringMode,
            $recurringTransactionsCount,
            $recurringExpiryDate,
            $recurringDaysBetweenPayments,
            $extra,
        );
        $rawResponse = $this->rawRequest($this->prepareApiRequestOptions($requestPayload));
        $response = new AuthorizationResponse($rawResponse);

        // handle the invalid request status and verify signature
        $this->catchInvalidStatus($response, $requestPayload);
        $this->verifyResponseSignature($response);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
        }

        return $response;
    }

    /**
     * @param  PayfortPurchaseCommandEnum|string  $command  Authorization or Purchase
     * @param  string  $merchantReference  The merchant’s unique order number.
     * @param  int  $amount  The transaction’s amount. Example: 10000
     * @param  string  $currency  The currency of the transaction’s amount in ISO code 3.
     * @param  string  $language  ONLY en|ar
     * @param  string|null  $tokenName  // The Token received from the Tokenization process.
     * @param  string|null  $settlementReference  The value is then passed to the acquiring bank and displayed to the
     *                                            merchant in the acquirer settlement file.
     * @param  string|null  $customerIp  // IPv4 and IPv6 are supported.
     * @param  bool|null  $rememberMe  This parameter provides you with an indication to whether to save this token for
     *                                 the user based on the user selection.
     * @param  string|null  $phoneNumber  The customer’s phone number.
     * @param  string|null  $returnUrl  // The URL of the Merchant’s page that will be displayed to the customer when the
     *                                  order is processed.
     * @param  string|null  $agreementId  // Identifier for the agreement with the payer to process payments to be used in
     *                                    recurring payments.
     * @param  PayfortRecurringModeEnum|string|null  $recurringMode  // Indicates if the subsequent payments within the
     *                                                               agreement has same/different amount or unscheduled (unknown interval/amount).
     * @param  string|null  $recurringTransactionsCount  The number of merchant-initiated payments within the recurring
     *                                                   payment agreement. Required only if recurring_mode = VARIABLE or FIXED
     * @param  string|null  $recurringExpiryDate  // The date where the merchant needs to end the recurring, the format is
     *                                            YYYY-MM-DD
     * @param  string|null  $recurringDaysBetweenPayments  The number of days between payments agreed with the payer under
     *                                                     your agreement with them.
     *
     * @throws PayfortSignatureException
     *
     * @see https://paymentservices-reference.payfort.com/docs/api/build/index.html#authorization-purchase-request
     */
    protected function prepareRedirectionPayload(
        PayfortPurchaseCommandEnum|string $command,
        string $merchantReference,
        int $amount,
        string $currency,
        string $customerEmail,
        string $language = 'en',
        ?string $tokenName = null,
        PayfortPaymentOptionEnum|string|null $paymentOption = null,
        ?string $settlementReference = null,
        ?string $orderDescription = null,
        ?string $statementDescriptor = null,
        ?string $customerIp = null,
        ?string $customerName = null,
        ?bool $rememberMe = null,
        ?string $phoneNumber = null,
        ?string $returnUrl = null,
        ?string $agreementId = null,
        PayfortRecurringModeEnum|string|null $recurringMode = null,
        ?string $recurringTransactionsCount = null,
        ?string $recurringExpiryDate = null,
        ?string $recurringDaysBetweenPayments = null,
        array $extra = [],
    ): array {
        if ($command instanceof PayfortPurchaseCommandEnum) {
            $command = $command->value;
        }

        if ($paymentOption instanceof PayfortPaymentOptionEnum) {
            $paymentOption = $paymentOption->value;
        }

        if ($recurringMode instanceof PayfortRecurringModeEnum) {
            $recurringMode = $recurringMode->value;
        }

        return $this->signRequestPayload([
            'command' => $command,
            'merchant_reference' => $merchantReference,
            'amount' => $amount,
            'currency' => $currency,
            'customer_email' => $customerEmail,
            'language' => $language,
            ...$this->filterPayload([
                'token_name' => $tokenName,
                'payment_option' => $paymentOption,
                'order_description' => $orderDescription,
                'statement_descriptor' => $statementDescriptor,
                'customer_ip' => $customerIp,
                'customer_name' => $customerName,
                'remember_me' => $rememberMe === false ? false : null,
                'phone_number' => $phoneNumber,
                'settlement_reference' => $settlementReference,
                'return_url' => $returnUrl,
                'agreement_id' => $agreementId,
                'recurring_mode' => $recurringMode,
                'recurring_transactions_count' => $recurringTransactionsCount,
                'recurring_expiry_date' => $recurringExpiryDate,
                'recurring_days_between_payments' => $recurringDaysBetweenPayments,
                ...$extra,
            ]),
        ]);
    }
}
