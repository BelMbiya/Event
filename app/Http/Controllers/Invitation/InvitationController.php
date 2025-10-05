<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Content;
use App\Models\EventDrink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Liste des invitations (1 par événement)
     */
    public function index(Request $request)
    {
        $query = Invitation::with(['event.guests', 'content']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('unique_code', 'like', "%{$search}%")
                    ->orWhereHas('event', function($eventQuery) use ($search) {
                        $eventQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%");
                    });
            });
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();
        $events = Event::orderBy('title')->get();

        return view('invitation.index', compact('invitations', 'events'));
    }

    /**
     * Créer ou éditer l'invitation unique d'un événement
     */
    public function create($event_id)
    {
        $event = Event::with(['eventType', 'guests', 'eventDrinks.drink'])->findOrFail($event_id);

        // Récupérer les invités (pour l'affichage uniquement)
        $guests = $event->guests; // ou Guest::where('event_id', $event_id)->get();

        // Vérifier si une invitation existe déjà
        $invitation = Invitation::where('event_id', $event_id)->first();
        $content = $invitation ? $invitation->content : null;

        $templates = [
            'classic' => 'Classique',
            'modern' => 'Moderne',
            'elegant' => 'Élégant',
            'romantic' => 'Romantique',
            'vintage' => 'Vintage'
        ];

        // S'assurer que toutes les variables nécessaires sont passées à la vue
        return view('invitation.create-advanced', compact(
            'event',
            'guests',      // ← Important : ajouter cette variable
            'invitation',
            'content',
            'templates'
        ));
    }

    /**
     * Enregistrer ou mettre à jour l'invitation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'couple' => 'required|string|max:255',
            'event_datetime' => 'required|date',
            'body_html' => 'required|string|min:10',
            'theme_primary_color' => 'nullable|string|max:7',
            'theme_secondary_color' => 'nullable|string|max:7',
            'theme_accent_color' => 'nullable|string|max:7',
            'hero_image_path' => 'nullable|file|image|max:2048',
            // ... autres validations
        ]);

        try {
            DB::beginTransaction();

            $event = Event::findOrFail($validated['event_id']);

            // Chercher ou créer l'invitation unique
            $uniqueCode = Str::uuid();
            $invitation = Invitation::firstOrCreate(
                ['event_id' => $event->id],
                [
                    'unique_code' => $uniqueCode,
                    'status' => 'draft',
                    'invitation_url' => url("/invitation/{$uniqueCode}")
                ]
            );

            // Upload des fichiers
            $heroPath = $request->file('hero_image_path')?->store('invitations/hero', 'public');
            $programBgPath = $request->file('program_background_image')?->store('invitations/sections', 'public');

            // Chercher ou créer le contenu
            $content = Content::firstOrNew(['invitation_id' => $invitation->id]);

            // Mise à jour des données
            $content->fill([
                'event_id' => $event->id,
                'unique_code' => $invitation->unique_code,
                'slug' => Str::slug($validated['couple'] . '-' . $invitation->unique_code),
                'couple' => $validated['couple'],
                'event_datetime' => $validated['event_datetime'],
                'body_html' => $validated['body_html'],
                'theme' => json_encode([
                    'colors' => [
                        'primary' => $request->input('theme_primary_color', '#e11d48'),
                        'secondary' => $request->input('theme_secondary_color', '#f43f5e'),
                        'accent' => $request->input('theme_accent_color', '#fb7185')
                    ],
                    'fonts' => [
                        'headings' => $request->input('theme_heading_font', 'Alex Brush'),
                        'body' => $request->input('theme_body_font', 'Cormorant Garamond')
                    ],
                ]),
                'status' => $request->input('content_status', 'draft'),
            ]);

            if ($heroPath) $content->hero_image_path = $heroPath;
            if ($programBgPath) $content->program_background_image = $programBgPath;

            $content->save();

            DB::commit();

            return redirect()->route('invitation.index')
                ->with('success', "✅ Invitation créée/mise à jour avec succès pour {$event->title}");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Affichage dynamique pour un invité spécifique
     * URL: /invitation/{event_id}/{guest_id}
     */
    public function show($event_id, $guest_id)
    {
        // Récupérer l'événement et l'invité
        $event = Event::with(['eventType', 'eventDrinks.drink'])->findOrFail($event_id);
        $guest = Guest::where('event_id', $event_id)->findOrFail($guest_id);

        // Récupérer l'invitation unique de l'événement
        $invitation = Invitation::where('event_id', $event_id)->firstOrFail();
        $content = $invitation->content;

        // Si pas de contenu, créer un contenu par défaut
        if (!$content) {
            $content = $this->createDefaultContentForEvent($event, $invitation);
        }

        // Récupérer les données spécifiques à l'invité
        $eventDrinks = EventDrink::with('drink')->where('event_id', $event_id)->get();
        $guestDrinkChoices = $guest->drinkChoices ?? collect();
        $guestBookMessages = \App\Models\GuestBook::where('event_id', $event_id)
            ->where('guest_id', $guest_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Code unique pour les formulaires (event + guest)
        $unique_code = "{$event_id}_{$guest_id}";

        // Préparer les URLs d'images
        $heroImageUrl = $this->getImageUrl($content->hero_image_path, asset('img/img1.webp'));
        $programBgUrl = $this->getImageUrl($content->program_background_image);

        // Extraire le thème
        $theme = json_decode($content->theme ?? '{}', true);
        $themeColors = $theme['colors'] ?? [
            'primary' => '#e11d48',
            'secondary' => '#f43f5e',
            'accent' => '#fb7185'
        ];
        $themeFonts = $theme['fonts'] ?? [
            'headings' => 'Playfair Display',
            'body' => 'Inter'
        ];
        $themeDecorations = $theme['decorations'] ?? [
            'floral' => false,
            'overlay' => false,
            'parallax' => false
        ];

        // Marquer comme vue
        $this->trackView($event_id, $guest_id);

        return view('invitation.invitation_paper.Invitation_3', compact(
            'invitation',
            'guest',
            'content',
            'event',
            'eventDrinks',
            'guestDrinkChoices',
            'guestBookMessages',
            'unique_code',
            'heroImageUrl',
            'programBgUrl',
            'themeColors',
            'themeFonts',
            'themeDecorations'
        ));
    }

    /**
     * Affichage générique (sans invité spécifique)
     * URL: /invitation/{event_id}
     */
    public function showGeneric($event_id)
    {
        $event = Event::with(['eventType', 'eventDrinks.drink'])->findOrFail($event_id);
        $invitation = Invitation::where('event_id', $event_id)->firstOrFail();
        $content = $invitation->content;

        // Si pas de contenu, créer un contenu par défaut
        if (!$content) {
            $content = $this->createDefaultContentForEvent($event, $invitation);
        }

        // Créer un invité virtuel
        $guest = (object) [
            'id' => null,
            'first_name' => 'Invité',
            'last_name' => '',
            'email' => null,
            'phone' => null,
            'rsvp_status' => 'pending'
        ];

        $eventDrinks = EventDrink::with('drink')->where('event_id', $event_id)->get();
        $unique_code = "general_{$event_id}";

        $heroImageUrl = $this->getImageUrl($content->hero_image_path, asset('img/img1.webp'));

        $theme = json_decode($content->theme ?? '{}', true);
        $themeColors = $theme['colors'] ?? ['primary' => '#e11d48', 'secondary' => '#f43f5e', 'accent' => '#fb7185'];
        $themeFonts = $theme['fonts'] ?? ['headings' => 'Playfair Display', 'body' => 'Inter'];
        $themeDecorations = $theme['decorations'] ?? ['floral' => false, 'overlay' => false, 'parallax' => false];

        return view('invitation.invitation_paper.Invitation_3', compact(
            'invitation',
            'guest',
            'content',
            'event',
            'eventDrinks',
            'unique_code',
            'heroImageUrl',
            'themeColors',
            'themeFonts',
            'themeDecorations'
        ));
    }


    public function update(Request $request, $id)
    {
        $invitation = Invitation::with(['event', 'content'])->findOrFail($id);

        // Si c'est une requête GET, afficher le formulaire d'édition
        if ($request->isMethod('GET')) {
            // ✅ RÉCUPÉRATION DES COULEURS DEPUIS LA BASE DE DONNÉES
            // Récupérer le premier contenu existant pour cet événement pour avoir les couleurs
            $existingContent = Content::where('event_id', $invitation->event_id)->first();

            if ($existingContent) {
                $content = $existingContent;
                \Log::info("Contenu existant trouvé pour l'édition, ID: {$content->id}");
            } else {
                // Créer le contenu s'il n'existe pas
                $content = $this->createDefaultContent($invitation);
                \Log::info("Aucun contenu existant, création d'un contenu par défaut");
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

        // Si c'est une requête PUT/POST, traiter la mise à jour
        $event = $invitation->event;

        // ✅ DEBUG : Log des données reçues
        \Log::info("=== MISE À JOUR INVITATION {$id} ===");
        \Log::info("Données reçues:", $request->all());

        // Validation complète basée sur la logique de création
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
            'program_background_image' => 'nullable|file|image|max:2048',
            'guestbook_background_image' => 'nullable|file|image|max:2048',
            'drinks_background_image' => 'nullable|file|image|max:2048',
            'rsvp_background_image' => 'nullable|file|image|max:2048',
            'footer_background_image' => 'nullable|file|image|max:2048',
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
            'content_status' => 'nullable|in:draft,published,archived'
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
            // ✅ UPLOAD DES FICHIERS (même logique que la création)
            $heroPath = $request->file('hero_image_path')?->store('invitations/hero', 'public');
            $programBgPath = $request->file('program_background_image')?->store('invitations/sections', 'public');
            $guestbookBgPath = $request->file('guestbook_background_image')?->store('invitations/sections', 'public');
            $drinksBgPath = $request->file('drinks_background_image')?->store('invitations/sections', 'public');
            $rsvpBgPath = $request->file('rsvp_background_image')?->store('invitations/sections', 'public');
            $footerBgPath = $request->file('footer_background_image')?->store('invitations/sections', 'public');
            $galleryPaths = $request->file('gallery') ? array_map(fn($f) => $f->store('invitations/gallery', 'public'), $request->file('gallery')) : null;
            $ogPath = $request->file('og_image')?->store('invitations/og', 'public');

            // ✅ MISE À JOUR DE TOUTES LES INVITATIONS DE L'ÉVÉNEMENT
            $allInvitations = Invitation::where('event_id', $event->id)->get();
            $updatedCount = 0;
            $skippedCount = 0;

            \Log::info("Mise à jour de toutes les invitations pour l'événement {$event->id}, total: {$allInvitations->count()}");

            foreach ($allInvitations as $currentInvitation) {
                $content = $currentInvitation->content;
                if (!$content) {
                    \Log::info("Contenu manquant pour l'invitation {$currentInvitation->id}, création d'un nouveau contenu");
                    // Créer le contenu s'il n'existe pas
                    $content = new Content();
                    $content->invitation_id = $currentInvitation->id;
                    $content->event_id = $event->id;
                    $content->guest_id = $currentInvitation->guest_id;
                    $content->unique_code = Str::uuid();
                    $content->slug = Str::slug($validated['couple'] . '-' . $content->unique_code);
                    $content->locale = 'fr';
                    $content->version = 1;
                }

                // ✅ MISE À JOUR DES CHAMPS (même logique que la création)
                $content->couple = $validated['couple'] ?? $event->title;
                $content->event_datetime = $validated['event_datetime'] ?? $event->event_date;
                $content->timezone = $request->input('timezone', 'Africa/Kinshasa');
                $content->status = $request->input('status', 'published');

                // Champs de lieu
                $content->venue_name = $request->input('venue_name', $event->location ?? '');
                $content->venue_address_line1 = $request->input('venue_address_line1', $event->address ?? '');
                $content->venue_address_line2 = $request->input('venue_address_line2', '');
                $content->venue_city = $request->input('venue_city', $event->city ?? '');
                $content->venue_region = $request->input('venue_region', '');
                $content->venue_country = $request->input('venue_country', $event->country ?? 'RDC');
                $content->google_maps_url = $request->input('google_maps_url', '');
                $content->venue_lat = $request->input('venue_lat');
                $content->venue_lng = $request->input('venue_lng');

                // Champs de contenu
                $content->intro_1 = $request->input('intro_1', 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous');
                $content->intro_2 = $request->input('intro_2', '💌 Invitation 💌');
                $content->body_html = $request->input('body_html', '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>');
                $content->program_html = $request->input('program_html');

                // Images (garder l'ancienne si pas de nouvelle)
                if ($heroPath) $content->hero_image_path = $heroPath;
                if ($programBgPath) $content->program_background_image = $programBgPath;
                if ($guestbookBgPath) $content->guestbook_background_image = $guestbookBgPath;
                if ($drinksBgPath) $content->drinks_background_image = $drinksBgPath;
                if ($rsvpBgPath) $content->rsvp_background_image = $rsvpBgPath;
                if ($footerBgPath) $content->footer_background_image = $footerBgPath;
                if ($galleryPaths) $content->gallery = json_encode($galleryPaths);
                if ($ogPath) $content->og_image = $ogPath;

                // Champs de fonctionnalités
                $content->guestbook_enabled = $request->has('guestbook_enabled') ? true : false;
                $content->guestbook_title = $request->input('guestbook_title', 'Livre d\'or');
                $content->guestbook_subtitle = $request->input('guestbook_subtitle', 'Laissez-nous un message');
                $content->drinks_enabled = $request->has('drinks_enabled') ? true : false;

                // JSON pour les boissons (même logique que la création)
                $content->drinks = $request->has('drinks_enabled') ? json_encode([
                    'enabled' => true,
                    'title' => $request->input('drinks_title', 'Choisissez vos boissons'),
                    'submit_label' => $request->input('drinks_submit_label', 'Valider mes choix')
                ]) : json_encode([
                    'enabled' => false,
                    'title' => 'Choisissez vos boissons',
                    'submit_label' => 'Valider mes choix'
                ]);

                // JSON pour les CTA (même logique que la création)
                $content->cta = json_encode([
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
                ]);

                // JSON pour le thème (même logique que la création)
                $content->theme = json_encode([
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
                ]);

                // Champs meta
                $content->meta_title = $request->input('meta_title', $validated['couple'] . ' - Invitation');
                $content->meta_description = $request->input('meta_description', 'Invitation pour l\'événement de ' . $validated['couple']);
                $content->published_at = $request->input('content_status') === 'published' ? now() : null;

                // Planning dynamique
                $scheduleData = [];
                for ($i = 0; $i < 5; $i++) {
                    $time = $request->input("schedule_time_{$i}");
                    $scheduleEvent = $request->input("schedule_event_{$i}");
                    if ($time || $scheduleEvent) {
                        $scheduleData[] = [
                            'time' => $time,
                            'event' => $scheduleEvent
                        ];
                    }
                }
                $content->schedule = !empty($scheduleData) ? json_encode($scheduleData) : null;

                // Sauvegarder ce contenu
                $content->save();
                $updatedCount++;

                \Log::info("Contenu mis à jour pour l'invitation {$currentInvitation->id}, guest_id: {$currentInvitation->guest_id}");
            }

            DB::commit();

            // ✅ MESSAGE DE SUCCÈS DÉTAILLÉ
            $successMessage = "✅ Modification réussie !\n\n";
            $successMessage .= "📊 Statistiques :\n";
            $successMessage .= "• {$updatedCount} invitation(s) mise(s) à jour\n";
            $successMessage .= "• Événement : {$event->title}\n";
            $successMessage .= "• Couple : " . ($validated['couple'] ?? $event->title) . "\n\n";

            $successMessage .= "🎨 Modifications appliquées :\n";
            if ($request->has('theme_primary_color')) {
                $successMessage .= "• Couleur principale : " . $request->input('theme_primary_color') . "\n";
            }
            if ($request->has('theme_secondary_color')) {
                $successMessage .= "• Couleur secondaire : " . $request->input('theme_secondary_color') . "\n";
            }
            if ($request->has('theme_accent_color')) {
                $successMessage .= "• Couleur d'accent : " . $request->input('theme_accent_color') . "\n";
            }
            if ($request->file('hero_image_path')) {
                $successMessage .= "• Image héros mise à jour\n";
            }
            if ($request->input('body_html')) {
                $successMessage .= "• Message principal modifié\n";
            }
            if ($request->input('intro_1')) {
                $successMessage .= "• Introduction mise à jour\n";
            }

            $successMessage .= "\n🎯 Les modifications ont été appliquées à toutes les invitations de cet événement.";

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'updated_count' => $updatedCount,
                    'event_title' => $event->title
                ]);
            }

            return redirect()->route('invitation.index')
                ->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            \Log::error('Erreur de validation lors de la mise à jour: ' . $e->getMessage());

            // ❌ MESSAGE D'ERREUR DE VALIDATION DÉTAILLÉ
            $errorMessage = "❌ Erreur de validation !\n\n";
            $errorMessage .= "🔍 Cause : Certains champs ne respectent pas les règles de validation.\n\n";
            $errorMessage .= "📋 Champs en erreur :\n";

            foreach ($e->errors() as $field => $messages) {
                $fieldName = ucfirst(str_replace('_', ' ', $field));
                $errorMessage .= "• {$fieldName} : " . implode(', ', $messages) . "\n";
            }

            $errorMessage .= "\n💡 Solutions :\n";
            $errorMessage .= "• Vérifiez que tous les champs obligatoires sont remplis\n";
            $errorMessage .= "• Assurez-vous que les images ne dépassent pas 2MB\n";
            $errorMessage .= "• Vérifiez le format des couleurs (ex: #ff0000)\n";
            $errorMessage .= "• Les dates doivent être au format valide\n";

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $e->errors(),
                    'type' => 'validation'
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la mise à jour d\'invitation: ' . $e->getMessage());

            // ❌ MESSAGE D'ERREUR GÉNÉRALE DÉTAILLÉ
            $errorMessage = "❌ Erreur lors de la modification !\n\n";
            $errorMessage .= "🔍 Cause : " . $e->getMessage() . "\n\n";

            // Diagnostiquer le type d'erreur
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $errorMessage .= "🗄️ Type d'erreur : Problème de base de données\n";
                $errorMessage .= "💡 Solutions possibles :\n";
                $errorMessage .= "• Vérifiez la connexion à la base de données\n";
                $errorMessage .= "• Assurez-vous que les tables existent\n";
                $errorMessage .= "• Vérifiez les contraintes d'intégrité\n";
            } elseif (strpos($e->getMessage(), 'storage') !== false) {
                $errorMessage .= "📁 Type d'erreur : Problème de stockage de fichiers\n";
                $errorMessage .= "💡 Solutions possibles :\n";
                $errorMessage .= "• Vérifiez les permissions du dossier storage\n";
                $errorMessage .= "• Assurez-vous que le lien symbolique existe\n";
                $errorMessage .= "• Vérifiez l'espace disque disponible\n";
            } elseif (strpos($e->getMessage(), 'memory') !== false) {
                $errorMessage .= "💾 Type d'erreur : Problème de mémoire\n";
                $errorMessage .= "💡 Solutions possibles :\n";
                $errorMessage .= "• Réduisez la taille des images\n";
                $errorMessage .= "• Traitez moins d'invitations à la fois\n";
            } else {
                $errorMessage .= "⚠️ Type d'erreur : Erreur inattendue\n";
                $errorMessage .= "💡 Solutions possibles :\n";
                $errorMessage .= "• Réessayez dans quelques instants\n";
                $errorMessage .= "• Contactez l'administrateur si le problème persiste\n";
            }

            $errorMessage .= "\n📝 Détails techniques : " . get_class($e) . "\n";
            $errorMessage .= "🕒 Heure : " . now()->format('d/m/Y H:i:s');

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'type' => 'general',
                    'error_class' => get_class($e)
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }


    /**
     * Générer les liens dynamiques pour tous les invités
     */
    public function generateLinks($event_id)
    {
        $event = Event::findOrFail($event_id);
        $guests = Guest::where('event_id', $event_id)->get();

        $links = $guests->map(function($guest) use ($event_id) {
            return [
                'guest_id' => $guest->id,
                'guest_name' => "{$guest->first_name} {$guest->last_name}",
                'guest_email' => $guest->email,
                'url' => url("/invitation/{$event_id}/{$guest->id}"),
                'whatsapp' => "https://wa.me/{$guest->phone}?text=" .
                    urlencode("Bonjour ! Voici votre invitation : " . url("/invitation/{$event_id}/{$guest->id}"))
            ];
        });

        return view('invitation.links', compact('event', 'links'));
    }

    /**
     * Obtenir l'URL d'une image avec fallback
     */
    private function getImageUrl($imagePath, $defaultUrl = null)
    {
        if (!$imagePath) return $defaultUrl;

        $publicPath = 'storage/' . trim($imagePath);

        if (file_exists(public_path($publicPath))) {
            return asset($publicPath);
        }

        return asset($publicPath);
    }

    /**
     * Tracker les vues d'invitation
     */
    private function trackView($event_id, $guest_id)
    {
        \Log::info("Invitation vue - Event: {$event_id}, Guest: {$guest_id}");

        // Mettre à jour la date de dernière vue
        Guest::where('id', $guest_id)->update([
            'last_viewed_at' => now()
        ]);
    }

    /**
     * Supprimer une invitation (et son contenu)
     */
    public function destroy($id)
    {
        try {
            $invitation = Invitation::findOrFail($id);

            // Supprimer le contenu associé
            if ($invitation->content) {
                $invitation->content->delete();
            }

            $invitation->delete();

            return redirect()->route('invitation.index')
                ->with('success', 'Invitation supprimée avec succès');

        } catch (\Exception $e) {
            return redirect()->route('invitation.index')
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Créer un contenu par défaut pour un événement
     */
    private function createDefaultContentForEvent($event, $invitation)
    {
        $contentUuid = Str::uuid();

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

        // Créer le contenu par défaut
        $content = \App\Models\Content::create([
            'invitation_id' => $invitation->id,
            'event_id' => $event->id,
            'guest_id' => $invitation->guest_id,
            'unique_code' => $contentUuid,
            'slug' => Str::slug($event->title . '-' . $contentUuid),
            'locale' => 'fr',
            'couple' => $event->title,
            'event_datetime' => $event->event_date,
            'timezone' => 'Africa/Kinshasa',
            'status' => 'draft',
            'version' => 1,
            // Champs de lieu avec valeurs par défaut
            'venue_name' => '',
            'venue_address_line1' => '',
            'venue_address_line2' => '',
            'venue_city' => 'Kinshasa',
            'venue_region' => 'Kinshasa',
            'venue_country' => 'RDC',
            'google_maps_url' => '',
            'venue_lat' => null,
            'venue_lng' => null,
            // Contenu par défaut
            'intro_1' => 'Nous avons le plaisir de vous inviter à célébrer avec nous',
            'intro_2' => 'Votre présence nous honorera',
            'body_html' => '<p>Nous sommes ravis de partager ce moment spécial avec vous.</p><p>Votre présence rendra cette célébration encore plus belle.</p>',
            'program_html' => '<div class="program-item"><h4>Accueil</h4><p>Arrivée des invités</p></div><div class="program-item"><h4>Cérémonie</h4><p>Moment de célébration</p></div><div class="program-item"><h4>Réception</h4><p>Repas et festivités</p></div>',
            // Images par défaut
            'hero_image_path' => null,
            'program_background_image' => null,
            'gallery_images' => json_encode([]),
            // Thème et configuration
            'theme' => $defaultTheme,
            'cta_buttons' => $defaultCta,
            'drinks_config' => $defaultDrinks,
            'guestbook_enabled' => true,
            'drinks_enabled' => true,
            // Métadonnées
            'meta_title' => $event->title . ' - Invitation',
            'meta_description' => 'Invitation pour ' . $event->title,
            'og_image' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $content;
    }
}
