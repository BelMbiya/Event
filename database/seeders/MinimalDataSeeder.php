<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Guest;
use App\Models\GuestType;
use App\Models\GuestCategory;
use App\Models\Invitation;
use App\Models\Content;
use App\Models\Drink;
use App\Models\EventDrink;
use Illuminate\Support\Str;

class MinimalDataSeeder extends Seeder
{
    /**
     * Seed minimal data for testing
     */
    public function run(): void
    {
        // 1. Créer un utilisateur admin
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Créer un type d'événement
        $eventType = EventType::create([
            'label' => 'Mariage',
        ]);

        // 3. Créer un type d'invité
        $guestType = GuestType::create([
            'label' => 'Famille',
        ]);

        // 4. Créer une catégorie d'invité
        $guestCategory = GuestCategory::create([
            'label' => 'Famille proche',
        ]);

        // 5. Créer un événement
        $event = Event::create([
            'title' => 'Mariage de Marie & Jean',
            'description' => 'Cérémonie de mariage de Marie et Jean',
            'event_date' => now()->addDays(30),
            'location' => 'Kinshasa, RDC',
            'event_type_id' => $eventType->id,
            'organizer_id' => $user->id,
        ]);

        // 6. Créer quelques invités (3 seulement)
        $guests = [];
        $guestNames = [
            ['first_name' => 'Marie', 'last_name' => 'Dupont', 'email' => 'marie@test.com'],
            ['first_name' => 'Jean', 'last_name' => 'Martin', 'email' => 'jean@test.com'],
            ['first_name' => 'Sophie', 'last_name' => 'Bernard', 'email' => 'sophie@test.com'],
        ];

        foreach ($guestNames as $guestData) {
            $guest = Guest::create([
                'first_name' => $guestData['first_name'],
                'last_name' => $guestData['last_name'],
                'email' => $guestData['email'],
                'phone' => '+243 123 456 789',
                'event_id' => $event->id,
                'guest_type_id' => $guestType->id,
                'rsvp_status' => 'pending',
            ]);
            $guests[] = $guest;
        }

        // 7. Créer quelques boissons (3 seulement)
        $drinks = [];
        $drinkData = [
            ['name' => 'Coca-Cola', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'can', 'volume_ml' => 330],
            ['name' => 'Jus d\'orange', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Eau minérale', 'type' => 'water', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 500],
        ];

        foreach ($drinkData as $data) {
            $drink = Drink::create($data);
            $drinks[] = $drink;
        }

        // 8. Associer les boissons à l'événement
        foreach ($drinks as $drink) {
            EventDrink::create([
                'event_id' => $event->id,
                'drink_id' => $drink->id,
                'price' => 1000.00,
                'available' => true,
                'limit_per_guest' => 2,
            ]);
        }

        // 9. Créer des invitations pour chaque invité
        foreach ($guests as $guest) {
            $uuid = Str::uuid();
            $invitation = Invitation::create([
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'unique_code' => $uuid,
                'status' => 'pending',
                'invitation_url' => url('/invitation/' . $uuid),
            ]);

            // 10. Créer le contenu pour chaque invitation
            $contentUuid = Str::uuid();
            Content::create([
                'invitation_id' => $invitation->id,
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'unique_code' => $contentUuid,
                'slug' => Str::slug($event->title . '-' . $contentUuid),
                'locale' => 'fr',
                'couple' => 'Marie & Jean',
                'event_datetime' => $event->event_date,
                'timezone' => 'Africa/Kinshasa',
                'status' => 'draft',
                'version' => 1,
                'venue_name' => 'Salle des fêtes de Kinshasa',
                'venue_address_line1' => 'Avenue de la Paix',
                'venue_city' => 'Kinshasa',
                'venue_country' => 'RDC',
                'intro_1' => 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous',
                'intro_2' => 'Nous avons le plaisir de vous inviter à partager ce moment spécial',
                'body_html' => '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>',
                'guestbook_enabled' => true,
                'guestbook_title' => 'Livre d\'or',
                'guestbook_subtitle' => 'Laissez-nous un message',
                'drinks_enabled' => true,
                'drinks' => json_encode([
                    'enabled' => true,
                    'title' => 'Choisissez vos boissons',
                    'submit_label' => 'Valider mes choix'
                ]),
                'cta' => json_encode([
                    "rsvp" => ["enabled" => true, "label" => "Confirmer ma présence 💌"],
                    "map" => ["enabled" => true, "label" => "Voir sur la carte 📍"],
                    "download" => ["enabled" => true, "label" => "Télécharger l'invitation (PDF)"]
                ]),
                'theme' => json_encode([
                    'colors' => ['primary' => '#e11d48', 'secondary' => '#f43f5e', 'accent' => '#fb7185'],
                    'fonts' => ['headings' => 'Alex Brush', 'body' => 'Cormorant Garamond'],
                    'decorations' => ['floral' => true, 'overlay' => true, 'parallax' => false]
                ]),
                'meta_title' => 'Mariage de Marie & Jean - Invitation',
                'meta_description' => 'Invitation pour le mariage de Marie et Jean',
            ]);
        }

        $this->command->info('✅ Données minimales créées avec succès !');
        $this->command->info('👤 Utilisateur: admin@test.com / password');
        $this->command->info('🎉 Événement: Mariage de Marie & Jean');
        $this->command->info('👥 Invités: 3');
        $this->command->info('🍹 Boissons: 3');
        $this->command->info('💌 Invitations: 3');
    }
}
