<?php

namespace Sevaske\PayfortApi\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Http\ApiResponse;

class ApiResponseTest extends TestCase
{
    public function test_attributes_valid_json_response(): void
    {
        $response = $this->mockResponse(['status' => 'success', 'amount' => 100]);

        $apiResponse = new ApiResponse($response);

        $this->assertEquals('success', $apiResponse->status);
        $this->assertEquals('success', $apiResponse['status']);
        $this->assertEquals(100, $apiResponse->amount);
    }

    public function test_to_array_and_json_serialize_are_identical(): void
    {
        $response = $this->mockResponse(['foo' => 'bar']);
        $apiResponse = new ApiResponse($response);

        $this->assertEquals(['foo' => 'bar'], $apiResponse->jsonSerialize());
    }

    public function test_raw_returns_original_response(): void
    {
        $response = $this->mockResponse(['check' => 'ok']);
        $apiResponse = new ApiResponse($response);

        $this->assertSame($response, $apiResponse->raw());
    }

    public function test_invalid_json_throws_exception(): void
    {
        $this->expectException(PayfortResponseException::class);

        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $stream->method('getContents')
            ->willReturn('{invalid json');

        $response->method('getBody')
            ->willReturn($stream);

        $response->method('getStatusCode')
            ->willReturn(400);

        new ApiResponse($response); // should throw
    }

    public function test_parses_valid_json_response(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"status":"success","amount":100}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $result = ApiResponse::parse($response);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals(100, $result['amount']);
    }

    public function test_throws_exception_on_invalid_json(): void
    {
        $this->expectException(PayfortResponseException::class);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('INVALID_JSON');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $response->method('getStatusCode')->willReturn(400);

        ApiResponse::parse($response);
    }

    private function mockResponse(array $bodyArray): ResponseInterface
    {
        $json = json_encode($bodyArray);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn($json);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        return $response;
    }
}