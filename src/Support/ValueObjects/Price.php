<?php

namespace Support\ValueObjects;

use Support\Traits\Makeable;

class Price
{
    use Makeable;

    private array $currencies = [
        'RUB' => '₽'
    ];

    public function __construct(
        private readonly int $value,
        private readonly string $currency = 'RUB'
    ) {
    }
}
