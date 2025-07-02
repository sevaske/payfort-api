<?php

namespace Sevaske\PayfortApi;

use Psr\Http\Client\ClientInterface;
use Sevaske\PayfortApi\Enums\PayfortEnvironmentEnum;
use Sevaske\PayfortApi\Http\Api;
use Sevaske\PayfortApi\Http\Redirection;
use Sevaske\PayfortApi\Interfaces\CredentialInterface;
use Sevaske\PayfortApi\Interfaces\HasCredentialInterface;
use Sevaske\PayfortApi\Interfaces\MerchantInterface;
use Sevaske\PayfortApi\Traits\HasCredential;

class Merchant implements MerchantInterface, HasCredentialInterface
{
    use HasCredential;

    private Api $api;

    private Redirection $redirection;

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
        $this->credential = $credential;
        $this->api = new Api($environment, $httpClient, $credential);
        $this->redirection = new Redirection();
    }

    public function api(): Api
    {
        return $this->api;
    }

    // todo
    public function redirection(): Redirection
    {
        return $this->redirection;
    }
}