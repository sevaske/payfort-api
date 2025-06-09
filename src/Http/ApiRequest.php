<?php

namespace Sevaske\PayfortApi\Http;

use Psr\Http\Client\ClientInterface;
use Sevaske\PayfortApi\Enums\PayfortEnvironmentEnum;
use Sevaske\PayfortApi\Interfaces\CredentialInterface;
use Sevaske\PayfortApi\Traits\HasCredentialTrait;
use Sevaske\PayfortApi\Traits\RequestBuilder;

class ApiRequest
{
    use HasCredentialTrait;
    use RequestBuilder;

    protected PayfortEnvironmentEnum $environment;

    /**
     * Initialize the API request with an HTTP client and credentials.
     *
     * @param ClientInterface $httpClient The HTTP client for sending requests.
     * @param CredentialInterface $credential The credential instance for authentication and signing requests.
     */
    public function __construct(ClientInterface $httpClient, CredentialInterface $credential)
    {
        $this->httpClient = $httpClient;
        $this->credential = $credential;
    }
}
