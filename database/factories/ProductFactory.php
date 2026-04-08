<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

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
    public function definition(): array
    {
        return [
             'name'          => fake()->words(3, true),
        'slug'          => fake()->unique()->slug(),
        'description'   => fake()->paragraph(),
        'price'         => fake()->randomFloat(2, 50, 1000),
        'sale_price'    => null,
        'brand'         => fake()->company(),
        'stock'         => fake()->numberBetween(0, 100),
        'status'        => true,
        'is_featured'   => fake()->boolean(),
        'is_new_arrival'=> fake()->boolean(),
        'category_id'   => Category::factory(),
        ];
    }
}
