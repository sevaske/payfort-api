<?php

namespace Sevaske\PayfortApi\Tests;

use PHPUnit\Framework\TestCase;
use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;
use Sevaske\PayfortApi\Signature;

class SignatureTest extends TestCase
{
    public function test_valid_signature(): void
    {
        $expected = '9eb08299a5f9cda485e4967a25d4c8ff7ecfc98ae8f6d23fcb12457ea709fb26';
        $actual = (new Signature('sha-request-phrase', 'sha256'))->calculate([
            'query_command' => 'CHECK_STATUS',
            'language' => 'en',
            'merchant_reference' => '122333',
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_incorrect_sha_type(): void
    {
        $this->expectException(PayfortSignatureException::class);

        (new Signature('sha-request-phrase', 'incorrect-sha-type'))->calculate([
            'language' => 'en',
        ]);
    }

    public function test_verify_throws_exception_when_signature_is_missing(): void
    {
        $this->expectException(PayfortSignatureException::class);

        (new Signature('sha-request-phrase', 'sha256'))->verify([]);
    }

    public function test_verify_throws_exception_when_signature_is_invalid(): void
    {
        $this->expectException(PayfortSignatureException::class);

        (new Signature('sha-request-phrase', 'sha256'))->verify([
            'query_command' => 'CHECK_STATUS',
            'language' => 'en',
            'merchant_reference' => '122333',
            'signature' => 'wrong-signature',
        ]);
    }
}
