<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeederMinimal extends Seeder
{
    /**
     * Seed minimal data for testing
     */
    public function run(): void
    {
        $this->call([
            MinimalDataSeeder::class,
        ]);
    }
}
