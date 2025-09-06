<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;

class ContentInvitationController extends Controller
{
    public function index()
    {
        return view('invitation.content');
    }

    public function create($invitation_id)
    {
        return view('invitation.content', compact('invitation_id'));
    }

    public function store(Request $request)
    {
        // Validation
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
        
        // ✅ CORRECTION : Utiliser le modèle Content au lieu de DB::table
        $contentUuid = \Illuminate\Support\Str::uuid();
        
        \App\Models\Content::create([
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
