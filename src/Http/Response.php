<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Message\ResponseInterface;
use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Interfaces\PayfortResponseInterface;
use Sevaske\PayfortApi\Traits\HasAttributes;

/**
 * Represents a structured API response from Payfort, with dynamic and read-only attributes.
 * Provides array-style access and JSON serialization.
 */
class Response implements PayfortResponseInterface
{
    use HasAttributes;

    /**
     * Constructs the ApiResponse object by parsing a PSR-7 response into attributes.
     *
     * @param  ResponseInterface|array  $response  The original PSR-7 HTTP response OR array.
     *
     * @throws PayfortResponseException If the response body cannot be parsed as valid JSON.
     */
    public function __construct(protected ResponseInterface|array $response)
    {
        if ($response instanceof ResponseInterface) {
            $this->attributes = self::parse($response);
        } else {
            $this->attributes = $this->response;
        }

        $this->readOnlyAttributes = true;
    }

    /**
     * Parses the PSR-7 response and returns an associative array of its JSON contents.
     *
     * @param  ResponseInterface  $response  The HTTP response to parse.
     * @return array The decoded JSON content as an array.
     *
     * @throws PayfortResponseException If JSON decoding fails.
     */
    public static function parse(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();
        $parsed = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw (new PayfortResponseException)->withContext([
                'message' => json_last_error_msg(),
                'content' => $content,
                'status' => $response->getStatusCode(),
            ]);
        }

        return (array) $parsed;
    }

    /**
     * Returns the original raw PSR-7 HTTP response object OR array.
     *
     * @return ResponseInterface|array The raw response.
     */
    public function raw(): ResponseInterface|array
    {
        return $this->response;
    }

    public function invalidRequest(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::InvalidRequest->value;
    }
}
