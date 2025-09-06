<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contents')->insert([
    //'invitation_id' ,
    'event_id' => 1,
    'Epoux' => 'Gloire',
    'Epouse' => 'Benie',
    'famille_epoux' => 'MUKOKO',
    'famille_epouse' => 'MUTOMBO',
    'content' => 'Nous vous aimons. Vous êtes cordialement invités à célébrer notre union.',
    'guest_id' => null,
    'unique_code' => Str::uuid(),
    'slug' => 'benie-gloire-2025',
    'locale' => 'fr',
    'couple' => 'Bénie & Gloire',
    'event_datetime' => '2025-08-30 14:00:00',
    'timezone' => 'Africa/Kinshasa',
    'venue_name' => 'Salle des Fêtes XYZ',
    'venue_address_line1' => 'Avenue des Orangers 12',
    'venue_city' => 'Kinshasa',
    'venue_region' => 'Lukunga',
    'venue_country' => 'RDC',
    'google_maps_url' => 'https://maps.google.com/?q=-4.325,-15.322',
    'hero_image_path' => 'invitations/benie-gloire/wed_2.jpg',
    'hero_image_alt' => 'Photo romantique de Bénie et Gloire',
    'gallery' => json_encode([
        'invitations/benie-gloire/gal1.jpg',
        'invitations/benie-gloire/gal2.jpg',
    ]),
    'body_html' => "<b>Nous vous aimons</b><br>Vous êtes cordialement invités à célébrer notre union.",
    'intro_1' => "C'est avec une immense joie et beaucoup d'amour que nous vous invitons à célébrer notre union sacrée.",
    'intro_2' => "Rejoignez-nous pour partager ce moment unique de bonheur, d'émotion et de fête, entourés de ceux qui nous sont chers.",
    'schedule' => json_encode([
        'ceremony' => '15:00',
        'reception' => '18:00',
    ]),
    'theme' => json_encode([
        'colors' => [
            'primary' => '#e11d48',
            'secondary' => '#f43f5e',
            'accent' => '#fb7185',
        ],
        'fonts' => [
            'headings' => 'Alex Brush',
            'body' => 'Cormorant Garamond',
        ],
        'decorations' => [
            'floral' => true,
            'overlay' => true,
            'parallax' => false,
        ]
    ]),
    'cta' => json_encode([
        'rsvp' => [
            'enabled' => true,
            'label' => 'Confirmer ma présence 💌',
            'route_name' => 'guests.rsvp',
        ],
        'map' => [
            'enabled' => true,
            'label' => 'Voir sur la carte 📍',
        ],
        'download' => [
            'enabled' => true,
            'label' => 'Télécharger l\'invitation (PDF)',
        ]
    ]),
    'guestbook_enabled' => true,
    'guestbook_title' => 'Livre d\'or',
    'guestbook_subtitle' => 'Laissez-nous un mot, une pensée ou une bénédiction pour marquer ce moment unique.',
    'drinks_enabled' => true,
    'drinks' => json_encode([
        'title' => 'Choisissez vos boissons',
        'submit_label' => 'Valider mes choix'
    ]),
    'status' => 'published',
    'published_at' => now(),
    'meta_title' => 'Bénie & Gloire - Notre Mariage',
    'meta_description' => 'Invitation au mariage de Bénie & Gloire à Kinshasa, le 30 août 2025.',
    'og_image' => 'invitations/benie-gloire/og.jpg',
    'created_at' => now(),
    'updated_at' => now(),
]);

    }
}
