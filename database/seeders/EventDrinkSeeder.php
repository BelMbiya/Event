<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventDrink;

class EventDrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventdrinks = [
            [
                'event_id' => 1,
                'drink_id' => 2,
                'price' => 2000.00,
                'available' => 1,
                'limit_per_guest' => 5,
                'display_order' => 1
            ],
            [
                'event_id' => 1,
                'drink_id' => 1,
                'price' => 2000.00,
                'available' => 0,
                'limit_per_guest' => 5,
                'display_order' => 1
            ],
            [
                'event_id' => 1,
                'drink_id' => 3,
                'price' => 2500.00,
                'available' => 1,
                'limit_per_guest' => 5,
                'display_order' => 1
            ],
            [
                'event_id' => 1,
                'drink_id' => 4,
                'price' => 3000.00,
                'available' => 1,
                'limit_per_guest' => 5,
                'display_order' => 1
            ]
        ];

        foreach ($eventdrinks as $drink) {
            EventDrink::firstOrCreate(
                [
                    'event_id' => $drink['event_id'],
                    'drink_id' => $drink['drink_id']
                ],
                $drink
            );
        }
    }
}
