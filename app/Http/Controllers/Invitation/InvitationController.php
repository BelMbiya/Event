<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;

/**
 * ========================================
 * INVITATION CONTROLLER - GESTION COMPLÈTE DES INVITATIONS
 * ========================================
 * 
 * Ce contrôleur gère toutes les fonctionnalités des invitations d'événements.
 * Il traite la création, édition, publication, duplication et suppression
 * des invitations avec gestion des contenus et métriques d'engagement.
 */
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Content;
use App\Models\EventDrink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\GuestDrinkChoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvitationController extends Controller
{

    public function index(Request $request)
    {
        $query = Invitation::with(['event', 'guest', 'content']);

        // ✅ RECHERCHE GLOBALE SIMPLIFIÉE
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Recherche dans les invitations
                $q->where('status', 'like', "%{$search}%")
                  ->orWhere('unique_code', 'like', "%{$search}%")
                  
                  // Recherche dans les événements
                  ->orWhereHas('event', function($eventQuery) use ($search) {
                      $eventQuery->where('title', 'like', "%{$search}%")
                                ->orWhere('location', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                  })
                  
                  // Recherche dans les invités
                  ->orWhereHas('guest', function($guestQuery) use ($search) {
                      $guestQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                  })
                  
                  // Recherche dans le contenu
                  ->orWhereHas('content', function($contentQuery) use ($search) {
                      $contentQuery->where('couple', 'like', "%{$search}%")
                                  ->orWhere('intro_1', 'like', "%{$search}%")
                                  ->orWhere('intro_2', 'like', "%{$search}%")
                                  ->orWhere('venue_name', 'like', "%{$search}%");
                  });
            });
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();

        // Données pour les filtres
        $events = Event::orderBy('title')->get();
        $statuses = ['pending', 'sent', 'opened', 'responded', 'called'];

        return view('invitation.index', compact('invitations', 'events', 'statuses'));
    }


    public function create($event_id)
    {
        $event = Event::with(['eventType', 'guests', 'eventDrinks.drink'])->findOrFail($event_id);
        $guests = Guest::where('event_id', $event_id)->get();
        $eventDrinks = EventDrink::with('drink')->where('event_id', $event_id)->get();
        
        // Récupérer les templates d'invitation disponibles
        $templates = [
            'classic' => 'Classique',
            'modern' => 'Moderne', 
            'elegant' => 'Élégant',
            'romantic' => 'Romantique',
            'vintage' => 'Vintage'
        ];
        
        // Récupérer les contenus existants pour cet événement
        $existingContents = Content::where('event_id', $event_id)->get();
        
        return view('invitation.create-advanced', compact('event', 'guests', 'eventDrinks', 'templates', 'existingContents'));
    }

    public function store(Request $request)
    {
        try {
            // Validation étendue pour un système complet
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'couple' => 'required|string|max:255',
            'event_datetime' => 'required|date',
            'template' => 'nullable|string|in:classic,modern,elegant,romantic,vintage',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|max:500',
            'intro_text' => 'nullable|string',
            'body_text' => 'nullable|string',
            'rsvp_enabled' => 'nullable|boolean',
            'drinks_enabled' => 'nullable|boolean',
            'guestbook_enabled' => 'nullable|boolean',
            'selected_guests' => 'nullable|array',
            'selected_guests.*' => 'exists:guests,id',
            // Champs supplémentaires pour le contenu
            'timezone' => 'nullable|string|max:50',
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
            'hero_image_path' => 'nullable|file|image|max:2048',
            'program_background_image' => 'nullable|file|image|max:2048',
            'guestbook_background_image' => 'nullable|file|image|max:2048',
            'drinks_background_image' => 'nullable|file|image|max:2048',
            'rsvp_background_image' => 'nullable|file|image|max:2048',
            'footer_background_image' => 'nullable|file|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|image|max:2048',
            'og_image' => 'nullable|file|image|max:2048',
            'guestbook_title' => 'nullable|string|max:255',
            'guestbook_subtitle' => 'nullable|string|max:255',
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
            'content_status' => 'nullable|in:draft,published,archived'
        ]);
        
        // Validation du programme HTML
        $request->validate([
            'program_html' => 'nullable|string',
        ]);
            
            DB::beginTransaction();
            
            // Récupérer l'événement
            $event = Event::findOrFail($validated['event_id']);
            $guests = Guest::where('event_id', $validated['event_id'])->get();
            
            // ✅ LOGIQUE SIMPLIFIÉE : Créer toujours une invitation unique par événement
            // Vérifier si une invitation existe déjà pour cet événement
            $existingInvitation = Invitation::where('event_id', $validated['event_id'])
                ->whereNull('guest_id') // Invitation générale de l'événement
                ->first();
            
            if ($existingInvitation) {
                return redirect()->back()
                    ->with('warning', 'Une invitation existe déjà pour cet événement. Utilisez l\'édition pour la modifier.');
            }
            
            // Créer une seule invitation générale pour l'événement
            $eventUuid = Str::uuid();
            $invitation = Invitation::create([
                'event_id' => $validated['event_id'],
                'guest_id' => null, // Invitation générale
                'unique_code' => $eventUuid,
                'status' => 'sent',
                'invitation_url' => url('/invitation/dynamic'),
            ]);
            $invitations = [$invitation];
            
            // Créer un contenu pour CHAQUE invitation (logique cohérente)
            if (!empty($invitations)) {
                // Gestion des uploads de fichiers
                $heroPath = $request->file('hero_image_path')?->store('invitations/hero', 'public');
                $programBgPath = $request->file('program_background_image')?->store('invitations/sections', 'public');
                $guestbookBgPath = $request->file('guestbook_background_image')?->store('invitations/sections', 'public');
                $drinksBgPath = $request->file('drinks_background_image')?->store('invitations/sections', 'public');
                $rsvpBgPath = $request->file('rsvp_background_image')?->store('invitations/sections', 'public');
                $footerBgPath = $request->file('footer_background_image')?->store('invitations/sections', 'public');
                $galleryPaths = $request->file('gallery') ? array_map(fn($f) => $f->store('invitations/gallery', 'public'), $request->file('gallery')) : null;
                $ogPath = $request->file('og_image')?->store('invitations/og', 'public');
                
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
                
                // Créer un contenu pour chaque invitation
                foreach ($invitations as $invitation) {
                    $contentUuid = Str::uuid();
                    
                    Content::create([
                        'invitation_id' => $invitation->id, // ✅ Lié à cette invitation spécifique
                        'event_id' => $validated['event_id'],
                        'guest_id' => $invitation->guest_id, // ✅ null pour invitation générale, ou guest_id pour invitation spécifique
                        'unique_code' => $contentUuid,
                        'slug' => Str::slug($validated['couple'] . '-' . $contentUuid),
                        'locale' => 'fr',
                        'couple' => $validated['couple'],
                        'event_datetime' => $validated['event_datetime'],
                        'timezone' => $request->input('timezone', 'Africa/Kinshasa'),
                        'status' => $request->input('content_status', 'draft'),
                        'version' => 1,
                        // Champs de lieu
                        'venue_name' => $request->input('venue_name', ''),
                        'venue_address_line1' => $request->input('venue_address_line1', ''),
                        'venue_address_line2' => $request->input('venue_address_line2', ''),
                        'venue_city' => $request->input('venue_city', ''),
                        'venue_region' => $request->input('venue_region', ''),
                        'venue_country' => $request->input('venue_country', ''),
                        'google_maps_url' => $request->input('google_maps_url', ''),
                        'venue_lat' => $request->input('venue_lat'),
                        'venue_lng' => $request->input('venue_lng'),
                        // Champs de contenu
                        'intro_1' => $request->input('intro_1', 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous'),
                        'intro_2' => $request->input('intro_2', 'Nous avons le plaisir de vous inviter à partager ce moment spécial'),
                        'body_html' => $request->input('body_html', '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>'),
                        'hero_image_path' => $heroPath,
                        'program_background_image' => $programBgPath,
                        'guestbook_background_image' => $guestbookBgPath,
                        'drinks_background_image' => $drinksBgPath,
                        'rsvp_background_image' => $rsvpBgPath,
                        'footer_background_image' => $footerBgPath,
                        'gallery' => $galleryPaths ? json_encode($galleryPaths) : null,
                        'og_image' => $ogPath,
                        // Champs de fonctionnalités
                        'guestbook_enabled' => $request->has('guestbook_enabled') ? true : false,
                        'guestbook_title' => $request->input('guestbook_title', 'Livre d\'or'),
                        'guestbook_subtitle' => $request->input('guestbook_subtitle', 'Laissez-nous un message'),
                        'drinks_enabled' => $request->has('drinks_enabled') ? true : false,
                        'drinks' => $request->has('drinks_enabled') ? json_encode([
                            'enabled' => true,
                            'title' => $request->input('drinks_title', 'Choisissez vos boissons'),
                            'submit_label' => $request->input('drinks_submit_label', 'Valider mes choix')
                        ]) : $defaultDrinks,
                        'cta' => json_encode([
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
                        ]),
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
                            'decorations' => [
                                'floral' => $request->has('theme_floral'),
                                'overlay' => $request->has('theme_overlay'),
                                'parallax' => $request->has('theme_parallax')
                            ]
                        ]),
                        'program_html' => $request->input('program_html'),
                        'meta_title' => $request->input('meta_title', $validated['couple'] . ' - Invitation'),
                        'meta_description' => $request->input('meta_description', 'Invitation pour l\'événement de ' . $validated['couple']),
                        'published_at' => $request->input('content_status') === 'published' ? now() : null
                    ]);
                }
            }
            
            DB::commit();
            
            $count = count($invitations);
            $message = "{$count} invitation(s) et contenu(s) créé(s) avec succès !";
            
            // Ajouter un avertissement si des invités avaient déjà des invitations
            if (!empty($duplicateGuests)) {
                $message .= "\n\n⚠️ <strong>Attention :</strong> Les invités suivants avaient déjà des invitations et ont été ignorés :\n";
                $message .= "• " . implode("\n• ", $duplicateGuests);
            }
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'count' => $count,
                    'duplicates' => $duplicateGuests
                ]);
            }
            
            return redirect()->route('invitation.index')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation. Vérifiez les champs marqués en rouge.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Erreur de validation. Vérifiez les champs marqués en rouge.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la création d\'invitation: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création de l\'invitation: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'invitation: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Affichage dynamique d'invitation
     * URL: /invitation/{guest_id}
     * Une seule invitation par événement qui s'adapte selon l'invité
     */
    public function showDynamic($guest_id)
    {
        // Récupérer l'invité spécifique avec son événement
        $guest = Guest::with(['event.eventType', 'event.eventDrinks.drink', 'guestType', 'drinkChoices.eventDrink.drink', 'eventTable'])
            ->findOrFail($guest_id);
        
        // Récupérer l'événement depuis l'invité
        $event = $guest->event;
        
        // Récupérer le contenu de l'invitation (priorité au contenu général, puis spécifique)
        $content = Content::where('event_id', $event->id)
            ->whereNull('guest_id') // Contenu général de l'événement
            ->first();
        
        // Si pas de contenu général ou pas de schedule/program_html, chercher le contenu spécifique de l'invité
        if (!$content || (!$content->schedule && !$content->program_html)) {
            $content = Content::where('event_id', $event->id)
                ->where('guest_id', $guest_id) // Contenu spécifique de l'invité
                ->first();
        }
        
        // Si toujours pas de contenu, créer un contenu par défaut
        if (!$content) {
            $content = $this->createDefaultEventContent($event);
        }
        
        // Récupérer les boissons de l'événement
        $eventDrinks = EventDrink::with('drink')
            ->where('event_id', $event->id)
            ->get();
        
        // Récupérer les choix de boissons de cet invité
        $guestDrinkChoices = $guest->drinkChoices ?? collect();
        
        // Récupérer les messages du livre d'or pour cet invité
        $guestBookMessages = \App\Models\GuestBook::where('event_id', $event->id)
            ->where('guest_id', $guest_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Créer un objet invitation virtuel pour la compatibilité avec la vue
        $invitation = (object) [
            'id' => $event->id,
            'unique_code' => $event->id . '_' . $guest_id,
            'event_id' => $event->id,
            'guest_id' => $guest_id,
            'status' => 'published',
            'event' => $event,
            'guest' => $guest,
            'content' => $content
        ];

        // Créer le code unique pour les formulaires
        $unique_code = $event->id . '_' . $guest_id;

        // Marquer l'invitation comme ouverte
        $this->markInvitationAsOpened($event->id, $guest_id);

        return view('invitation.invitation_paper.Invitation_3', compact(
            'invitation',
            'guest',
            'content',
            'event',
            'eventDrinks',
            'guestDrinkChoices',
            'guestBookMessages',
            'unique_code'
        ));
    }

    /**
     * ✅ MÉTHODE LEGACY : Affichage d'invitation par code unique (pour compatibilité)
     */
    public function show($unique_code)
    {
        $invitation = Invitation::with(['event', 'guest', 'content'])
            ->where('unique_code', $unique_code)
            ->firstOrFail();

        $event = $invitation->event ?? new \App\Models\Event();
        $guest = $invitation->guest ?? null;
        
        // ✅ CORRECTION : Utiliser le contenu de cette invitation spécifique
        $content = $invitation->content ?? new \App\Models\Content();

        $eventDrinks = $event->id
            ? EventDrink::with('drink')->where('event_id', $event->id)->get()
            : collect();

        return view('invitation.invitation_paper.Invitation_3', compact(
            'invitation',
            'guest',
            'content',
            'event',
            'eventDrinks',
            'unique_code'
        ));
    }

    public function destroy($id)
    {
        try {
            $invitation = Invitation::findOrFail($id);
            
            // Supprimer le contenu associé
            if ($invitation->content) {
                $invitation->content->delete();
            }
            
            // Supprimer l'invitation
            $invitation->delete();
            
            return redirect()->route('invitation.index')
                ->with('success', 'Invitation supprimée avec succès !');
                
        } catch (\Exception $e) {
            return redirect()->route('invitation.index')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function downloadInvitation($unique_code)
    {
        $invitation = Invitation::with(['event', 'guest', 'content'])
            ->where('unique_code', $unique_code)
            ->firstOrFail();

        $event = $invitation->event ?? new \App\Models\Event();
        $guest = $invitation->guest ?? null;
        
        // ✅ CORRECTION : Utiliser le contenu de cette invitation spécifique
        $content = $invitation->content ?? new \App\Models\Content();

        $eventDrinks = $event->id
            ? EventDrink::with('drink')->where('event_id', $event->id)->get()
            : collect();

        // Récupérer les choix de boissons de cet invité
        $guestDrinkChoices = $guest ? $guest->drinkChoices ?? collect() : collect();
        
        // Récupérer les messages du livre d'or pour cet invité
        $guestBookMessages = $guest && $event->id
            ? \App\Models\GuestBook::where('event_id', $event->id)
                ->where('guest_id', $guest->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $pdf = Pdf::loadView('invitation.invitation_paper.Invitation_3', compact(
            'invitation',
            'guest',
            'content',
            'event',
            'eventDrinks',
            'guestDrinkChoices',
            'guestBookMessages',
            'unique_code'
        ));

        return $pdf->download('invitation-' . $unique_code . '.pdf');
    }

    public function downloadAllInvitationsPDF(Request $request)
    {
        $query = Invitation::with(['event', 'guest']);

        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('guest_name')) {
            $query->whereHas('guest', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->guest_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->guest_name . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();

        // Générer un nom de fichier basé sur les filtres
        $filename = 'invitations';
        if ($request->filled('event_id')) {
            $event = Event::find($request->event_id);
            $filename .= '_' . Str::slug($event->title);
        }
        if ($request->filled('status')) {
            $filename .= '_' . $request->status;
        }
        $filename .= '_' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('invitation.all-invitations-pdf', compact('invitations'));

        return $pdf->download($filename);
    }

    /**
     * Créer automatiquement des invitations pour tous les invités d'un événement
     * Vérifie d'abord quels invités ont déjà des invitations et crée seulement pour ceux qui n'en ont pas
     */
    public function createInvitationsForAllGuests(Request $request, $event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $guests = Guest::where('event_id', $event_id)->get();
            
            if ($guests->isEmpty()) {
                $warningMessage = '⚠️ <strong>Aucun invité trouvé pour cet événement</strong><br><br>';
                $warningMessage .= '🔍 <strong>Événement :</strong> ' . $event->title . '<br><br>';
                $warningMessage .= '💡 <strong>Solutions possibles :</strong><br>';
                $warningMessage .= '• Ajoutez des invités à cet événement<br>';
                $warningMessage .= '• Vérifiez que les invités sont bien associés à l\'événement ID: ' . $event_id . '<br>';
                $warningMessage .= '• Utilisez le bouton "Gérer Invités" pour ajouter des invités';
                
                return redirect()->back()->with('warning', $warningMessage);
            }
            
            $createdCount = 0;
            $existingCount = 0;
            $createdForGuests = [];
            $existingForGuests = [];
            
            DB::beginTransaction();
            
            // Vérifier s'il existe déjà un contenu partagé pour cet événement
            $existingContent = Content::where('event_id', $event_id)
                ->where('guest_id', null)
                ->first();
            
            $invitations = [];
            
            foreach ($guests as $guest) {
                // Vérifier si une invitation existe déjà pour cet invité
                $existingInvitation = Invitation::where('event_id', $event_id)
                    ->where('guest_id', $guest->id)
                    ->first();
                
                if (!$existingInvitation) {
                    $guestUuid = Str::uuid();
                    $invitation = Invitation::create([
                        'event_id' => $event_id,
                        'guest_id' => $guest->id,
                        'unique_code' => $guestUuid,
                        'status' => 'pending',
                        'invitation_url' => url('/invitation/' . $guestUuid),
                    ]);
                    
                    // Mettre à jour le statut RSVP de l'invité en "pending" (attente)
                    $guest->update(['rsvp_status' => 'pending']);
                    
                    $invitations[] = $invitation;
                    $createdCount++;
                    $createdForGuests[] = "{$guest->first_name} {$guest->last_name}";
                    \Log::info("Invitation créée pour {$guest->first_name} {$guest->last_name} avec ID: {$invitation->id} - RSVP mis en attente");
                } else {
                    $existingCount++;
                    $existingForGuests[] = "{$guest->first_name} {$guest->last_name}";
                }
            }
            
            // ✅ CORRECTION : Créer un contenu pour chaque nouvelle invitation
            if (!empty($invitations)) {
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
                
                // Créer un contenu pour chaque nouvelle invitation
                foreach ($invitations as $invitation) {
                    $contentUuid = Str::uuid();
                    
                    Content::create([
                        'invitation_id' => $invitation->id, // ✅ Lié à cette invitation spécifique
                        'event_id' => $event_id,
                        'guest_id' => $invitation->guest_id, // ✅ Lié à cet invité spécifique
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
                        'program_html' => null,
                        'meta_title' => $event->title . ' - Invitation',
                        'meta_description' => 'Invitation pour l\'événement de ' . $event->title,
                        'published_at' => null
                    ]);
                    
                    \Log::info("Contenu créé pour l'invitation {$invitation->id} de l'événement {$event->title}");
                }
            }
            
            DB::commit();
            
            // Construire un message détaillé
            $message = "🔍 <strong>Analyse terminée pour l'événement : {$event->title}</strong><br><br>";
            
            if ($createdCount > 0) {
                $message .= "✅ <strong>{$createdCount} nouvelle(s) invitation(s) créée(s) :</strong><br>";
                $message .= "• " . implode("<br>• ", $createdForGuests) . "<br><br>";
            }
            
            if ($existingCount > 0) {
                $message .= "ℹ️ <strong>{$existingCount} invitation(s) existante(s) (ignorée(s)) :</strong><br>";
                $message .= "• " . implode("<br>• ", $existingForGuests) . "<br><br>";
            }
            
            if ($createdCount === 0 && $existingCount > 0) {
                $message .= "🎉 <strong>Tous les invités ont déjà des invitations !</strong>";
            } elseif ($createdCount > 0) {
                $message .= "🎯 <strong>Opération réussie !</strong> Les invitations manquantes ont été créées.";
            }
            
            // Redirection normale côté serveur
            return redirect()->route('invitation.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la création automatique des invitations: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = '❌ <strong>Erreur lors de la création des invitations</strong><br><br>';
            $errorMessage .= '🔍 <strong>Détails de l\'erreur :</strong><br>';
            $errorMessage .= '• ' . $e->getMessage() . '<br><br>';
            $errorMessage .= '💡 <strong>Solutions possibles :</strong><br>';
            $errorMessage .= '• Vérifiez que l\'événement existe<br>';
            $errorMessage .= '• Vérifiez que les invités sont bien associés à l\'événement<br>';
            $errorMessage .= '• Vérifiez les permissions de la base de données<br>';
            $errorMessage .= '• Contactez l\'administrateur si le problème persiste';
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Créer une invitation pour des invités sélectionnés
     */
    public function createInvitationsForSelectedGuests(Request $request, $event_id)
    {
        try {
            $validated = $request->validate([
                'selected_guests' => 'required|array|min:1',
                'selected_guests.*' => 'exists:guests,id',
                'template' => 'nullable|string|in:classic,modern,elegant,romantic,vintage',
                'couple' => 'required|string|max:255',
                'event_datetime' => 'required|date'
            ]);

            $event = Event::findOrFail($event_id);
            $guests = Guest::whereIn('id', $validated['selected_guests'])->get();
            
            $invitations = [];
            $duplicateGuests = [];
            
            foreach ($guests as $guest) {
                // Vérifier si l'invité a déjà une invitation
                $existingInvitation = Invitation::where('event_id', $event_id)
                    ->where('guest_id', $guest->id)
                    ->first();
                
                if ($existingInvitation) {
                    $duplicateGuests[] = "{$guest->first_name} {$guest->last_name}";
                    continue;
                }
                
                // Créer l'invitation
                $invitation = Invitation::create([
                    'event_id' => $event_id,
                    'guest_id' => $guest->id,
                    'unique_code' => $this->generateUniqueCode(),
                    'status' => 'pending'
                ]);
                
                // Créer le contenu avec les données personnalisées
                Content::create([
                    'invitation_id' => $invitation->id,
                    'event_id' => $event_id,
                    'guest_id' => $guest->id,
                    'unique_code' => $invitation->unique_code,
                    'couple' => $validated['couple'],
                    'event_datetime' => $validated['event_datetime'],
                    'venue_name' => $event->location,
                    'template' => $validated['template'] ?? 'classic',
                    'status' => 'draft'
                ]);
                
                $invitations[] = $invitation;
            }
            
            $count = count($invitations);
            $message = "{$count} invitation(s) créée(s) avec succès pour les invités sélectionnés !";
                
            if (!empty($duplicateGuests)) {
                $message .= " Invités déjà existants : " . implode(', ', $duplicateGuests);
            }
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'count' => $count,
                    'duplicates' => $duplicateGuests
                ]);
            }
            
            return redirect()->route('invitation.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création d\'invitations pour invités sélectionnés: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création des invitations: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la création des invitations: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer une invitation existante
     */
    public function duplicateInvitation($invitation_id)
    {
        try {
            $originalInvitation = Invitation::with(['event', 'guest', 'content'])->findOrFail($invitation_id);
            
            // Créer une nouvelle invitation
            $newInvitation = Invitation::create([
                'event_id' => $originalInvitation->event_id,
                'guest_id' => $originalInvitation->guest_id,
                'unique_code' => $this->generateUniqueCode(),
                'status' => 'pending'
            ]);
            
            // Dupliquer le contenu
            if ($originalInvitation->content) {
                $contentData = $originalInvitation->content->toArray();
                unset($contentData['id'], $contentData['created_at'], $contentData['updated_at']);
                $contentData['invitation_id'] = $newInvitation->id;
                $contentData['unique_code'] = $newInvitation->unique_code;
                
                Content::create($contentData);
            }
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invitation dupliquée avec succès !',
                    'invitation_id' => $newInvitation->id
                ]);
            }
            
            return redirect()->route('invitation.index')
                ->with('success', 'Invitation dupliquée avec succès !');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la duplication d\'invitation: ' . $e->getMessage());
            
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
     * Prévisualiser une invitation
     */
    public function preview($invitation_id)
    {
        $invitation = Invitation::with(['event', 'guest', 'content'])->findOrFail($invitation_id);
        
        return view('invitation.preview', compact('invitation'));
    }

    /**
     * Publier une invitation
     */
    public function publish($invitation_id)
    {
        try {
            $invitation = Invitation::findOrFail($invitation_id);
            $content = $invitation->content;
            
            if (!$content) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Aucun contenu trouvé pour cette invitation.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Aucun contenu trouvé pour cette invitation.');
            }
            
            $content->update([
                'status' => 'published',
                'published_at' => now()
            ]);
            
            $invitation->update(['status' => 'sent']);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invitation publiée avec succès !'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Invitation publiée avec succès !');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la publication d\'invitation: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la publication: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la publication: ' . $e->getMessage());
        }
    }

    /**
     * Archiver une invitation
     */
    public function archive($invitation_id)
    {
        try {
            $invitation = Invitation::findOrFail($invitation_id);
            $content = $invitation->content;
            
            if ($content) {
                $content->update(['status' => 'archived']);
            }
            
            $invitation->update(['status' => 'archived']);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invitation archivée avec succès !'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Invitation archivée avec succès !');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'archivage d\'invitation: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'archivage: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'archivage: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques des invitations
     */
    public function getStats($event_id = null)
    {
        $query = Invitation::query();
        
        if ($event_id) {
            $query->where('event_id', $event_id);
        }
        
        $stats = [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'sent' => $query->where('status', 'sent')->count(),
            'opened' => $query->where('status', 'opened')->count(),
            'responded' => $query->where('status', 'responded')->count(),
            'archived' => $query->where('status', 'archived')->count()
        ];
        
        if (request()->ajax()) {
            return response()->json($stats);
        }
        
        return $stats;
    }

    /**
     * Construire les données de planning à partir de la requête
     */
    private function buildScheduleData(Request $request)
    {
        $scheduleData = [];
        
        // Parcourir les champs de planning (jusqu'à 5 éléments)
        for ($i = 0; $i < 5; $i++) {
            $time = $request->input("schedule_time_{$i}");
            $event = $request->input("schedule_event_{$i}");
            
            // Ajouter seulement si les deux champs sont remplis
            if (!empty($time) && !empty($event)) {
                $scheduleData[] = [
                    'time' => $time,
                    'event' => $event
                ];
            }
        }
        
        // Retourner null si aucun planning n'est défini, sinon encoder en JSON
        return !empty($scheduleData) ? json_encode($scheduleData) : null;
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
     * ✅ NOUVEAU : Vérifier le statut des invitations d'un événement
     */
    public function checkEventInvitationsStatus($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $status = $event->getInvitationsStatus();
            
            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                ],
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Créer un contenu par défaut pour un événement
     */
    private function createDefaultEventContent($event)
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
        
        return Content::create([
            'invitation_id' => null, // Pas d'invitation spécifique
            'event_id' => $event->id,
            'guest_id' => null, // Contenu général de l'événement
            'unique_code' => $contentUuid,
            'slug' => Str::slug($event->title . '-' . $contentUuid),
            'locale' => 'fr',
            'couple' => $event->title,
            'event_datetime' => $event->event_date,
            'timezone' => 'Africa/Kinshasa',
            'status' => 'published',
            'version' => 1,
            // Champs de lieu avec valeurs par défaut
            'venue_name' => $event->location,
            'venue_address_line1' => '',
            'venue_address_line2' => '',
            'venue_city' => '',
            'venue_region' => '',
            'venue_country' => '',
            'google_maps_url' => $event->google_maps_url ?? '',
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
            'program_html' => null,
            'meta_title' => $event->title . ' - Invitation',
            'meta_description' => 'Invitation pour l\'événement de ' . $event->title,
            'published_at' => now()
        ]);
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Marquer une invitation comme ouverte
     */
    private function markInvitationAsOpened($event_id, $guest_id)
    {
        // Mettre à jour le statut de l'invité pour indiquer qu'il a ouvert l'invitation
        // Note: rsvp_status ne peut être que 'pending', 'confirmed', ou 'declined'
        // On ne change pas le statut RSVP, on peut ajouter un champ 'opened_at' si nécessaire
        $guest = Guest::where('event_id', $event_id)
            ->where('id', $guest_id)
            ->first();
        
        if ($guest) {
            // Mettre à jour la date de réponse pour indiquer que l'invitation a été ouverte
            $guest->update(['response_date' => now()]);
            
            // Log de l'ouverture pour les statistiques
            \Log::info("Invitation ouverte - Event: {$event_id}, Guest: {$guest_id}");
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Générer les liens d'invitation dynamiques pour tous les invités
     */
    public function generateDynamicLinks($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $guests = Guest::where('event_id', $event_id)->get();
            
            $links = [];
            foreach ($guests as $guest) {
                $links[] = [
                    'guest_id' => $guest->id,
                    'guest_name' => "{$guest->first_name} {$guest->last_name}",
                    'guest_email' => $guest->email,
                    'dynamic_url' => url("/invitation/{$guest->id}"),
                    'whatsapp_url' => "https://wa.me/?text=" . urlencode("Vous êtes invité à notre événement ! Cliquez ici : " . url("/invitation/{$guest->id}"))
                ];
            }
            
            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title
                ],
                'links' => $links,
                'total_guests' => count($links)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des liens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✏️ AFFICHAGE DU FORMULAIRE D'ÉDITION D'UNE INVITATION
     * 
     * @param int $id ID de l'invitation à éditer
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $invitation = Invitation::with(['event', 'content'])->findOrFail($id);
        $event = $invitation->event;
        
        // Récupérer le contenu de l'invitation
        $content = $invitation->content;
        
        return view('invitation.edit-advanced', compact('invitation', 'event', 'content'));
    }

    /**
     * 💾 MISE À JOUR D'UNE INVITATION EXISTANTE
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id ID de l'invitation à mettre à jour
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $invitation = Invitation::with(['event', 'content'])->findOrFail($id);
        $event = $invitation->event;
        
        // Validation des données
        $request->validate([
            'intro_1' => 'nullable|string',
            'intro_2' => 'nullable|string',
            'body_html' => 'nullable|string',
            'program_html' => 'nullable|string',
            'theme' => 'nullable|string',
            'cta' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'accent_color' => 'nullable|string',
            'hero_image_path' => 'nullable|file|image|max:2048',
            'program_background_image' => 'nullable|file|image|max:2048',
            'guestbook_background_image' => 'nullable|file|image|max:2048',
            'drinks_background_image' => 'nullable|file|image|max:2048',
            'rsvp_background_image' => 'nullable|file|image|max:2048',
            'footer_background_image' => 'nullable|file|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|image|max:2048',
            'og_image' => 'nullable|file|image|max:2048',
            'guestbook_enabled' => 'boolean',
            'drinks_enabled' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Gestion des images
            $heroPath = $request->file('hero_image_path')?->store('invitations/hero', 'public');
            $programBgPath = $request->file('program_background_image')?->store('invitations/sections', 'public');
            $guestbookBgPath = $request->file('guestbook_background_image')?->store('invitations/sections', 'public');
            $drinksBgPath = $request->file('drinks_background_image')?->store('invitations/sections', 'public');
            $rsvpBgPath = $request->file('rsvp_background_image')?->store('invitations/sections', 'public');
            $footerBgPath = $request->file('footer_background_image')?->store('invitations/sections', 'public');
            $galleryPaths = $request->file('gallery') ? array_map(fn($f) => $f->store('invitations/gallery', 'public'), $request->file('gallery')) : null;
            $ogPath = $request->file('og_image')?->store('invitations/og', 'public');

            // Mise à jour du contenu
            $contentData = [
                'intro_1' => $request->input('intro_1'),
                'intro_2' => $request->input('intro_2'),
                'body_html' => $request->input('body_html'),
                'program_html' => $request->input('program_html'),
                'theme' => $request->input('theme'),
                'cta' => $request->input('cta'),
                'primary_color' => $request->input('primary_color'),
                'secondary_color' => $request->input('secondary_color'),
                'accent_color' => $request->input('accent_color'),
                'guestbook_enabled' => $request->boolean('guestbook_enabled'),
                'drinks_enabled' => $request->boolean('drinks_enabled'),
            ];

            // Ajouter les chemins d'images seulement s'ils sont fournis
            if ($heroPath) $contentData['hero_image_path'] = $heroPath;
            if ($programBgPath) $contentData['program_background_image'] = $programBgPath;
            if ($guestbookBgPath) $contentData['guestbook_background_image'] = $guestbookBgPath;
            if ($drinksBgPath) $contentData['drinks_background_image'] = $drinksBgPath;
            if ($rsvpBgPath) $contentData['rsvp_background_image'] = $rsvpBgPath;
            if ($footerBgPath) $contentData['footer_background_image'] = $footerBgPath;
            if ($galleryPaths) $contentData['gallery'] = json_encode($galleryPaths);
            if ($ogPath) $contentData['og_image'] = $ogPath;

            // Mettre à jour le contenu
            $invitation->content->update($contentData);

            DB::commit();

            return redirect()->route('invitation.index')
                ->with('success', 'Invitation mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}
