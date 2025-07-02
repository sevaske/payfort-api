<?php

namespace Sevaske\PayfortApi\Interfaces;

use ArrayAccess;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface that guarantees array-style access and JSON serialization.
 */
interface PayfortResponseInterface extends ArrayAccess, JsonSerializable
{
    /**
     * Returns the original raw PSR-7 HTTP response object.
     *
     * @return ResponseInterface The raw response.
     */
    public function raw(): ResponseInterface;
}