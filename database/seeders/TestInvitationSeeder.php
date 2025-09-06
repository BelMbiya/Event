<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invitation;
use App\Models\Event;
use App\Models\Content;
use Illuminate\Support\Str;

class TestInvitationSeeder extends Seeder
{
    public function run()
    {
        // Récupérer le premier événement
        $event = Event::first();
        
        if ($event) {
            // Créer une invitation de test
            $invitation = Invitation::create([
                'event_id' => $event->id,
                'unique_code' => 'test-' . time(),
                'status' => 'pending',
                'invitation_url' => 'http://localhost:8000/invitation/test-' . time(),
                'sent_at' => null,
            ]);

            // Créer un contenu pour cette invitation
            Content::create([
                'invitation_id' => $invitation->id,
                'event_id' => $event->id,
                'guest_id' => null,
                'unique_code' => $invitation->unique_code,
                'slug' => Str::slug('test-demo-' . $invitation->unique_code),
                'locale' => 'fr',
                'couple' => 'Test & Demo',
                'event_datetime' => $event->event_date,
                'timezone' => 'Africa/Kinshasa',
                'status' => 'published',
                'version' => 1,
                // Champs de lieu avec valeurs par défaut
                'venue_name' => 'Salle de Test',
                'venue_address_line1' => '',
                'venue_address_line2' => '',
                'venue_city' => 'Kinshasa',
                'venue_region' => '',
                'venue_country' => 'RDC',
                'google_maps_url' => '',
                'venue_lat' => null,
                'venue_lng' => null,
                // Champs de contenu avec valeurs par défaut
                'intro_1' => '🧪 Test de l\'invitation 🧪',
                'intro_2' => '💌 Invitation de test 💌',
                'body_html' => '<p>Ceci est une invitation de test pour vérifier le bon fonctionnement de l\'application.</p>',
                'hero_image_path' => null,
                'hero_image_alt' => '',
                'gallery' => null,
                'og_image' => null,
                // Champs de fonctionnalités avec valeurs par défaut
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
                'schedule' => null,
                'meta_title' => 'Test & Demo - Invitation',
                'meta_description' => 'Invitation de test pour l\'application',
                'published_at' => now(),
            ]);

            echo "Invitation de test créée avec l'ID: " . $invitation->id . "\n";
        } else {
            echo "Aucun événement trouvé pour créer une invitation de test.\n";
        }
    }
}
