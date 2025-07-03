<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Message\ResponseInterface;
use Sevaske\PayfortApi\Enums\PayfortStatusEnum;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Exceptions\ReadOnlyAttributesException;
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
     * @param ResponseInterface|array $response The original PSR-7 HTTP response OR array.
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
     * @param ResponseInterface $response The HTTP response to parse.
     *
     * @return array The decoded JSON content as an array.
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

    /**
     * Serializes the internal attributes to an array for JSON representation.
     *
     * @return array The internal attributes.
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    /**
     * Checks whether the given offset exists in the internal attributes.
     *
     * @param mixed $offset The attribute key.
     * @return bool True if set, false otherwise.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Retrieves a value by array key (offset).
     *
     * @param mixed $offset The attribute key.
     * @return mixed|null The attribute value, or null if not set.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * Sets a value by array key (offset).
     *
     * @param mixed $offset The attribute key.
     * @param mixed $value The value to set.
     *
     * @throws ReadOnlyAttributesException If attributes are marked as read-only.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->readOnlyAttributes) {
            throw new ReadOnlyAttributesException;
        }

        $this->attributes[$offset] = $value;
    }

    /**
     * Unsets a value by array key (offset).
     *
     * @param mixed $offset The attribute key.
     *
     * @throws ReadOnlyAttributesException If attributes are marked as read-only.
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($this->readOnlyAttributes) {
            throw new ReadOnlyAttributesException;
        }

        unset($this->attributes[$offset]);
    }

    public function invalidRequest(): bool
    {
        return $this->getOptionalAttribute('status') === PayfortStatusEnum::InvalidRequest->value;
    }
}
