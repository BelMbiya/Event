<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des types d'événements s'ils n'existent pas
        $eventTypes = [
            ['label' => 'Mariage'],
            ['label' => 'Anniversaire'],
            ['label' => 'Baptême'],
        ];

        foreach ($eventTypes as $typeData) {
            EventType::firstOrCreate(['label' => $typeData['label']], $typeData);
        }

        $events = [
            [
                'organizer_id' => 1, // Premier utilisateur créé
                'title' => 'Mariage de Marie & Jean',
                'description' => 'Cérémonie de mariage de Marie Dubois et Jean Martin',
                'event_date' => Carbon::now()->addMonths(2)->format('Y-m-d H:i:s'),
                'location' => 'Église Saint-Pierre, 123 Rue de la Paix, Kinshasa',
                'google_maps_url' => 'https://maps.google.com/?q=Kinshasa',
                'program' => 'Cérémonie religieuse à 14h, cocktail à 16h, dîner à 19h',
                'theme_color' => '#FF6B6B',
                'event_type_id' => EventType::where('label', 'Mariage')->first()->id,
            ],
            [
                'organizer_id' => 2, // Deuxième utilisateur créé
                'title' => 'Anniversaire de Sophie - 30 ans',
                'description' => 'Fête d\'anniversaire de Sophie Laurent',
                'event_date' => Carbon::now()->addWeeks(3)->format('Y-m-d H:i:s'),
                'location' => 'Restaurant Le Jardin, 456 Avenue des Fleurs, Lubumbashi',
                'google_maps_url' => 'https://maps.google.com/?q=Lubumbashi',
                'program' => 'Apéritif à 18h, dîner à 19h30, soirée dansante à 21h',
                'theme_color' => '#4ECDC4',
                'event_type_id' => EventType::where('label', 'Anniversaire')->first()->id,
            ],
            [
                'organizer_id' => 3, // Troisième utilisateur créé
                'title' => 'Baptême de Lucas',
                'description' => 'Cérémonie de baptême de Lucas Moreau',
                'event_date' => Carbon::now()->addMonth()->format('Y-m-d H:i:s'),
                'location' => 'Église Notre-Dame, 789 Boulevard de la République, Kisangani',
                'google_maps_url' => 'https://maps.google.com/?q=Kisangani',
                'program' => 'Cérémonie à 10h, réception à 12h',
                'theme_color' => '#45B7D1',
                'event_type_id' => EventType::where('label', 'Baptême')->first()->id,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}