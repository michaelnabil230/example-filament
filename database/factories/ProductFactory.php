<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'name' => $name = fake()->unique()->catchPhrase(),
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->ean8(),
            'description' => fake()->realText(),
            'quantity' => fake()->randomDigitNotNull(),
            'featured' => fake()->boolean(),
            'is_visible' => fake()->boolean(),
            'price' => fake()->randomFloat(2, 80, 400),
            'created_at' => fake()->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => fake()->dateTimeBetween('-5 month', 'now'),
        ];
    }

    public function configure(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            $imageUrl = 'https://picsum.photos/200';
            $product
                ->addMediaFromUrl($imageUrl)
                ->toMediaCollection('product-images');
        });
    }
}
