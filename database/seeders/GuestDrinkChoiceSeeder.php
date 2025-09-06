<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuestDrinkChoice;
use App\Models\EventDrink;
use App\Models\Guest;
use App\Models\Event;

class GuestDrinkChoiceSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            // Récupérer les boissons de l'événement
            $eventDrinks = EventDrink::where('event_id', $event->id)->get();
            
            if ($eventDrinks->isEmpty()) {
                continue; // Passer si pas de boissons pour cet événement
            }

            // Récupérer les invités de l'événement
            $guests = Guest::where('event_id', $event->id)->get();

            foreach ($guests as $guest) {
                // 70% de chance qu'un invité fasse des choix de boissons
                if (rand(1, 100) <= 70) {
                    // Choisir 1 à 3 boissons aléatoirement
                    $numberOfChoices = rand(1, min(3, $eventDrinks->count()));
                    $selectedDrinks = $eventDrinks->random($numberOfChoices);

                    foreach ($selectedDrinks as $eventDrink) {
                        GuestDrinkChoice::create([
                            'guest_id' => $guest->id,
                            'event_drink_id' => $eventDrink->id,
                            'quantity' => rand(1, 3), // 1 à 3 verres
                            'comment' => rand(1, 100) <= 20 ? 'Sans glace' : null, // 20% de chance d'avoir un commentaire
                            'created_at' => now()->subDays(rand(1, 15)),
                        ]);
                    }
                }
            }
        }
    }
}
