<?php

namespace Tests\Unit\Support\ValueObjects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Support\ValueObjects\Price;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_all(): void
    {
        $price = Price::make(100000);

        $this->assertInstanceOf(Price::class, $price);
        $this->assertEquals(1000, $price->value());
        $this->assertEquals(100000, $price->raw());
        $this->assertEquals('RUB', $price->currency());
        $this->assertEquals('₽', $price->symbol());
        $this->assertEquals('1 000,00 ₽', $price);

        $this->expectException(InvalidArgumentException::class);
        $price = Price::make(-1);

        $this->expectException(InvalidArgumentException::class);
        $price = Price::make(100000, 'RUB');
    }
}
