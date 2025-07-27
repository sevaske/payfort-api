<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortPurchaseCommandEnum: string
{
    case Authorization = 'Authorization';

    case Purchase = 'Purchase';
}
