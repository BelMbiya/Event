<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guest_categories')->insert([
            ['label' => 'Couple', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Mr', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Mme', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Mlle', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
