<?php

namespace Sevaske\PayfortApi\Traits;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionFunction;
use ReflectionMethod;
use Sevaske\PayfortApi\Exceptions\PayfortException;
use Sevaske\PayfortApi\Http\Response;
use Sevaske\PayfortApi\Interfaces\PayfortResponseInterface;
use Sevaske\PayfortApi\Interfaces\HasCredentialInterface;
use Sevaske\PayfortApi\Exceptions\PayfortRequestException;
use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;
use Sevaske\PayfortApi\Signature;
use Throwable;

/**
 * Provides methods for building and sending API requests.
 *
 * This trait contains logic for making API requests and handling
 * responses, including request signing and validation.
 */
trait RequestBuilder
{
    /**
     * @var string Base API url.
     */
    private string $baseUrl;

    /**
     * HTTP client instance for sending requests.
     *
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * Get the HTTP client instance.
     *
     * @return ClientInterface The HTTP client.
     */
    protected function httpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Send a raw request without processing the response.
     *
     * @param array $options Additional request options.
     * @param ?string $uri The endpoint URI. Pass null to set by default: $this->baseUrl.'FortAPI/paymentApi'
     * @param string $method The HTTP method (e.g., GET, POST).
     *
     * @return ResponseInterface The raw API response.
     *
     * @throws PayfortRequestException If an error occurs during the request.
     */
    public function rawRequest(
        array $options = [],
        ?string $uri = null,
        string $method = 'POST',
    ): ResponseInterface {
        if (! $uri) {
            $uri = $this->baseUrl.'FortAPI/paymentApi';
        }

        try {
            return $this->httpClient()->request($method, $uri, $options);
        } catch (ClientExceptionInterface $e) {
            throw new PayfortRequestException(
                'Request failed.',
                [
                    'uri' => $uri,
                    'method' => $method,
                    'options' => $options,
                ],
                $e
            );
        }
    }

    /**
     * Send an API request and process the response.
     *
     * @param array $payload The request payload.
     * @param callable|array|string|null $callback The callback to execute.
     *
     * @return mixed The response data OR when you pass a callback, you must return something inside.
     *
     * @throws PayfortSignatureException If signature validation fails.
     * @throws PayfortRequestException If the request fails.
     * @throws PayfortException If the callback is invalid or execution fails.
     */
    public function request(array $payload, callable|array|string|null $callback = null): mixed
    {
        $requestPayload = $this->signRequestPayload($payload);
        $requestOptions = $this->prepareApiRequestOptions($requestPayload);
        $rawResponse = $this->rawRequest($requestOptions);

        $response = new Response($rawResponse);

        if ($callback) {
            return self::executeCallback($callback, $response, $requestPayload);
        }

        return $response;
    }

    /**
     * Prepares the request payload with credentials and signature.
     *
     * If the class implements HasCredentialContract, credentials
     * are automatically added to the request.
     *
     * @param array $payload The initial request payload.
     * @return array The modified payload with credentials and signature.
     *
     * @throws PayfortSignatureException If signature generation fails.
     */
    protected function signRequestPayload(array $payload): array
    {
        if ($this instanceof HasCredentialInterface) {
            $credential = $this->credential();

            $payload = array_merge($payload, [
                'access_code' => $credential->accessCode(),
                'merchant_identifier' => $credential->merchantIdentifier(),
            ]);

            $payload['signature'] = Signature::fromCredential($credential, true)
                ->calculate($payload);
        }

        return $payload;
    }

    /**
     * Calls a given request callback function.
     *
     * @param callable|array|string $callback The callback to execute.
     * @param PayfortResponseInterface|ResponseInterface|array $response The response payload.
     * @param array $request The request payload.
     *
     * @throws PayfortRequestException If the callback is invalid or execution fails.
     */
    protected static function executeCallback(
        callable|array|string $callback,
        PayfortResponseInterface|ResponseInterface|array $response,
        array $request,
    ): mixed
    {
        // if it's an array, ensure it is a valid class-method pair
        if (is_array($callback)) {
            if (! isset($callback[0], $callback[1]) || !method_exists($callback[0], $callback[1])) {
                throw new PayfortRequestException("Invalid callback provided. Must be a valid class-method pair.");
            }
        }

        // ensure the callback is callable
        if (! is_callable($callback)) {
            throw new PayfortRequestException("Invalid callback provided. Must be callable.");
        }

        try {
            // use Reflection to check the number of required parameters
            $reflection = match (true) {
                is_array($callback) => new ReflectionMethod($callback[0], $callback[1]), // [Class, 'method']
                is_string($callback) && str_contains($callback, '::') => new ReflectionMethod(...explode('::', $callback, 2)),
                default => new ReflectionFunction($callback)  // closure or function
            };

            // ensure the callback accepts at least two parameters
            if ($reflection->getNumberOfParameters() < 2) {
                throw new PayfortRequestException("Callback must accept at least 2 parameters: (payload, response).");
            }

            // execute the callback
            return $callback($response, $request);
        } catch (Throwable $e) {
            throw new PayfortRequestException(
                'Callback execution failed: ' . $e->getMessage(),
                [
                    'callback' => is_array($callback) ? implode('::', $callback) : 'Closure',
                ],
                $e
            );
        }
    }

    /**
     * Removes null values from request data.
     */
    protected function filterPayload(array $payload): array
    {
        return array_filter($payload, fn ($value) => $value !== null);
    }

    protected function prepareApiRequestOptions(array $payload): array
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ];
    }
}
