<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;

/**
 * ========================================
 * CONTENT CONTROLLER - GESTION UNIFIÉE DU CONTENU D'INVITATION
 * ========================================
 * 
 * Ce contrôleur gère TOUS les aspects du contenu des invitations :
 * - Création et édition de contenu moderne (système principal)
 * - Gestion des templates et thèmes
 * - Synchronisation entre invitations du même événement
 * - Compatibilité avec l'ancien système (legacy)
 * 
 * FONCTIONNALITÉS PRINCIPALES :
 * - ✅ Édition avancée avec templates
 * - ✅ Synchronisation automatique entre invitations
 * - ✅ Gestion des fichiers (images, galeries)
 * - ✅ Thèmes personnalisables
 * - ✅ Compatibilité legacy
 * 
 * @author Assistant IA - Optimisation et fusion
 * @version 2.0 - Unifié et optimisé
 */
class ContentController extends Controller
{
    public function edit($invitation_id)
    {
        $invitation = Invitation::with(['event', 'guest', 'content'])->findOrFail($invitation_id);
        
        // ✅ CORRECTION : Créer le contenu s'il n'existe pas
        if (!$invitation->content) {
            $content = $this->createDefaultContent($invitation);
        } else {
            $content = $invitation->content;
        }
        
        // Récupérer les templates disponibles
        $templates = [
            'classic' => 'Classique',
            'modern' => 'Moderne', 
            'elegant' => 'Élégant',
            'romantic' => 'Romantique',
            'vintage' => 'Vintage'
        ];
        
        // Récupérer les contenus similaires pour suggestions
        $similarContents = Content::where('event_id', $invitation->event_id)
            ->where('id', '!=', $content->id ?? 0)
            ->limit(5)
            ->get();
        
        // Extraire l'événement de l'invitation
        $event = $invitation->event;
        
        return view('invitation.edit-advanced', compact('invitation', 'content', 'templates', 'similarContents', 'event'));
    }

    public function update(Request $request, $invitation_id)
    {
        $invitation = Invitation::findOrFail($invitation_id);
        
        // ✅ DEBUG : Log des données reçues
        \Log::info("=== MISE À JOUR INVITATION {$invitation_id} ===");
        \Log::info("Données reçues:", $request->all());
        \Log::info("Checkbox sync_to_all_invitations présente: " . ($request->has('sync_to_all_invitations') ? 'true' : 'false'));
        \Log::info("Valeur sync_to_all_invitations: " . $request->input('sync_to_all_invitations'));
        
        // ✅ NOUVEAU : Vérifier si la synchronisation est demandée (amélioré)
        $syncToAllInvitations = $request->has('sync_to_all_invitations') || 
                               $request->input('sync_to_all_invitations') == '1' || 
                               $request->input('sync_to_all_invitations') == 'on' ||
                               $request->input('sync_to_all_invitations') === true;
        \Log::info("Synchronisation activée: " . ($syncToAllInvitations ? 'true' : 'false'));
        
        // Traitement des champs boolean avant validation
        $request->merge([
            'guestbook_enabled' => $request->has('guestbook_enabled') ? true : false,
            'drinks_enabled' => $request->has('drinks_enabled') ? true : false,
            'cta_rsvp_enabled' => $request->has('cta_rsvp_enabled') ? true : false,
            'cta_map_enabled' => $request->has('cta_map_enabled') ? true : false,
            'cta_download_enabled' => $request->has('cta_download_enabled') ? true : false,
            'theme_floral' => $request->has('theme_floral') ? true : false,
            'theme_overlay' => $request->has('theme_overlay') ? true : false,
            'theme_parallax' => $request->has('theme_parallax') ? true : false,
        ]);
        
        // Validation des champs de base
        $validated = $request->validate([
            'couple' => 'nullable|string|max:255',
            'event_datetime' => 'nullable|date',
            'timezone' => 'nullable|string|max:50',
            'venue_name' => 'nullable|string|max:255',
            'venue_address_line1' => 'nullable|string|max:255',
            'venue_address_line2' => 'nullable|string|max:255',
            'venue_city' => 'nullable|string|max:255',
            'venue_region' => 'nullable|string|max:255',
            'venue_country' => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url',
            'venue_lat' => 'nullable|numeric',
            'venue_lng' => 'nullable|numeric',
            'intro_1' => 'nullable|string',
            'intro_2' => 'nullable|string',
            'body_html' => 'nullable|string',
            'program_html' => 'nullable|string',
            'hero_image_path' => 'nullable|file|image|max:2048',
            'hero_image_alt' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|image|max:2048',
            'og_image' => 'nullable|file|image|max:2048',
            'guestbook_enabled' => 'nullable|boolean',
            'guestbook_title' => 'nullable|string|max:255',
            'guestbook_subtitle' => 'nullable|string|max:255',
            'drinks_enabled' => 'nullable|boolean',
            'drinks_title' => 'nullable|string|max:255',
            'drinks_submit_label' => 'nullable|string|max:255',
            'cta_rsvp_enabled' => 'nullable|boolean',
            'cta_rsvp_label' => 'nullable|string|max:255',
            'cta_map_enabled' => 'nullable|boolean',
            'cta_map_label' => 'nullable|string|max:255',
            'cta_download_enabled' => 'nullable|boolean',
            'cta_download_label' => 'nullable|string|max:255',
            'theme_primary_color' => 'nullable|string|max:7',
            'theme_secondary_color' => 'nullable|string|max:7',
            'theme_accent_color' => 'nullable|string|max:7',
            'theme_heading_font' => 'nullable|string|max:255',
            'theme_body_font' => 'nullable|string|max:255',
            'theme_floral' => 'nullable|boolean',
            'theme_overlay' => 'nullable|boolean',
            'theme_parallax' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        // Validation des champs de planning dynamiques
        for ($i = 0; $i < 5; $i++) {
            $request->validate([
                "schedule_time_{$i}" => 'nullable|string|max:10',
                "schedule_event_{$i}" => 'nullable|string|max:255',
            ]);
        }

        DB::beginTransaction();
        
        try {
            // Upload fichiers si présents
            $heroPath = $request->file('hero_image_path')?->store('invitations/hero', 'public');
            $galleryPaths = $request->file('gallery') ? array_map(fn($f) => $f->store('invitations/gallery', 'public'), $request->file('gallery')) : null;
            $ogPath = $request->file('og_image')?->store('invitations/og', 'public');

            // Préparer les données JSON pour le thème
            $themeData = [
                'colors' => [
                    'primary' => $request->input('theme_primary_color', '#e11d48'),
                    'secondary' => $request->input('theme_secondary_color', '#f43f5e'),
                    'accent' => $request->input('theme_accent_color', '#fb7185')
                ],
                'fonts' => [
                    'headings' => $request->input('theme_heading_font', 'Alex Brush'),
                    'body' => $request->input('theme_body_font', 'Cormorant Garamond')
                ],
                'decorations' => [
                    'floral' => $request->has('theme_floral'),
                    'overlay' => $request->has('theme_overlay'),
                    'parallax' => $request->has('theme_parallax')
                ]
            ];
            $themeJson = json_encode($themeData);

            // Préparer les données JSON pour les CTA
            $ctaData = [
                "rsvp" => [
                    "enabled" => $request->has('cta_rsvp_enabled'),
                    "label" => $request->input('cta_rsvp_label', 'Confirmer ma présence 💌')
                ],
                "map" => [
                    "enabled" => $request->has('cta_map_enabled'),
                    "label" => $request->input('cta_map_label', 'Voir sur la carte 📍')
                ],
                "download" => [
                    "enabled" => $request->has('cta_download_enabled'),
                    "label" => $request->input('cta_download_label', 'Télécharger l\'invitation (PDF)')
                ]
            ];
            $ctaJson = json_encode($ctaData);

            // Préparer les données JSON pour les boissons
            $drinksData = [
                'enabled' => $request->has('drinks_enabled'),
                'title' => $request->input('drinks_title', 'Choisissez vos boissons'),
                'submit_label' => $request->input('drinks_submit_label', 'Valider mes choix')
            ];
            $drinksJson = json_encode($drinksData);

            // Préparer les données JSON pour le planning
            $scheduleData = [];
            for ($i = 0; $i < 5; $i++) {
                $time = $request->input("schedule_time_{$i}");
                $event = $request->input("schedule_event_{$i}");
                if ($time && $event) {
                    $scheduleData[] = ['time' => $time, 'event' => $event];
                }
            }
            $scheduleJson = !empty($scheduleData) ? json_encode($scheduleData) : null;

            // Mettre à jour ou créer le contenu
            $content = Content::updateOrCreate(
                ['invitation_id' => $invitation->id],
                array_merge($validated, [
                    'event_id' => $invitation->event_id,
                    'guest_id' => $invitation->guest_id,
                    'unique_code' => $invitation->unique_code,
                    'schedule' => $scheduleJson,
                    'cta' => $ctaJson,
                    'drinks' => $drinksJson,
                    'theme' => $themeJson,
                    'hero_image_path' => $heroPath,
                    'gallery' => $galleryPaths ? json_encode($galleryPaths) : null,
                    'og_image' => $ogPath,
                    'published_at' => $validated['status'] === 'published' ? now() : null,
                ])
            );

            // ✅ NOUVEAU : Synchroniser avec toutes les autres invitations du même événement
            if ($syncToAllInvitations) {
                \Log::info("=== DÉBUT SYNCHRONISATION ===");
                \Log::info("Synchronisation demandée pour l'invitation {$invitation->id} de l'événement {$invitation->event_id}");
                try {
                    $syncCount = $this->syncContentToAllInvitations($content, $invitation->event_id, $invitation->id);
                    \Log::info("Synchronisation terminée avec succès: {$syncCount} invitations mises à jour");
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de la synchronisation: " . $e->getMessage());
                    \Log::error("Stack trace: " . $e->getTraceAsString());
                }
                \Log::info("=== FIN SYNCHRONISATION ===");
            } else {
                \Log::info("Synchronisation non demandée - mise à jour locale uniquement");
            }

            DB::commit();
            
            // Message de succès avec information sur la synchronisation
            $successMessage = 'Contenu de l\'invitation mis à jour avec succès !';
            if ($syncToAllInvitations) {
                $successMessage .= ' Les modifications ont été synchronisées avec toutes les autres invitations de cet événement.';
            }
            
            // Redirection normale côté serveur
            return redirect()->route('invitation.show', ['unique_code' => $invitation->unique_code])
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la mise à jour du contenu: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un contenu d'invitation
     */
    public function duplicate($content_id)
    {
        try {
            $originalContent = Content::findOrFail($content_id);
            $invitation = $originalContent->invitation;
            
            // Créer une nouvelle invitation
            $newInvitation = Invitation::create([
                'event_id' => $invitation->event_id,
                'guest_id' => $invitation->guest_id,
                'unique_code' => $this->generateUniqueCode(),
                'status' => 'pending'
            ]);
            
            // Dupliquer le contenu
            $contentData = $originalContent->toArray();
            unset($contentData['id'], $contentData['created_at'], $contentData['updated_at']);
            $contentData['invitation_id'] = $newInvitation->id;
            $contentData['unique_code'] = $newInvitation->unique_code;
            $contentData['status'] = 'draft';
            
            $newContent = Content::create($contentData);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contenu dupliqué avec succès !',
                    'content_id' => $newContent->id,
                    'invitation_id' => $newInvitation->id
                ]);
            }
            
            return redirect()->route('invitation.content.edit', $newInvitation->id)
                ->with('success', 'Contenu dupliqué avec succès !');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la duplication de contenu: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la duplication: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la duplication: ' . $e->getMessage());
        }
    }

    /**
     * Appliquer un template à un contenu
     */
    public function applyTemplate(Request $request, $content_id)
    {
        try {
            $content = Content::findOrFail($content_id);
            $template = $request->input('template');
            
            $templates = [
                'classic' => [
                    'theme' => json_encode([
                        'colors' => ['primary' => '#2c3e50', 'secondary' => '#34495e', 'accent' => '#3498db'],
                        'fonts' => ['headings' => 'Playfair Display', 'body' => 'Open Sans'],
                        'decorations' => ['floral' => false, 'overlay' => false, 'parallax' => false]
                    ])
                ],
                'modern' => [
                    'theme' => json_encode([
                        'colors' => ['primary' => '#e11d48', 'secondary' => '#f43f5e', 'accent' => '#fb7185'],
                        'fonts' => ['headings' => 'Inter', 'body' => 'Inter'],
                        'decorations' => ['floral' => false, 'overlay' => true, 'parallax' => true]
                    ])
                ],
                'elegant' => [
                    'theme' => json_encode([
                        'colors' => ['primary' => '#8b5cf6', 'secondary' => '#a78bfa', 'accent' => '#c4b5fd'],
                        'fonts' => ['headings' => 'Cormorant Garamond', 'body' => 'Cormorant Garamond'],
                        'decorations' => ['floral' => true, 'overlay' => true, 'parallax' => false]
                    ])
                ],
                'romantic' => [
                    'theme' => json_encode([
                        'colors' => ['primary' => '#ec4899', 'secondary' => '#f472b6', 'accent' => '#fbbf24'],
                        'fonts' => ['headings' => 'Alex Brush', 'body' => 'Cormorant Garamond'],
                        'decorations' => ['floral' => true, 'overlay' => true, 'parallax' => true]
                    ])
                ],
                'vintage' => [
                    'theme' => json_encode([
                        'colors' => ['primary' => '#92400e', 'secondary' => '#b45309', 'accent' => '#d97706'],
                        'fonts' => ['headings' => 'Cinzel', 'body' => 'Crimson Text'],
                        'decorations' => ['floral' => true, 'overlay' => false, 'parallax' => false]
                    ])
                ]
            ];
            
            if (isset($templates[$template])) {
                $content->update($templates[$template]);
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Template '{$template}' appliqué avec succès !"
                    ]);
                }
                
                return redirect()->back()
                    ->with('success', "Template '{$template}' appliqué avec succès !");
            }
            
            throw new \Exception('Template non trouvé');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'application du template: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'application du template: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'application du template: ' . $e->getMessage());
        }
    }

    /**
     * Sauvegarder comme brouillon
     */
    public function saveDraft(Request $request, $invitation_id)
    {
        try {
            $invitation = Invitation::findOrFail($invitation_id);
            
            $validated = $request->validate([
                'couple' => 'nullable|string|max:255',
                'event_datetime' => 'nullable|date',
                'venue_name' => 'nullable|string|max:255',
                'intro_1' => 'nullable|string',
                'body_html' => 'nullable|string',
            ]);
            
            $content = Content::updateOrCreate(
                ['invitation_id' => $invitation->id],
                array_merge($validated, [
                    'event_id' => $invitation->event_id,
                    'guest_id' => $invitation->guest_id,
                    'unique_code' => $invitation->unique_code,
                    'status' => 'draft'
                ])
            );
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brouillon sauvegardé avec succès !',
                    'content_id' => $content->id
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Brouillon sauvegardé avec succès !');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la sauvegarde du brouillon: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    /**
     * Créer un contenu par défaut pour une invitation
     */
    private function createDefaultContent($invitation)
    {
        $contentUuid = \Illuminate\Support\Str::uuid();
        
        // Données par défaut pour le contenu
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
        
        return Content::create([
            'invitation_id' => $invitation->id,
            'event_id' => $invitation->event_id,
            'guest_id' => $invitation->guest_id,
            'unique_code' => $contentUuid,
            'slug' => \Illuminate\Support\Str::slug($invitation->event->title . '-' . $contentUuid),
            'locale' => 'fr',
            'couple' => $invitation->event->title,
            'event_datetime' => $invitation->event->event_date,
            'timezone' => 'Africa/Kinshasa',
            'status' => 'draft',
            'version' => 1,
            // Champs de lieu avec valeurs par défaut
            'venue_name' => '',
            'venue_address_line1' => '',
            'venue_address_line2' => '',
            'venue_city' => '',
            'venue_region' => '',
            'venue_country' => '',
            'google_maps_url' => '',
            'venue_lat' => null,
            'venue_lng' => null,
            // Champs de contenu avec valeurs par défaut
            'intro_1' => 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous',
            'intro_2' => 'Nous avons le plaisir de vous inviter à partager ce moment spécial',
            'body_html' => '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>',
            'hero_image_path' => null,
            'hero_image_alt' => '',
            'gallery' => null,
            'og_image' => null,
            // Champs de fonctionnalités avec valeurs par défaut
            'guestbook_enabled' => true,
            'guestbook_title' => 'Livre d\'or',
            'guestbook_subtitle' => 'Laissez-nous un message',
            'drinks_enabled' => true,
            'drinks' => $defaultDrinks,
            'cta' => $defaultCta,
            'theme' => $defaultTheme,
            'schedule' => null,
            'meta_title' => $invitation->event->title . ' - Invitation',
            'meta_description' => 'Invitation pour l\'événement de ' . $invitation->event->title,
            'published_at' => null
        ]);
    }

    /**
     * Générer un code unique
     */
    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Invitation::where('unique_code', $code)->exists());
        
        return $code;
    }

    /**
     * ✅ NOUVEAU : Synchroniser le contenu avec toutes les autres invitations du même événement
     */
    private function syncContentToAllInvitations($sourceContent, $eventId, $excludeInvitationId)
    {
        try {
            \Log::info("=== DÉBUT syncContentToAllInvitations ===");
            \Log::info("Event ID: {$eventId}, Exclude Invitation ID: {$excludeInvitationId}");
            
            // Récupérer toutes les autres invitations du même événement
            $otherInvitations = Invitation::where('event_id', $eventId)
                ->where('id', '!=', $excludeInvitationId)
                ->with('content')
                ->get();

            \Log::info("Nombre d'invitations trouvées à synchroniser: " . $otherInvitations->count());
            $syncCount = 0;

            foreach ($otherInvitations as $invitation) {
                \Log::info("Synchronisation de l'invitation ID: {$invitation->id}");
                
                // Préparer les données à synchroniser (exclure les champs spécifiques à l'invitation)
                $syncData = [
                    // Informations générales de l'événement
                    'couple' => $sourceContent->couple,
                    'event_datetime' => $sourceContent->event_datetime,
                    'timezone' => $sourceContent->timezone,
                    
                    // Informations du lieu
                    'venue_name' => $sourceContent->venue_name,
                    'venue_address_line1' => $sourceContent->venue_address_line1,
                    'venue_address_line2' => $sourceContent->venue_address_line2,
                    'venue_city' => $sourceContent->venue_city,
                    'venue_region' => $sourceContent->venue_region,
                    'venue_country' => $sourceContent->venue_country,
                    'google_maps_url' => $sourceContent->google_maps_url,
                    'venue_lat' => $sourceContent->venue_lat,
                    'venue_lng' => $sourceContent->venue_lng,
                    
                    // Contenu de l'invitation
                    'intro_1' => $sourceContent->intro_1,
                    'intro_2' => $sourceContent->intro_2,
                    'body_html' => $sourceContent->body_html,
                    
                    // Images (seulement si elles existent)
                    'hero_image_alt' => $sourceContent->hero_image_alt,
                    
                    // Fonctionnalités
                    'guestbook_enabled' => $sourceContent->guestbook_enabled,
                    'guestbook_title' => $sourceContent->guestbook_title,
                    'guestbook_subtitle' => $sourceContent->guestbook_subtitle,
                    'drinks_enabled' => $sourceContent->drinks_enabled,
                    'drinks' => $sourceContent->drinks,
                    
                    // CTA et thème
                    'cta' => $sourceContent->cta,
                    'theme' => $sourceContent->theme,
                    'schedule' => $sourceContent->schedule,
                    
                    // SEO
                    'meta_title' => $sourceContent->meta_title,
                    'meta_description' => $sourceContent->meta_description,
                    
                    // Statut (garde le statut original de chaque invitation)
                    // 'status' => $sourceContent->status, // Ne pas synchroniser le statut
                ];

                // Ajouter les images seulement si elles existent et ne sont pas vides
                if (!empty($sourceContent->hero_image_path) && $sourceContent->hero_image_path !== null) {
                    $syncData['hero_image_path'] = $sourceContent->hero_image_path;
                }
                if (!empty($sourceContent->gallery) && $sourceContent->gallery !== null) {
                    $syncData['gallery'] = $sourceContent->gallery;
                }
                if (!empty($sourceContent->og_image) && $sourceContent->og_image !== null) {
                    $syncData['og_image'] = $sourceContent->og_image;
                }

                // Mettre à jour ou créer le contenu pour cette invitation
                $updatedContent = Content::updateOrCreate(
                    ['invitation_id' => $invitation->id],
                    array_merge($syncData, [
                        'event_id' => $eventId,
                        'guest_id' => $invitation->guest_id,
                        'unique_code' => $invitation->unique_code,
                        'slug' => \Illuminate\Support\Str::slug($sourceContent->couple . '-' . $invitation->unique_code),
                        'locale' => $sourceContent->locale,
                        'version' => ($invitation->content ? $invitation->content->version + 1 : 1),
                    ])
                );

                \Log::info("Contenu mis à jour pour l'invitation {$invitation->id}, Content ID: {$updatedContent->id}");
                $syncCount++;
            }

            \Log::info("Synchronisation terminée: {$syncCount} invitations mises à jour pour l'événement {$eventId}");
            
            return $syncCount;
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la synchronisation: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ========================================
     * MÉTHODES DE COMPATIBILITÉ LEGACY
     * ========================================
     * Ces méthodes sont fusionnées depuis ContentInvitationController
     * pour maintenir la compatibilité avec l'ancien système
     */

    /**
     * Afficher la page de contenu (legacy)
     */
    public function index()
    {
        return view('invitation.content');
    }

    /**
     * Créer du contenu (legacy - méthode de compatibilité)
     */
    public function create($invitation_id)
    {
        return view('invitation.content', compact('invitation_id'));
    }

    /**
     * Sauvegarder du contenu (legacy - méthode de compatibilité)
     * Cette méthode utilise l'ancien format de données
     */
    public function store(Request $request)
    {
        // Validation legacy
        $request->validate([
            'couple' => 'required|string|max:255',
            'invitation_id' => 'required|exists:invitations,id',
            'epoux' => 'nullable|string|max:255',
            'epouse' => 'nullable|string|max:255',
            'Famille_epoux' => 'nullable|string|max:255',
            'Famille_epouse' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $invitation = Invitation::find($request->invitation_id);
        
        // Données par défaut pour le contenu (même logique que l'ancien système)
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
        
        // Créer le contenu avec l'ancien format
        $contentUuid = \Illuminate\Support\Str::uuid();
        
        Content::create([
            'invitation_id' => $request->invitation_id,
            'event_id' => $invitation->event_id,
            'guest_id' => $invitation->guest_id,
            'unique_code' => $contentUuid,
            'slug' => \Illuminate\Support\Str::slug($request->couple . '-' . $contentUuid),
            'locale' => 'fr',
            'couple' => $request->couple,
            'event_datetime' => $invitation->event->event_date ?? now(),
            'timezone' => 'Africa/Kinshasa',
            'status' => 'draft',
            'version' => 1,
            // Champs de lieu avec valeurs par défaut
            'venue_name' => '',
            'venue_address_line1' => '',
            'venue_address_line2' => '',
            'venue_city' => '',
            'venue_region' => '',
            'venue_country' => '',
            'google_maps_url' => '',
            'venue_lat' => null,
            'venue_lng' => null,
            // Champs de contenu avec valeurs par défaut
            'intro_1' => 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous',
            'intro_2' => 'Nous avons le plaisir de vous inviter à partager ce moment spécial',
            'body_html' => $request->input('content'),
            'hero_image_path' => null,
            'hero_image_alt' => '',
            'gallery' => null,
            'og_image' => null,
            // Champs de fonctionnalités avec valeurs par défaut
            'guestbook_enabled' => true,
            'guestbook_title' => 'Livre d\'or',
            'guestbook_subtitle' => 'Laissez-nous un message',
            'drinks_enabled' => true,
            'drinks' => $defaultDrinks,
            'cta' => $defaultCta,
            'theme' => $defaultTheme,
            'schedule' => null,
            'meta_title' => $request->couple . ' - Invitation',
            'meta_description' => 'Invitation pour l\'événement de ' . $request->couple,
            'published_at' => null,
        ]);

        return redirect()->route('invitation.index')->with('success', 'Contenu ajouté avec succès !');
    }
}
