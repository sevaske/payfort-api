<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortRecurringModeEnum: string
{
    case Unscheduled = 'UNSCHEDULED';

    case Variable = 'VARIABLE';

    case Fixed = 'FIXED';
}
