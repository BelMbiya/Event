<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invitation;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Content;
use Illuminate\Support\Str;

/**
 * ========================================
 * INVITATION SEEDER - CRÉATION D'INVITATIONS COMPLÈTES
 * ========================================
 * 
 * Ce seeder crée des invitations complètes avec tous les types :
 * - Invitations dynamiques (une par événement)
 * - Invitations individuelles (legacy)
 * - Contenu personnalisé pour chaque type
 */
class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            $this->command->info("🎯 Création d'invitations pour l'événement : {$event->title}");

            // Créer une invitation dynamique (générale) pour l'événement
            $this->createDynamicInvitation($event);
            
            // Créer des invitations individuelles (legacy) pour quelques invités
            $this->createLegacyInvitations($event);
        }
    }

    /**
     * Créer une invitation dynamique (générale) pour l'événement
     */
    private function createDynamicInvitation($event)
    {
        // Vérifier s'il existe déjà une invitation générale pour cet événement
        $existingInvitation = Invitation::where('event_id', $event->id)
            ->whereNull('guest_id')
            ->first();

        if ($existingInvitation) {
            $this->command->warn("⚠️  Une invitation générale existe déjà pour cet événement (ID: {$existingInvitation->id})");
            return;
        }

        // Créer une invitation générale pour l'événement
        $eventUuid = Str::uuid();
        $invitation = Invitation::create([
            'event_id' => $event->id,
            'guest_id' => null, // Invitation générale
            'unique_code' => $eventUuid,
            'status' => 'sent',
            'invitation_url' => url('/invitation/dynamic'),
            'sent_at' => now()->subDays(5),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info("✅ Invitation dynamique créée :");
        $this->command->info("   - ID: {$invitation->id}");
        $this->command->info("   - Code unique: {$invitation->unique_code}");
        $this->command->info("   - Statut: {$invitation->status}");

        // Créer le contenu de l'invitation
        $this->createInvitationContent($event, $invitation);

        // Générer les liens dynamiques pour tous les invités
        $this->generateDynamicLinks($event);
    }

    /**
     * Créer des invitations individuelles (legacy) pour quelques invités
     */
    private function createLegacyInvitations($event)
    {
        $guests = Guest::where('event_id', $event->id)->take(3)->get();
        
        if ($guests->isEmpty()) {
            $this->command->warn("⚠️  Aucun invité trouvé pour cet événement.");
            return;
        }

        $this->command->info("👥 Création d'invitations individuelles pour " . $guests->count() . " invité(s)");

        $invitationStatuses = [
            ['status' => 'sent', 'sent_at' => now()->subDays(5)],
            ['status' => 'opened', 'sent_at' => now()->subDays(3), 'opened_at' => now()->subDays(2)],
            ['status' => 'responded', 'sent_at' => now()->subDays(7), 'opened_at' => now()->subDays(6)],
        ];

        foreach ($guests as $index => $guest) {
            // Vérifier si l'invitation existe déjà
            $existingInvitation = Invitation::where('event_id', $event->id)
                ->where('guest_id', $guest->id)
                ->first();

            if ($existingInvitation) {
                $this->command->warn("⚠️  Invitation existe déjà pour {$guest->first_name} {$guest->last_name}");
                continue;
            }

            // Créer l'invitation individuelle
            $guestUuid = Str::uuid();
            $statusData = $invitationStatuses[$index % count($invitationStatuses)];
            
            $invitation = Invitation::create([
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'unique_code' => $guestUuid,
                'status' => $statusData['status'],
                'invitation_url' => url("/invitation/{$guestUuid}"),
                'sent_at' => $statusData['sent_at'] ?? null,
                'opened_at' => $statusData['opened_at'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Créer le contenu pour cette invitation
            $this->createGuestContent($event, $invitation, $guest);

            $this->command->info("✅ Invitation individuelle créée pour {$guest->first_name} {$guest->last_name} (ID: {$invitation->id})");
        }
    }

    /**
     * Créer le contenu de l'invitation dynamique
     */
    private function createInvitationContent($event, $invitation)
    {
        $contentUuid = Str::uuid();
        
        // Données par défaut pour le contenu
        $defaultTheme = json_encode([
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
        ]);
        
        $defaultCta = json_encode([
            "rsvp" => [
                "enabled" => true,
                "label" => "Confirmer ma présence 💌"
            ],
            "map" => [
                "enabled" => true,
                "label" => "Voir sur la carte 📍"
            ],
            "download" => [
                "enabled" => true,
                "label" => "Télécharger l'invitation (PDF)"
            ]
        ]);
        
        $defaultDrinks = json_encode([
            'enabled' => true,
            'title' => 'Choisissez vos boissons',
            'submit_label' => 'Valider mes choix'
        ]);

        $schedule = json_encode([
            'ceremonie' => [
                'time' => '14:00',
                'event' => 'Cérémonie religieuse',
                'location' => 'Chapelle du Château'
            ],
            'cocktail' => [
                'time' => '15:30',
                'event' => 'Cocktail de bienvenue',
                'location' => 'Jardins du Château'
            ],
            'diner' => [
                'time' => '18:00',
                'event' => 'Dîner de gala',
                'location' => 'Grande Salle des Fêtes'
            ],
            'soiree' => [
                'time' => '21:00',
                'event' => 'Soirée dansante',
                'location' => 'Salle de bal'
            ]
        ]);
        
        $content = Content::create([
            'invitation_id' => $invitation->id,
            'event_id' => $event->id,
            'guest_id' => null, // Contenu général de l'événement
            'unique_code' => $contentUuid,
            'slug' => Str::slug($event->title . '-' . $contentUuid),
            'locale' => 'fr',
            'couple' => 'Marie & Jean',
            'event_datetime' => $event->event_date,
            'timezone' => 'Europe/Paris',
            'status' => 'published',
            'version' => 1,
            // Champs de lieu
            'venue_name' => $event->location,
            'venue_address_line1' => 'Château de Versailles',
            'venue_address_line2' => 'Place d\'Armes',
            'venue_city' => 'Versailles',
            'venue_region' => 'Île-de-France',
            'venue_country' => 'France',
            'google_maps_url' => $event->google_maps_url,
            'venue_lat' => 48.8049,
            'venue_lng' => 2.1204,
            // Champs de contenu
            'intro_1' => 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous',
            'intro_2' => 'Nous avons le plaisir de vous inviter à partager ce moment si spécial de notre vie',
            'body_html' => '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p><p>Votre présence à nos côtés rendra ce jour encore plus magique.</p>',
            'hero_image_path' => null,
            'gallery' => null,
            'og_image' => null,
            // Champs de fonctionnalités
            'guestbook_enabled' => true,
            'guestbook_title' => 'Livre d\'or',
            'guestbook_subtitle' => 'Laissez-nous un message de félicitations',
            'drinks_enabled' => true,
            'drinks' => $defaultDrinks,
            'cta' => $defaultCta,
            'theme' => $defaultTheme,
            'schedule' => $schedule,
            'meta_title' => $event->title . ' - Invitation',
            'meta_description' => 'Invitation pour le mariage de Marie & Jean - ' . $event->location,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info("✅ Contenu de l'invitation créé :");
        $this->command->info("   - ID: {$content->id}");
        $this->command->info("   - Couple: {$content->couple}");
        $this->command->info("   - Lieu: {$content->venue_name}");
        $this->command->info("   - Date: {$content->event_datetime}");

        return $content;
    }

    /**
     * Créer le contenu pour une invitation d'invité
     */
    private function createGuestContent($event, $invitation, $guest)
    {
        $contentUuid = Str::uuid();
        
        // Données par défaut pour le contenu
        $defaultTheme = json_encode([
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
        ]);
        
        $defaultCta = json_encode([
            "rsvp" => [
                "enabled" => true,
                "label" => "Confirmer ma présence 💌"
            ],
            "map" => [
                "enabled" => true,
                "label" => "Voir sur la carte 📍"
            ],
            "download" => [
                "enabled" => true,
                "label" => "Télécharger l'invitation (PDF)"
            ]
        ]);
        
        $defaultDrinks = json_encode([
            'enabled' => true,
            'title' => 'Choisissez vos boissons',
            'submit_label' => 'Valider mes choix'
        ]);

        $schedule = json_encode([
            'ceremonie' => [
                'time' => '14:00',
                'event' => 'Cérémonie religieuse',
                'location' => 'Chapelle du Château'
            ],
            'cocktail' => [
                'time' => '15:30',
                'event' => 'Cocktail de bienvenue',
                'location' => 'Jardins du Château'
            ],
            'diner' => [
                'time' => '18:00',
                'event' => 'Dîner de gala',
                'location' => 'Grande Salle des Fêtes'
            ],
            'soiree' => [
                'time' => '21:00',
                'event' => 'Soirée dansante',
                'location' => 'Salle de bal'
            ]
        ]);
        
        $content = Content::create([
            'invitation_id' => $invitation->id,
            'event_id' => $event->id,
            'guest_id' => $guest->id, // Contenu spécifique à cet invité
            'unique_code' => $contentUuid,
            'slug' => Str::slug($event->title . '-' . $guest->first_name . '-' . $guest->last_name),
            'locale' => 'fr',
            'couple' => 'Marie & Jean',
            'event_datetime' => $event->event_date,
            'timezone' => 'Europe/Paris',
            'status' => 'published',
            'version' => 1,
            // Champs de lieu
            'venue_name' => $event->location,
            'venue_address_line1' => 'Château de Versailles',
            'venue_address_line2' => 'Place d\'Armes',
            'venue_city' => 'Versailles',
            'venue_region' => 'Île-de-France',
            'venue_country' => 'France',
            'google_maps_url' => $event->google_maps_url,
            'venue_lat' => 48.8049,
            'venue_lng' => 2.1204,
            // Champs de contenu personnalisés
            'intro_1' => "Cher(e) {$guest->first_name}, c'est avec une immense joie que nous vous invitons à célébrer avec nous",
            'intro_2' => "Nous avons le plaisir de vous inviter à partager ce moment si spécial de notre vie",
            'body_html' => "<p>Cher(e) {$guest->first_name}, c'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p><p>Votre présence à nos côtés rendra ce jour encore plus magique.</p>",
            'hero_image_path' => null,
            'gallery' => null,
            'og_image' => null,
            // Champs de fonctionnalités
            'guestbook_enabled' => true,
            'guestbook_title' => 'Livre d\'or',
            'guestbook_subtitle' => 'Laissez-nous un message de félicitations',
            'drinks_enabled' => true,
            'drinks' => $defaultDrinks,
            'cta' => $defaultCta,
            'theme' => $defaultTheme,
            'schedule' => $schedule,
            'meta_title' => "Mariage de Marie & Jean - Invitation pour {$guest->first_name}",
            'meta_description' => "Invitation personnalisée pour {$guest->first_name} {$guest->last_name} - Mariage de Marie & Jean",
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $content;
    }

    /**
     * Générer les liens dynamiques pour tous les invités
     */
    private function generateDynamicLinks($event)
    {
        $guests = Guest::where('event_id', $event->id)->get();
        
        $this->command->info("🔗 Liens dynamiques générés :");
        $this->command->info("=============================");
        
        foreach ($guests as $guest) {
            $dynamicUrl = url("/invitation/{$guest->id}");
            $whatsappUrl = "https://wa.me/?text=" . urlencode("Vous êtes invité au mariage de Marie & Jean ! Cliquez ici : {$dynamicUrl}");
            
            $this->command->info("👤 {$guest->first_name} {$guest->last_name}:");
            $this->command->info("   🔗 Lien: {$dynamicUrl}");
            $this->command->info("   📱 WhatsApp: {$whatsappUrl}");
            $this->command->info("");
        }

        $this->command->info("📊 Total: " . $guests->count() . " liens générés");
    }
}
