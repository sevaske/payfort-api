<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortApiEndpointEnum: string
{
    case Authorization = 'AUTHORIZATION';

    case Purchase = 'PURCHASE';

    case Tokenization = 'TOKENIZATION';

    case Capture = 'CAPTURE';

    case VoidAuthorization = 'VOID_AUTHORIZATION';

    case Refund = 'REFUND';

    case CheckStatus = 'CHECK_STATUS';

    case Recurring = 'RECURRING';

    case CreateToken = 'CREATE_TOKEN';

    case UpdateToken = 'UPDATE_TOKEN';
}
