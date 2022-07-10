<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female']),
            'created_at' => fake()->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => fake()->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
