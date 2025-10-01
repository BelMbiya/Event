<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use App\Models\Guest;
use App\Models\GuestType;
use App\Models\EventTable;
use App\Models\Drink;
use App\Models\EventDrink;
use App\Models\Content;
use Carbon\Carbon;

/**
 * ========================================
 * EVENT SEEDER - CRÉATION D'ÉVÉNEMENTS COMPLETS
 * ========================================
 * 
 * Ce seeder crée des événements complets avec tous les éléments nécessaires :
 * - Types d'événements
 * - Événements avec invités
 * - Tables d'événements
 * - Boissons et choix
 * - Contenu personnalisé
 */
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

        // Créer des types d'invités
        $familyType = GuestType::firstOrCreate(['label' => 'Famille']);
        $friendType = GuestType::firstOrCreate(['label' => 'Ami(e)']);
        $colleagueType = GuestType::firstOrCreate(['label' => 'Collègue']);

        // Créer des boissons de base
        $this->createBaseDrinks();

        // Créer les événements
        $this->createWeddingEvent($familyType, $friendType);
        $this->createBirthdayEvent($familyType, $friendType, $colleagueType);
        $this->createBaptismEvent($familyType);
    }

    /**
     * Créer des boissons de base
     */
    private function createBaseDrinks()
    {
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
            Drink::firstOrCreate(['name' => $drinkData['name']], $drinkData);
        }
    }

    /**
     * Créer un événement de mariage
     */
    private function createWeddingEvent($familyType, $friendType)
    {
        $event = Event::create([
            'organizer_id' => 1,
            'title' => 'Mariage de Marie & Jean - Test Dynamique',
            'description' => 'Cérémonie de mariage de Marie et Jean avec test de la nouvelle logique d\'invitations dynamiques.',
            'event_date' => Carbon::now()->addDays(45)->format('Y-m-d H:i:s'),
            'location' => 'Château de Versailles, Salle des Fêtes',
            'google_maps_url' => 'https://maps.google.com/?q=Château+de+Versailles',
            'program' => json_encode([
                'ceremonie' => ['time' => '14:00', 'event' => 'Cérémonie religieuse', 'location' => 'Chapelle du Château'],
                'cocktail' => ['time' => '15:30', 'event' => 'Cocktail de bienvenue', 'location' => 'Jardins du Château'],
                'diner' => ['time' => '18:00', 'event' => 'Dîner de gala', 'location' => 'Grande Salle des Fêtes'],
                'soiree' => ['time' => '21:00', 'event' => 'Soirée dansante', 'location' => 'Salle de bal']
            ]),
            'theme_color' => '#e11d48',
            'event_type_id' => EventType::where('label', 'Mariage')->first()->id,
        ]);

        // Créer des invités pour le mariage
        $this->createWeddingGuests($event, $familyType, $friendType);
        
        // Créer des boissons pour l'événement
        $this->createEventDrinks($event);
    }

    /**
     * Créer un événement d'anniversaire
     */
    private function createBirthdayEvent($familyType, $friendType, $colleagueType)
    {
        $event = Event::firstOrCreate(
            ['title' => 'Anniversaire de Marie - 30 ans'],
            [
                'organizer_id' => 2,
                'description' => 'Célébration du 30ème anniversaire de Marie avec famille et amis',
                'event_date' => Carbon::now()->addDays(30)->setTime(19, 0, 0),
                'location' => 'Restaurant Le Jardin, Kinshasa',
                'google_maps_url' => 'https://maps.google.com/?q=Restaurant+Le+Jardin+Kinshasa',
                'program' => 'Accueil des invités à 19h00, Cocktail d\'ouverture à 19h30, Dîner à 20h00, Coupure du gâteau à 21h30, Soirée dansante à 22h00',
                'theme_color' => '#ff6b6b',
                'event_type_id' => EventType::where('label', 'Anniversaire')->first()->id,
            ]
        );

        // Créer des tables pour l'anniversaire
        $this->createEventTables($event);
        
        // Créer des invités pour l'anniversaire
        $this->createBirthdayGuests($event, $familyType, $friendType, $colleagueType);
        
        // Créer des boissons pour l'événement
        $this->createEventDrinks($event);
        
        // Créer du contenu pour l'événement
        $this->createEventContent($event);
    }

    /**
     * Créer un événement de baptême
     */
    private function createBaptismEvent($familyType)
    {
        $event = Event::create([
            'organizer_id' => 3,
            'title' => 'Baptême de Lucas',
            'description' => 'Cérémonie de baptême de Lucas Moreau',
            'event_date' => Carbon::now()->addMonth()->format('Y-m-d H:i:s'),
            'location' => 'Église Notre-Dame, 789 Boulevard de la République, Kisangani',
            'google_maps_url' => 'https://maps.google.com/?q=Kisangani',
            'program' => 'Cérémonie à 10h, réception à 12h',
            'theme_color' => '#45B7D1',
            'event_type_id' => EventType::where('label', 'Baptême')->first()->id,
        ]);

        // Créer des invités pour le baptême
        $this->createBaptismGuests($event, $familyType);
    }

    /**
     * Créer des invités pour le mariage
     */
    private function createWeddingGuests($event, $familyType, $friendType)
    {
        $guests = [
            ['first_name' => 'Pierre', 'last_name' => 'Dupont', 'email' => 'pierre.dupont@email.com', 'phone' => '0123456789', 'rsvp_status' => 'pending', 'guest_type_id' => $familyType->id, 'meal_choice' => 'Végétarien'],
            ['first_name' => 'Sophie', 'last_name' => 'Martin', 'email' => 'sophie.martin@email.com', 'phone' => '0987654321', 'rsvp_status' => 'pending', 'guest_type_id' => $familyType->id, 'meal_choice' => 'Standard'],
            ['first_name' => 'Paul', 'last_name' => 'Durand', 'email' => 'paul.durand@email.com', 'phone' => '0555666777', 'rsvp_status' => 'pending', 'guest_type_id' => $friendType->id, 'meal_choice' => 'Sans gluten'],
            ['first_name' => 'Emma', 'last_name' => 'Leroy', 'email' => 'emma.leroy@email.com', 'phone' => '0444555666', 'rsvp_status' => 'pending', 'guest_type_id' => $friendType->id, 'meal_choice' => 'Standard'],
            ['first_name' => 'Lucas', 'last_name' => 'Moreau', 'email' => 'lucas.moreau@email.com', 'phone' => '0333444555', 'rsvp_status' => 'pending', 'guest_type_id' => $friendType->id, 'meal_choice' => 'Halal'],
        ];

        foreach ($guests as $guestData) {
            Guest::create([
                'event_id' => $event->id,
                'guest_type_id' => $guestData['guest_type_id'],
                'first_name' => $guestData['first_name'],
                'last_name' => $guestData['last_name'],
                'email' => $guestData['email'],
                'phone' => $guestData['phone'],
                'rsvp_status' => $guestData['rsvp_status'],
                'meal_choice' => $guestData['meal_choice'],
            ]);
        }
    }

    /**
     * Créer des tables d'événement
     */
    private function createEventTables($event)
    {
        $tables = [
            ['name' => 'Table Famille', 'table_number' => 1, 'capacity' => 8],
            ['name' => 'Table Amis', 'table_number' => 2, 'capacity' => 6],
            ['name' => 'Table Collègues', 'table_number' => 3, 'capacity' => 4],
        ];

        foreach ($tables as $tableData) {
            EventTable::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'table_number' => $tableData['table_number']
                ],
                [
                    'event_id' => $event->id,
                    'name' => $tableData['name'],
                    'table_number' => $tableData['table_number'],
                    'capacity' => $tableData['capacity']
                ]
            );
        }
    }

    /**
     * Créer des invités pour l'anniversaire
     */
    private function createBirthdayGuests($event, $familyType, $friendType, $colleagueType)
    {
        // Vérifier si des invités existent déjà pour cet événement
        if ($event->guests()->count() > 0) {
            $this->command->warn("⚠️  Des invités existent déjà pour l'événement '{$event->title}'. Ignoré.");
            return;
        }
        
        // Récupérer les tables créées pour cet événement
        $tables = $event->eventTables()->get();
        
        // Vérifier qu'il y a au moins 3 tables
        if ($tables->count() < 3) {
            throw new \Exception("Pas assez de tables créées pour l'événement. Attendu: 3, trouvé: {$tables->count()}");
        }
        
        $guests = [
            // Famille
            ['first_name' => 'Jean', 'last_name' => 'Mukendi', 'email' => 'jean.mukendi@email.com', 'phone' => '+243 123 456 789', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Végétarien', 'guest_type_id' => $familyType->id, 'table_id' => $tables[0]->id],
            ['first_name' => 'Sophie', 'last_name' => 'Mukendi', 'email' => 'sophie.mukendi@email.com', 'phone' => '+243 123 456 790', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Standard', 'guest_type_id' => $familyType->id, 'table_id' => $tables[0]->id],
            ['first_name' => 'Pierre', 'last_name' => 'Mukendi', 'email' => 'pierre.mukendi@email.com', 'phone' => '+243 123 456 791', 'rsvp_status' => 'pending', 'meal_choice' => null, 'guest_type_id' => $familyType->id, 'table_id' => $tables[0]->id],
            // Amis
            ['first_name' => 'Alice', 'last_name' => 'Kabila', 'email' => 'alice.kabila@email.com', 'phone' => '+243 123 456 792', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Standard', 'guest_type_id' => $friendType->id, 'table_id' => $tables[1]->id],
            ['first_name' => 'David', 'last_name' => 'Tshisekedi', 'email' => 'david.tshisekedi@email.com', 'phone' => '+243 123 456 793', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Halal', 'guest_type_id' => $friendType->id, 'table_id' => $tables[1]->id],
            ['first_name' => 'Grace', 'last_name' => 'Mobutu', 'email' => 'grace.mobutu@email.com', 'phone' => '+243 123 456 794', 'rsvp_status' => 'declined', 'meal_choice' => null, 'guest_type_id' => $friendType->id, 'table_id' => $tables[1]->id],
            // Collègues
            ['first_name' => 'Marc', 'last_name' => 'Lumumba', 'email' => 'marc.lumumba@email.com', 'phone' => '+243 123 456 795', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Standard', 'guest_type_id' => $colleagueType->id, 'table_id' => $tables[2]->id],
            ['first_name' => 'Julie', 'last_name' => 'Kasavubu', 'email' => 'julie.kasavubu@email.com', 'phone' => '+243 123 456 796', 'rsvp_status' => 'pending', 'meal_choice' => null, 'guest_type_id' => $colleagueType->id, 'table_id' => $tables[2]->id],
        ];

        foreach ($guests as $guestData) {
            Guest::create([
                'event_id' => $event->id,
                'guest_type_id' => $guestData['guest_type_id'],
                'event_table_id' => $guestData['table_id'],
                'first_name' => $guestData['first_name'],
                'last_name' => $guestData['last_name'],
                'email' => $guestData['email'],
                'phone' => $guestData['phone'],
                'rsvp_status' => $guestData['rsvp_status'],
                'meal_choice' => $guestData['meal_choice'],
                'unique_url' => strtolower($guestData['first_name'] . '-' . $guestData['last_name'] . '-' . uniqid()),
                'response_date' => $guestData['rsvp_status'] === 'confirmed' ? Carbon::now()->subDays(rand(1, 7)) : null,
            ]);
        }
    }

    /**
     * Créer des invités pour le baptême
     */
    private function createBaptismGuests($event, $familyType)
    {
        $guests = [
            ['first_name' => 'Marie', 'last_name' => 'Dubois', 'email' => 'marie.dubois@email.com', 'phone' => '0123456789', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Standard', 'guest_type_id' => $familyType->id],
            ['first_name' => 'Jean', 'last_name' => 'Martin', 'email' => 'jean.martin@email.com', 'phone' => '0987654321', 'rsvp_status' => 'confirmed', 'meal_choice' => 'Standard', 'guest_type_id' => $familyType->id],
        ];

        foreach ($guests as $guestData) {
            Guest::create([
                'event_id' => $event->id,
                'guest_type_id' => $guestData['guest_type_id'],
                'first_name' => $guestData['first_name'],
                'last_name' => $guestData['last_name'],
                'email' => $guestData['email'],
                'phone' => $guestData['phone'],
                'rsvp_status' => $guestData['rsvp_status'],
                'meal_choice' => $guestData['meal_choice'],
            ]);
        }
    }

    /**
     * Créer des boissons pour un événement
     */
    private function createEventDrinks($event)
    {
        $drinks = Drink::all();
        
        foreach ($drinks as $drink) {
            EventDrink::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'drink_id' => $drink->id
                ],
                [
                    'event_id' => $event->id,
                    'drink_id' => $drink->id,
                    'price' => $drink->name === 'Coca-Cola' ? 500 : 
                              ($drink->name === 'Jus d\'Orange' ? 800 : 
                              ($drink->name === 'Champagne' ? 15000 : 300)),
                    'limit_per_guest' => 3,
                    'available' => true,
                ]
            );
        }
    }

    /**
     * Créer du contenu pour un événement
     */
    private function createEventContent($event)
    {
        Content::create([
            'event_id' => $event->id,
            'unique_code' => 'anniversaire-marie-30',
            'couple' => 'Marie Mukendi',
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
            'drinks' => json_encode([1, 2, 3, 4]),
            'meta_title' => 'Anniversaire de Marie - 30 ans',
            'meta_description' => 'Célébration du 30ème anniversaire de Marie avec famille et amis',
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);
    }
}