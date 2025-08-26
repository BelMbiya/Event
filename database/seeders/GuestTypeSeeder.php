<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guest_types')->insert([
            ['label' => 'Parents', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Collègues de travail', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Membres d’église', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Amis', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Frères', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Amis du quartier', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Sœurs', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Groupe', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Famille', 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Autres', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
