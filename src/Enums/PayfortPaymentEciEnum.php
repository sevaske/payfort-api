<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortPaymentEciEnum: string
{
    case Moto = 'MOTO';

    case Recurring = 'RECURRING';

    case Ecommerce = 'ECOMMERCE';
}
