<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Marie',
                'last_name' => 'Dubois',
                'email' => 'marie.dubois@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+243 123 456 789',
                'role' => 'admin',
            ],
            [
                'first_name' => 'Jean',
                'last_name' => 'Martin',
                'email' => 'jean.martin@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+243 234 567 890',
                'role' => 'organizer',
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Laurent',
                'email' => 'sophie.laurent@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+243 345 678 901',
                'role' => 'organizer',
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Moreau',
                'email' => 'pierre.moreau@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+243 456 789 012',
                'role' => 'organizer',
            ],
            [
                'first_name' => 'Claire',
                'last_name' => 'Bernard',
                'email' => 'DE ',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+243 567 890 123',
                'role' => 'organizer',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}