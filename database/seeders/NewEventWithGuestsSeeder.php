<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Guest;
use App\Models\GuestType;
use App\Models\GuestCategory;
use App\Models\EventTable;
use App\Models\Drink;
use App\Models\EventDrink;
use App\Models\Content;
use Carbon\Carbon;

class NewEventWithGuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un type d'événement si nécessaire
        $eventType = EventType::firstOrCreate([
            'label' => 'Anniversaire'
        ]);

        // Créer l'événement
        $event = Event::create([
            'organizer_id' => 1, // Assumant que l'utilisateur ID 1 existe
            'event_type_id' => $eventType->id,
            'title' => 'Anniversaire de Marie - 30 ans',
            'description' => 'Célébration du 30ème anniversaire de Marie avec famille et amis',
            'event_date' => Carbon::now()->addDays(30)->setTime(19, 0, 0),
            'location' => 'Restaurant Le Jardin, Kinshasa',
            'google_maps_url' => 'https://maps.google.com/?q=Restaurant+Le+Jardin+Kinshasa',
            'program' => 'Accueil des invités à 19h00, Cocktail d\'ouverture à 19h30, Dîner à 20h00, Coupure du gâteau à 21h30, Soirée dansante à 22h00',
            'theme_color' => '#ff6b6b',
        ]);

        // Créer des types d'invités
        $familyType = GuestType::firstOrCreate([
            'label' => 'Famille'
        ]);

        $friendType = GuestType::firstOrCreate([
            'label' => 'Ami(e)'
        ]);

        $colleagueType = GuestType::firstOrCreate([
            'label' => 'Collègue'
        ]);

        // Note: Les catégories d'invités ne sont pas utilisées dans la table guests actuelle

        // Créer des tables
        $table1 = EventTable::create([
            'event_id' => $event->id,
            'name' => 'Table Famille',
            'table_number' => 1,
            'capacity' => 8
        ]);

        $table2 = EventTable::create([
            'event_id' => $event->id,
            'name' => 'Table Amis',
            'table_number' => 2,
            'capacity' => 6
        ]);

        $table3 = EventTable::create([
            'event_id' => $event->id,
            'name' => 'Table Collègues',
            'table_number' => 3,
            'capacity' => 4
        ]);

        // Créer des invités
        $guests = [
            // Famille
            [
                'event_id' => $event->id,
                'guest_type_id' => $familyType->id,
                'event_table_id' => $table1->id,
                'first_name' => 'Jean',
                'last_name' => 'Mukendi',
                'email' => 'jean.mukendi@email.com',
                'phone' => '+243 123 456 789',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Végétarien',
                'unique_url' => 'jean-mukendi-' . uniqid(),
                'response_date' => Carbon::now()->subDays(5),
            ],
            [
                'event_id' => $event->id,
                'guest_type_id' => $familyType->id,
                'event_table_id' => $table1->id,
                'first_name' => 'Sophie',
                'last_name' => 'Mukendi',
                'email' => 'sophie.mukendi@email.com',
                'phone' => '+243 123 456 790',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => 'sophie-mukendi-' . uniqid(),
                'response_date' => Carbon::now()->subDays(3),
            ],
            [
                'event_id' => $event->id,
                'guest_type_id' => $familyType->id,
                'event_table_id' => $table1->id,
                'first_name' => 'Pierre',
                'last_name' => 'Mukendi',
                'email' => 'pierre.mukendi@email.com',
                'phone' => '+243 123 456 791',
                'rsvp_status' => 'pending',
                'meal_choice' => null,
                'unique_url' => 'pierre-mukendi-' . uniqid(),
                'response_date' => null,
            ],
            // Amis
            [
                'event_id' => $event->id,
                'guest_type_id' => $friendType->id,
                'event_table_id' => $table2->id,
                'first_name' => 'Alice',
                'last_name' => 'Kabila',
                'email' => 'alice.kabila@email.com',
                'phone' => '+243 123 456 792',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => 'alice-kabila-' . uniqid(),
                'response_date' => Carbon::now()->subDays(2),
            ],
            [
                'event_id' => $event->id,
                'guest_type_id' => $friendType->id,
                'event_table_id' => $table2->id,
                'first_name' => 'David',
                'last_name' => 'Tshisekedi',
                'email' => 'david.tshisekedi@email.com',
                'phone' => '+243 123 456 793',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Halal',
                'unique_url' => 'david-tshisekedi-' . uniqid(),
                'response_date' => Carbon::now()->subDays(1),
            ],
            [
                'event_id' => $event->id,
                'guest_type_id' => $friendType->id,
                'event_table_id' => $table2->id,
                'first_name' => 'Grace',
                'last_name' => 'Mobutu',
                'email' => 'grace.mobutu@email.com',
                'phone' => '+243 123 456 794',
                'rsvp_status' => 'declined',
                'meal_choice' => null,
                'unique_url' => 'grace-mobutu-' . uniqid(),
                'response_date' => Carbon::now()->subDays(4),
            ],
            // Collègues
            [
                'event_id' => $event->id,
                'guest_type_id' => $colleagueType->id,
                'event_table_id' => $table3->id,
                'first_name' => 'Marc',
                'last_name' => 'Lumumba',
                'email' => 'marc.lumumba@email.com',
                'phone' => '+243 123 456 795',
                'rsvp_status' => 'confirmed',
                'meal_choice' => 'Standard',
                'unique_url' => 'marc-lumumba-' . uniqid(),
                'response_date' => Carbon::now()->subDays(6),
            ],
            [
                'event_id' => $event->id,
                'guest_type_id' => $colleagueType->id,
                'event_table_id' => $table3->id,
                'first_name' => 'Julie',
                'last_name' => 'Kasavubu',
                'email' => 'julie.kasavubu@email.com',
                'phone' => '+243 123 456 796',
                'rsvp_status' => 'pending',
                'meal_choice' => null,
                'unique_url' => 'julie-kasavubu-' . uniqid(),
                'response_date' => null,
            ],
        ];

        foreach ($guests as $guestData) {
            Guest::create($guestData);
        }

        // Créer des boissons pour l'événement
        $drinks = [
            [
                'name' => 'Coca-Cola',
                'type' => 'soft',
                'alcoholic' => false,
                'volume_ml' => 330,
                'unit' => 'can',
                'active' => true,
            ],
            [
                'name' => 'Jus d\'Orange',
                'type' => 'juice',
                'alcoholic' => false,
                'volume_ml' => 250,
                'unit' => 'glass',
                'active' => true,
            ],
            [
                'name' => 'Champagne',
                'type' => 'wine',
                'alcoholic' => true,
                'volume_ml' => 750,
                'unit' => 'bottle',
                'active' => true,
            ],
            [
                'name' => 'Eau Minérale',
                'type' => 'water',
                'alcoholic' => false,
                'volume_ml' => 500,
                'unit' => 'bottle',
                'active' => true,
            ],
        ];

        foreach ($drinks as $drinkData) {
            $drink = Drink::firstOrCreate([
                'name' => $drinkData['name']
            ], $drinkData);

            // Associer la boisson à l'événement avec prix personnalisé
            EventDrink::create([
                'event_id' => $event->id,
                'drink_id' => $drink->id,
                'price' => $drink->name === 'Coca-Cola' ? 500 : 
                          ($drink->name === 'Jus d\'Orange' ? 800 : 
                          ($drink->name === 'Champagne' ? 15000 : 300)),
                'limit_per_guest' => 3,
                'available' => true,
            ]);
        }

        // Créer du contenu pour l'événement
        $content = Content::create([
            'event_id' => $event->id,
            'unique_code' => 'anniversaire-marie-30',
            'couple' => 'Marie Mukendi',
            'epoux' => null,
            'epouse' => 'Marie Mukendi',
            'Famille_epoux' => null,
            'Famille_epouse' => 'Famille Mukendi',
            'event_datetime' => $event->event_date,
            'timezone' => 'Africa/Kinshasa',
            'venue_name' => 'Restaurant Le Jardin',
            'venue_address_line1' => 'Avenue du 30 Juin',
            'venue_address_line2' => 'Quartier Gombe',
            'venue_city' => 'Kinshasa',
            'venue_region' => 'Kinshasa',
            'venue_country' => 'République Démocratique du Congo',
            'google_maps_url' => 'https://maps.google.com/?q=Restaurant+Le+Jardin+Kinshasa',
            'intro_1' => '🎉 Célébration spéciale 🎉',
            'intro_2' => '💫 Anniversaire de Marie 💫',
            'body_html' => '<p>C\'est avec une immense joie que nous vous invitons à célébrer le <strong>30ème anniversaire de Marie</strong> !</p><p>Rejoignez-nous pour une soirée inoubliable remplie de joie, de rires et de bons moments partagés.</p><p>Votre présence rendra cette célébration encore plus spéciale !</p>',
            'schedule' => json_encode([
                '19:00' => 'Accueil des invités',
                '19:30' => 'Cocktail d\'ouverture',
                '20:00' => 'Dîner',
                '21:30' => 'Coupure du gâteau',
                '22:00' => 'Soirée dansante',
                '00:00' => 'Fin de la soirée'
            ]),
            'cta' => json_encode([
                'rsvp' => ['enabled' => true, 'label' => 'Confirmer ma présence 🎉'],
                'map' => ['enabled' => true, 'label' => 'Voir sur la carte 📍'],
                'download' => ['enabled' => true, 'label' => 'Télécharger l\'invitation 📄']
            ]),
            'guestbook_enabled' => true,
            'guestbook_title' => 'Livre d\'Or de Marie',
            'guestbook_subtitle' => 'Laissez un message pour Marie',
            'drinks_enabled' => true,
            'drinks' => json_encode([1, 2, 3, 4]), // IDs des boissons
            'meta_title' => 'Anniversaire de Marie - 30 ans',
            'meta_description' => 'Célébration du 30ème anniversaire de Marie avec famille et amis',
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);

        $this->command->info("✅ Événement '{$event->title}' créé avec succès !");
        $this->command->info("👥 {$event->guests->count()} invités ajoutés");
        $this->command->info("🍷 {$event->eventDrinks->count()} boissons configurées");
        $this->command->info("📝 Contenu personnalisé créé");
        $this->command->info("🎯 Tables d'événement configurées");
    }
}
