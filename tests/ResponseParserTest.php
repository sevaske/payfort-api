<?php

namespace Sevaske\PayfortApi\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sevaske\PayfortApi\Exceptions\PayfortResponseException;
use Sevaske\PayfortApi\Http\ResponseParser;

class ResponseParserTest extends TestCase
{
    public function test_parses_valid_json_response(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"status":"success","amount":100}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $result = ResponseParser::fromResponse($response);

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

        ResponseParser::fromResponse($response);
    }
}