<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestType;
use App\Models\GuestCategory;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des types d'invités s'ils n'existent pas
        $guestTypes = [
            ['label' => 'Famille'],
            ['label' => 'Ami'],
            ['label' => 'Collègue'],
            ['label' => 'Voisin'],
        ];

        foreach ($guestTypes as $typeData) {
            GuestType::firstOrCreate(['label' => $typeData['label']], $typeData);
        }

        // Créer des catégories d'invités s'ils n'existent pas
        $guestCategories = [
            ['label' => 'VIP'],
            ['label' => 'Standard'],
            ['label' => 'Enfant'],
        ];

        foreach ($guestCategories as $categoryData) {
            GuestCategory::firstOrCreate(['label' => $categoryData['label']], $categoryData);
        }

        $events = Event::all();
        $guestTypes = GuestType::all();
        $guestCategories = GuestCategory::all();

        $guests = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Dupont',
                'email' => 'alice.dupont@example.com',
                'phone' => '+243 123 456 789',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Végétarien',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Michel',
                'last_name' => 'Bernard',
                'email' => 'michel.bernard@example.com',
                'phone' => '+243 234 567 890',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Catherine',
                'last_name' => 'Rousseau',
                'email' => 'catherine.rousseau@example.com',
                'phone' => '+243 345 678 901',
                'rsvp_status' => 'declined',
                'meal_choice' => null,
                'unique_url' => null,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Petit',
                'email' => 'david.petit@example.com',
                'phone' => '+243 456 789 012',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Sans gluten',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Isabelle',
                'last_name' => 'Moreau',
                'email' => 'isabelle.moreau@example.com',
                'phone' => '+243 567 890 123',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => null,
            ],
            [
                'first_name' => 'François',
                'last_name' => 'Simon',
                'email' => 'francois.simon@example.com',
                'phone' => '+243 678 901 234',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Nathalie',
                'last_name' => 'Michel',
                'email' => 'nathalie.michel@example.com',
                'phone' => '+243 789 012 345',
                'rsvp_status' => 'declined',
                'meal_choice' => null,
                'unique_url' => null,
            ],
            [
                'first_name' => 'Antoine',
                'last_name' => 'Garcia',
                'email' => 'antoine.garcia@example.com',
                'phone' => '+243 890 123 456',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Halal',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Valérie',
                'last_name' => 'Lopez',
                'email' => 'valerie.lopez@example.com',
                'phone' => '+243 901 234 567',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => null,
            ],
            [
                'first_name' => 'Thomas',
                'last_name' => 'Rodriguez',
                'email' => 'thomas.rodriguez@example.com',
                'phone' => '+243 012 345 678',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => null,
            ],
        ];

        foreach ($events as $event) {
            foreach ($guests as $index => $guestData) {
                $guestData['event_id'] = $event->id;
                $guestData['guest_type_id'] = $guestTypes->random()->id;
                $guestData['unique_url'] = $guestData['first_name'] . '-' . $guestData['last_name'] . '-' . $event->id . '-' . uniqid();
                
                Guest::create($guestData);
            }
        }
    }
}
