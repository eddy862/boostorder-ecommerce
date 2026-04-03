<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Apple',
                'description' => 'Fresh red apple',
                'price' => 3.50,
                'stock' => 10,
            ],
            [
                'name' => 'Banana',
                'description' => 'Ripe banana',
                'price' => 1.50,
                'stock' => 20,
            ],
            [
                'name' => 'Orange',
                'description' => 'Fresh orange',
                'price' => 2.00,
                'stock' => 30,
            ],
            [
                'name' => 'Grapes',
                'description' => 'Sweet grapes',
                'price' => 4.00,
                'stock' => 15,
            ],
            [
                'name' => 'Mango',
                'description' => 'Juicy mango',
                'price' => 5.00,
                'stock' => 25,
            ],
            [
                'name' => 'Pineapple',
                'description' => 'Tropical pineapple',
                'price' => 6.00,
                'stock' => 12,
            ],
            [
                'name' => 'Strawberry',
                'description' => 'Fresh strawberries',
                'price' => 3.00,
                'stock' => 18,
            ],
            [
                'name' => 'Watermelon',
                'description' => 'Refreshing watermelon',
                'price' => 7.00,
                'stock' => 8,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
