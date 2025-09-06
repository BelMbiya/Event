<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content;
use App\Models\Invitation;
use App\Models\Event;
use App\Models\Guest;

class AdvancedContentSeeder extends Seeder
{
    public function run()
    {
        // Créer des contenus avec différents thèmes et styles
        $themes = [
            [
                'name' => 'Romantique Rose',
                'colors' => [
                    'primary' => '#e91e63',
                    'secondary' => '#f8bbd9',
                    'accent' => '#ffcdd2'
                ],
                'fonts' => [
                    'headings' => 'Dancing Script',
                    'body' => 'Playfair Display'
                ]
            ],
            [
                'name' => 'Élégant Or',
                'colors' => [
                    'primary' => '#ffd700',
                    'secondary' => '#ffed4e',
                    'accent' => '#fff59d'
                ],
                'fonts' => [
                    'headings' => 'Great Vibes',
                    'body' => 'Crimson Text'
                ]
            ],
            [
                'name' => 'Moderne Bleu',
                'colors' => [
                    'primary' => '#2196f3',
                    'secondary' => '#64b5f6',
                    'accent' => '#90caf9'
                ],
                'fonts' => [
                    'headings' => 'Montserrat',
                    'body' => 'Open Sans'
                ]
            ]
        ];

        $couples = [
            ['couple' => 'Marie & Jean', 'intro' => '💕 Notre union sacrée 💕'],
            ['couple' => 'Sophie & Pierre', 'intro' => '🌟 Le grand jour arrive 🌟'],
            ['couple' => 'Emma & David', 'intro' => '💍 Célébrons notre amour 💍']
        ];

        $venues = [
            [
                'name' => 'Château de Versailles',
                'address' => 'Place d\'Armes',
                'city' => 'Versailles',
                'country' => 'France'
            ],
            [
                'name' => 'Hôtel InterContinental',
                'address' => 'Avenue de la Paix',
                'city' => 'Kinshasa',
                'country' => 'RDC'
            ],
            [
                'name' => 'Villa des Roses',
                'address' => 'Route de la Côte',
                'city' => 'Cannes',
                'country' => 'France'
            ]
        ];

        $invitations = Invitation::with(['event', 'guest'])->get();

        foreach ($invitations as $index => $invitation) {
            $theme = $themes[$index % count($themes)];
            $couple = $couples[$index % count($couples)];
            $venue = $venues[$index % count($venues)];

            // Mettre à jour le contenu existant ou en créer un nouveau
            $content = Content::updateOrCreate(
                ['invitation_id' => $invitation->id],
                [
                    'event_id' => $invitation->event_id,
                    'guest_id' => $invitation->guest_id,
                    'unique_code' => $invitation->unique_code,
                    'locale' => 'fr',
                    'couple' => $couple['couple'],
                    'event_datetime' => $invitation->event->event_date ?? now()->addDays(30),
                    'timezone' => 'Europe/Paris',
                    
                    // Lieu personnalisé
                    'venue_name' => $venue['name'],
                    'venue_address_line1' => $venue['address'],
                    'venue_city' => $venue['city'],
                    'venue_country' => $venue['country'],
                    'google_maps_url' => 'https://maps.google.com/?q=' . urlencode($venue['city']),
                    
                    // Textes personnalisés
                    'intro_1' => $couple['intro'],
                    'intro_2' => '💌 Invitation personnalisée 💌',
                    'body_html' => '<p>C\'est avec une immense joie et beaucoup d\'amour que nous vous invitons à célébrer notre union sacrée.</p><p>Rejoignez-nous pour partager ce moment unique de bonheur, d\'émotion et de fête, entourés de ceux qui nous sont chers.</p><p>Avec toute notre affection,<br><strong>' . $couple['couple'] . '</strong></p>',
                    
                    // Programme varié
                    'schedule' => json_encode([
                        '14:00' => 'Cérémonie religieuse',
                        '15:30' => 'Séance photos',
                        '16:30' => 'Cocktail de bienvenue',
                        '18:00' => 'Réception et dîner',
                        '20:00' => 'Ouverture du bal',
                        '22:00' => 'Soirée dansante',
                        '01:00' => 'Fin des festivités'
                    ]),
                    
                    // Thème personnalisé
                    'theme' => json_encode($theme),
                    
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
                        'title' => 'Choisissez vos boissons préférées',
                        'submit_label' => 'Valider mes choix de boissons'
                    ]),
                    
                    // SEO
                    'meta_title' => 'Invitation Mariage - ' . $couple['couple'],
                    'meta_description' => 'Vous êtes cordialement invité(e) au mariage de ' . $couple['couple'] . '. Rejoignez-nous pour célébrer notre union.',
                    
                    // État
                    'status' => 'published',
                    'published_at' => now(),
                    'version' => 1
                ]
            );
        }
    }
}
