<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('event_types')->insert([
            ['label' => 'Mariage', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Anniversaire', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Naissance / Baby Shower', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Baptême', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Anniversaire de mariage', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Remise de diplôme', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Événement d’entreprise', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Gala / Soirée caritative', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Autre', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
