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
    }
}
