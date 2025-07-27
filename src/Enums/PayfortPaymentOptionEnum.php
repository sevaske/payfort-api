<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortPaymentOptionEnum: string
{
    case MASTERCARD = 'MASTERCARD';

    case VISA = 'VISA';

    case AMEX = 'AMEX';

    case SADAD = 'SADAD'; // (for purchase operations only)

    case NAPS = 'NAPS'; // (for purchase operations only)

    case KNET = 'KNET'; // (for purchase operations only)

    case OMANNET = 'OMANNET'; // (for purchase operations and ECI E-commerce only)

    case BENEFIT = 'BENEFIT'; // (for purchase operations and ECI E-commerce only)

    case MADA = 'MADA'; // (for purchase operations and ECI E-commerce only)

    case MEEZA = 'MEEZA'; // (for purchase operations and E-commerce ECI only)

    case TABBY = 'TABBY';
}
