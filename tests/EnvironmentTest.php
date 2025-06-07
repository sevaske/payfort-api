<?php

namespace Sevaske\PayfortApi\Tests;

use PHPUnit\Framework\TestCase;
use Sevaske\PayfortApi\Enums\PayfortEnvironmentEnum;

class EnvironmentTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('sandbox', PayfortEnvironmentEnum::Sandbox->value);
        $this->assertEquals('production', PayfortEnvironmentEnum::Production->value);
    }

    public function test_correct_sandbox_url(): void
    {
        $this->assertEquals('https://sbpaymentservices.payfort.com/', PayfortEnvironmentEnum::Sandbox->url());
        $this->assertEquals('https://sbpaymentservices.payfort.com/', PayfortEnvironmentEnum::getUrl('sandbox'));
    }

    public function test_correct_production_url(): void
    {
        $this->assertEquals('https://paymentservices.payfort.com/', PayfortEnvironmentEnum::Production->url());
        $this->assertEquals('https://paymentservices.payfort.com/', PayfortEnvironmentEnum::getUrl('production'));
    }

    public function test_correct_payment_api(): void
    {
        $this->assertEquals(
            'https://paymentservices.payfort.com/FortAPI/paymentApi',
            PayfortEnvironmentEnum::Production->paymentApi()
        );
    }

    public function test_correct_payment_page(): void
    {
        $this->assertEquals(
            'https://paymentservices.payfort.com/FortAPI/paymentPage',
            PayfortEnvironmentEnum::Production->paymentPage()
        );
    }

    public function test_invalid_environment_returns_null(): void
    {
        $this->assertNull(PayfortEnvironmentEnum::getUrl('staging'));
        $this->assertNull(PayfortEnvironmentEnum::getUrl(''));
        $this->assertNull(PayfortEnvironmentEnum::getUrl('PRODUCTION'));
    }
}