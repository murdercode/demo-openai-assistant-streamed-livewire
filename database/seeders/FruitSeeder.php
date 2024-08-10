<?php

namespace Database\Seeders;

use App\Models\Fruit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fruits = [
            ['name' => 'Apple', 'quantity' => 150, 'price' => 1.50],
            ['name' => 'Banana', 'quantity' => 200, 'price' => 0.50],
            ['name' => 'Cherry', 'quantity' => 100, 'price' => 2.50],
            ['name' => 'Date', 'quantity' => 300, 'price' => 3.50],
            ['name' => 'Elderberry', 'quantity' => 50, 'price' => 4.50],
        ];

        foreach ($fruits as $fruit) {
            Fruit::create($fruit);
        }
    }
}
