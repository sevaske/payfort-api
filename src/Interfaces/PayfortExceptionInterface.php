<?php

namespace Sevaske\PayfortApi\Interfaces;

interface PayfortExceptionInterface
{
    public function withContext(array $context);

    public function context(): array;
}
