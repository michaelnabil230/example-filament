<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        User::factory()->create([
            'name' => 'Michael Nabil',
            'email' => 'admin@admin.com',
        ]);
        $this->command->info('Admin user created.');

        $categories = Category::factory()
            ->count(20)
            ->has(
                Category::factory()->count(3),
                'children'
            )->create();
        $this->command->info('Categories created.');

        $customers = Customer::factory()->count(1000)
            ->create();
        $this->command->info('Customers created.');

        $products = Product::factory()->count(50)
            ->sequence(fn ($sequence) => ['category_id' => $categories->random(1)->first()->id])
            ->create();
        $this->command->info('Products created.');

        $orders = Order::factory()->count(1000)
            ->sequence(fn ($sequence) => ['customer_id' => $customers->random(1)->first()->id])
            ->has(
                OrderProduct::factory()
                    ->count(rand(2, 5))
                    ->state(fn (array $attributes, Order $order) => ['product_id' => $products->random(1)->first()->id]),
                'products'
            )
            ->create();
        $this->command->info('Orders created.');
    }
}
