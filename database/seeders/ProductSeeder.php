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
        Product::create([
            'name' => 'Apple',
            'description' => 'Fresh red apple',
            'price' => 3.50,
            'stock' => 10
        ]);

        Product::create([
            'name' => 'Banana',
            'description' => 'Ripe banana',
            'price' => 1.50,
            'stock' => 20
        ]);

        Product::create([
            'name' => 'Orange',
            'description' => 'Fresh orange',
            'price' => 2.00,
            'stock' => 30
        ]);
        Product::create([
            'name' => 'Grapes',
            'description' => 'Sweet grapes',
            'price' => 4.00,
            'stock' => 15
        ]);
        Product::create([
            'name' => 'Mango',
            'description' => 'Juicy mango',
            'price' => 5.00,
            'stock' => 25
        ]);
        Product::create([
            'name' => 'Pineapple',
            'description' => 'Tropical pineapple',
            'price' => 6.00,
            'stock' => 12
        ]);
        Product::create([
            'name' => 'Strawberry',
            'description' => 'Fresh strawberries',
            'price' => 3.00,
            'stock' => 18
        ]);
        Product::create([
            'name' => 'Watermelon',
            'description' => 'Refreshing watermelon',
            'price' => 7.00,
            'stock' => 8
        ]);
    }
}
