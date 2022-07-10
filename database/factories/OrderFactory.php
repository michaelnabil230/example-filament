<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number' => 'OR'.fake()->unique()->randomNumber(6),
            'total_price' => fake()->randomFloat(2, 100, 2000),
            'status' => fake()->randomElement(['new', 'processing', 'shipped', 'delivered', 'cancelled']),
            'shipping_price' => fake()->randomFloat(2, 100, 500),
            'shipping_method' => fake()->randomElement(['free', 'flat', 'flat_rate', 'flat_rate_per_item']),
            'notes' => fake()->realText(100),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
