<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content;
use Illuminate\Support\Facades\DB;

class FixNullFieldsSeeder extends Seeder
{
    public function run()
    {
        echo "🔧 Correction des champs NULL dans la table contents...\n";
        
        // Données par défaut
        $defaultTheme = json_encode([
            'colors' => ['primary' => '#e11d48', 'secondary' => '#f43f5e', 'accent' => '#fb7185'],
            'fonts' => ['headings' => 'Alex Brush', 'body' => 'Cormorant Garamond'],
            'decorations' => ['floral' => true, 'overlay' => true, 'parallax' => false]
        ]);
        
        $defaultCta = json_encode([
            "rsvp" => ["enabled" => true, "label" => "Confirmer ma présence 💌"],
            "map" => ["enabled" => true, "label" => "Voir sur la carte 📍"],
            "download" => ["enabled" => true, "label" => "Télécharger l'invitation (PDF)"]
        ]);
        
        $defaultDrinks = json_encode([
            'enabled' => true,
            'title' => 'Choisissez vos boissons',
            'submit_label' => 'Valider mes choix'
        ]);
        
        // Récupérer tous les contenus avec des champs NULL
        $contents = Content::where(function($query) {
            $query->whereNull('venue_name')
                  ->orWhereNull('venue_address_line1')
                  ->orWhereNull('venue_city')
                  ->orWhereNull('venue_region')
                  ->orWhereNull('venue_country')
                  ->orWhereNull('google_maps_url')
                  ->orWhereNull('intro_1')
                  ->orWhereNull('intro_2')
                  ->orWhereNull('body_html')
                  ->orWhereNull('hero_image_alt')
                  ->orWhereNull('guestbook_title')
                  ->orWhereNull('guestbook_subtitle')
                  ->orWhereNull('meta_title')
                  ->orWhereNull('meta_description')
                  ->orWhereNull('theme')
                  ->orWhereNull('cta')
                  ->orWhereNull('drinks');
        })->get();
        
        $updatedCount = 0;
        
        foreach ($contents as $content) {
            $updateData = [];
            
            // Corriger les champs de lieu
            if (is_null($content->venue_name)) $updateData['venue_name'] = '';
            if (is_null($content->venue_address_line1)) $updateData['venue_address_line1'] = '';
            if (is_null($content->venue_address_line2)) $updateData['venue_address_line2'] = '';
            if (is_null($content->venue_city)) $updateData['venue_city'] = '';
            if (is_null($content->venue_region)) $updateData['venue_region'] = '';
            if (is_null($content->venue_country)) $updateData['venue_country'] = '';
            if (is_null($content->google_maps_url)) $updateData['google_maps_url'] = '';
            
            // Corriger les champs de contenu
            if (is_null($content->intro_1)) $updateData['intro_1'] = 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous';
            if (is_null($content->intro_2)) $updateData['intro_2'] = 'Nous avons le plaisir de vous inviter à partager ce moment spécial';
            if (is_null($content->body_html)) $updateData['body_html'] = '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>';
            if (is_null($content->hero_image_alt)) $updateData['hero_image_alt'] = '';
            
            // Corriger les champs de fonctionnalités
            if (is_null($content->guestbook_title)) $updateData['guestbook_title'] = 'Livre d\'or';
            if (is_null($content->guestbook_subtitle)) $updateData['guestbook_subtitle'] = 'Laissez-nous un message';
            if (is_null($content->meta_title)) $updateData['meta_title'] = ($content->couple ?: 'Invitation') . ' - Invitation';
            if (is_null($content->meta_description)) $updateData['meta_description'] = 'Invitation pour l\'événement de ' . ($content->couple ?: 'nos amis');
            
            // Corriger les champs JSON
            if (is_null($content->theme)) $updateData['theme'] = $defaultTheme;
            if (is_null($content->cta)) $updateData['cta'] = $defaultCta;
            if (is_null($content->drinks)) $updateData['drinks'] = $defaultDrinks;
            
            // Mettre à jour si nécessaire
            if (!empty($updateData)) {
                $content->update($updateData);
                $updatedCount++;
                echo "✅ Contenu ID {$content->id} mis à jour\n";
            }
        }
        
        echo "🎉 Correction terminée ! {$updatedCount} contenus mis à jour.\n";
    }
}
