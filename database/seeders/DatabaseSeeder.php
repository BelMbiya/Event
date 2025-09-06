<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EventTypeSeeder::class,
            GuestTypeSeeder::class,
            GuestCategorySeeder::class,
            UserSeeder::class,
            EventSeeder::class,
            GuestSeeder::class,
            InvitationSeeder::class,
            GuestBookSeeder::class,
            CongoleseDrinksSeeder::class, // Nouveau seeder pour les boissons congolaises
            EventDrinkSeeder::class,
            GuestDrinkChoiceSeeder::class,
            InvitationContentSeeder::class,
            ContentSeeder::class,
            DrinkPricesSeeder::class, // Seeder pour les prix des boissons
        ]);
        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])*/
    }
}
