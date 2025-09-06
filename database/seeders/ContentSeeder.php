<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content;
use App\Models\Invitation;
use App\Models\Event;
use App\Models\Guest;

class ContentSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les invitations existantes
        $invitations = Invitation::with(['event', 'guest'])->get();

        foreach ($invitations as $invitation) {
            // Créer un contenu personnalisé pour chaque invitation
            $content = Content::create([
                'invitation_id' => $invitation->id,
                'event_id' => $invitation->event_id,
                'guest_id' => $invitation->guest_id,
                'unique_code' => $invitation->unique_code,
                'locale' => 'fr',
                'couple' => 'Bénie & Gloire',
                'event_datetime' => $invitation->event->event_date ?? now()->addDays(30),
                'timezone' => 'Africa/Kinshasa',
                
                // Lieu personnalisé
                'venue_name' => 'Salle des Fêtes de Kinshasa',
                'venue_address_line1' => 'Avenue de la Paix, 123',
                'venue_address_line2' => 'Quartier Matonge',
                'venue_city' => 'Kinshasa',
                'venue_region' => 'Kinshasa',
                'venue_country' => 'RDC',
                'google_maps_url' => 'https://maps.google.com/?q=Kinshasa',
                'venue_lat' => -4.4419,
                'venue_lng' => 15.2663,
                
                // Textes personnalisés
                'intro_1' => '💍 Le grand jour arrive 💍',
                'intro_2' => '💌 Invitation 💌',
                'body_html' => '<p>C\'est avec une immense joie et beaucoup d\'amour que nous vous invitons à célébrer notre union sacrée.</p><p>Rejoignez-nous pour partager ce moment unique de bonheur, d\'émotion et de fête, entourés de ceux qui nous sont chers.</p>',
                
                // Programme
                'schedule' => json_encode([
                    '14:00' => 'Cérémonie religieuse',
                    '16:00' => 'Cocktail de bienvenue',
                    '18:00' => 'Réception et dîner',
                    '22:00' => 'Soirée dansante'
                ]),
                
                // Thème personnalisé
                'theme' => json_encode([
                    'colors' => [
                        'primary' => '#e11d48',
                        'secondary' => '#f43f5e',
                        'accent' => '#fb7185'
                    ],
                    'fonts' => [
                        'headings' => 'Alex Brush',
                        'body' => 'Cormorant Garamond'
                    ],
                    'decorations' => [
                        'floral' => true,
                        'overlay' => true,
                        'parallax' => false
                    ]
                ]),
                
                // CTA personnalisés
                'cta' => json_encode([
                    'rsvp' => [
                        'enabled' => true,
                        'label' => 'Confirmer ma présence 💌'
                    ],
                    'map' => [
                        'enabled' => true,
                        'label' => 'Voir sur la carte 📍'
                    ],
                    'download' => [
                        'enabled' => true,
                        'label' => 'Télécharger l\'invitation (PDF)'
                    ]
                ]),
                
                // Livre d'or
                'guestbook_enabled' => true,
                'guestbook_title' => 'Livre d\'or',
                'guestbook_subtitle' => 'Laissez-nous un mot, une pensée ou une bénédiction pour marquer ce moment unique.',
                
                // Boissons
                'drinks_enabled' => true,
                'drinks' => json_encode([
                    'title' => 'Choisissez vos boissons',
                    'submit_label' => 'Valider mes choix'
                ]),
                
                // SEO
                'meta_title' => 'Invitation Mariage - Bénie & Gloire',
                'meta_description' => 'Vous êtes cordialement invité(e) au mariage de Bénie & Gloire. Rejoignez-nous pour célébrer notre union.',
                
                // État
                'status' => 'published',
                'published_at' => now(),
                'version' => 1
            ]);
        }
    }
}
