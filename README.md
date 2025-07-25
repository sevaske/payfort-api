[![Latest Version on Packagist](https://img.shields.io/packagist/v/sevaske/zatca.svg?style=flat-square)](https://packagist.org/packages/sevaske/payfort-api)
[![Total Downloads](https://img.shields.io/packagist/dt/sevaske/zatca.svg?style=flat-square)](https://packagist.org/packages/sevaske/payfort-api)

# Payfort API PHP Client

A simple and extensible PSR-18 compatible PHP client for working with [Amazon Payment Services (Payfort)](https://paymentservices.amazon.com/).

#### Note:
1. This version is currently in beta. Use at your own risk.
2. This package requires a PSR-18 compatible HTTP client and PSR-17 factories.
   For example, you can use Guzzle:
```bash
composer require guzzlehttp/guzzle nyholm/psr7
``` 

## Features

- PSR-18 HTTP client support
- PSR-7 request/response
- Signature verification
- Custom command support
- Exception handling with context
- Easily debug request/response payloads

## Requirements
- PHP ^8.1
- PSR-18 HTTP client (psr/http-client)
- PSR-7 HTTP messages (psr/http-message)
- PSR-17 HTTP factories (optional, if required by your HTTP client)

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require sevaske/payfort-api
```

## Usage
```php
use Sevaske\PayfortApi\Credential;
use Sevaske\PayfortApi\Merchant;
use Sevaske\PayfortApi\Http\Responses\CheckStatusResponse;

// Setup credentials
$credential = new Credential(
    'merchantIdentifier',
    'accessCode',
    'shaRequestPhrase',
    'shaResponsePhrase',
);

// Use any PSR-18 compatible HTTP client
$httpClient = new \GuzzleHttp\Client(); // or any other PSR-18 client

// Create merchant instance
$merchant = new Merchant('sandbox', $httpClient, $credential);

// Simple status check
$response = $merchant->api()->checkStatus('12345'); // CheckStatusResponse
```

#### Available API methods
The following methods are available for use:

```php
$merchant->api()->checkStatus(); // Check the status of a transaction
$merchant->api()->recurring(); // Purchase
$merchant->api()->voidAuthorization(); // Void an authorization
$merchant->api()->refund(); // Process a refund
$merchant->api()->createToken(); // Create a new payment token
$merchant->api()->updateToken(); // Update an existing payment token
```

#### Debugging with callback
```php
$response = $merchant->api()->checkStatus('12345', callback: function (
    CheckStatusResponse $response,
    array $request
) {
    $request;                      // Raw request data
    $response->jsonSerialize();    // Parsed response as array
    $response->authorizedAmount(); // ?string
    $response->capturedAmount();   // ?string
    $response->refundedAmount();   // ?string
    
    return $response;
});
```

#### Custom command request
```php
$response = $merchant->api()->request([
    'command' => 'CUSTOM',
]); // Returns generic Response
```

#### Error handling
```php
use Sevaske\PayfortApi\Exceptions\PayfortException;
use Sevaske\PayfortApi\Exceptions\PayfortRequestException;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;

try {
    $response = $merchant->api()->refund('12345', 1000, 'SAR'); // RefundResponse
} catch (PayfortRequestException $e) {
    // Request could not be sent
} catch (PayfortResponseException $e) {
    // Received an invalid response
} catch (PayfortSignatureException $e) {
    // Signature mismatch
}

// or "catch-all" exception handling
try {
    $response = $merchant->api()->refund('12345', 1000, 'SAR');
} catch (PayfortException $e) {
    $context = $e->context();
}
```

## Future functionality
In the future, you will be able to handle redirection requests:

```php
$merchant->redirection()->...; // Example of future functionality
```

## 🧪 Testing

```bash
composer test
```

## 📜 Changelog

See [CHANGELOG.md](CHANGELOG.md) for recent changes.

## ⚖ License

MIT. See [LICENSE](LICENSE) for details.