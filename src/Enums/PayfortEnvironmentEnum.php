<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortEnvironmentEnum: string
{
    case Production = 'production';

    case Sandbox = 'sandbox';

    public static function getUrl(string $environment): ?string
    {
        return self::tryFrom($environment)?->url();
    }

    public function url(): string
    {
        if ($this->value === self::Production->value) {
            return 'https://paymentservices.payfort.com/';
        }

        return 'https://sbpaymentservices.payfort.com/';
    }
}
