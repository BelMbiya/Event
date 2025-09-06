<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Drink;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinks = [
            [
                'name' => 'Coca-Cola',
                'type' => 'water',
                'alcoholic' => false,
                'unit' => 'can',
                'volume_ml' => 330,
                'active' => true
            ],
            [
                'name' => 'Orange Juice',
                'type' => 'juice',
                'alcoholic' => false,
                'unit' => 'bottle',
                'volume_ml' => 500,
                'active' => true
            ],
            [
                'name' => 'Beer',
                'type' => 'soft',
                'alcoholic' => true,
                'unit' => 'bottle',
                'volume_ml' => 500,
                'active' => true
            ],
            [
                'name' => 'Wine',
                'type' => 'hot',
                'alcoholic' => true,
                'unit' => 'glass',
                'volume_ml' => 150,
                'active' => true
            ]
        ];

        foreach ($drinks as $drink) {
            Drink::create($drink);
        }
    }
}
