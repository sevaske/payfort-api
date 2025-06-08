<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Message\ResponseInterface;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;

/**
 * Handles parsing of HTTP responses from the Payfort API.
 */
class ResponseParser
{
    /**
     * Parses the HTTP response and returns it as an array.
     *
     * @param ResponseInterface $response The HTTP response.
     * @return array Parsed response data.
     *
     * @throws PayfortResponseException If the response is not valid JSON.
     */
    public static function fromResponse(ResponseInterface $response): array
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
}
