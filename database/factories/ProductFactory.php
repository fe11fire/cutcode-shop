<?php

namespace Database\Factories;

use Domain\Catalog\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => ucfirst($this->faker->word(2, true)),
            //TODO
            // 'thumbnail' => $this->faker->file(
            //     base_path('/tests/Fixtures/images/products'), 
            //     storage_path('/app/public/images/products')
            // ),
            'thumbnail' => $this->faker->image('images/products'),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'brand_id' => Brand::query()->inRandomOrder()->value('id'),
            'on_home_page' =>  $this->faker->boolean(),
            'sorting' => $this->faker->numberBetween(1, 999),
            'text' => $this->faker->realText(),
        ];
    }
}
