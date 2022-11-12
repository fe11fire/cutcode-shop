<?php

namespace Tests\Unit\Support\Casts;

use Tests\TestCase;
use App\Models\Product;
use Database\Factories\ProductFactory;
use InvalidArgumentException;
use Support\ValueObjects\Price;

use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceCastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_set_price(): void
    {
        $p = 3000000;

        ProductFactory::new()->count(10)->create([
            'price' => Price::make($p)
        ]);
        $this->assertDatabaseHas('products', ['price' => $p]);
    }

    /**
     * @test
     * @return void
     */
    public function it_get_price(): void
    {
        ProductFactory::new()->count(10)->create();
        $product = Product::query()->first();
        $this->assertInstanceOf(Price::class, $product->price);
    }
}
