<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;

/**
 * ========================================
 * EVENT SELECTION CONTROLLER - SÉLECTION D'ÉVÉNEMENTS
 * ========================================
 * 
 * Ce contrôleur gère la sélection et redirection vers les événements.
 * Il permet de choisir un événement et rediriger vers les fonctionnalités
 * appropriées (boissons, livre d'or, invitations).
 */
use App\Models\Event;
use Illuminate\Http\Request;

class EventSelectionController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('event_date', 'desc')->get();
        return view('event.selection', compact('events'));
    }

    public function selectEvent(Request $request)
    {
        $eventId = $request->input('event_id');
        $section = $request->input('section', 'dashboard');
        
        if (!$eventId) {
            return redirect()->route('event.selection')->with('error', 'Veuillez sélectionner un événement.');
        }

        $event = Event::findOrFail($eventId);

        // Rediriger vers la section appropriée avec l'ID de l'événement
        switch ($section) {
            case 'drink-choices':
                return redirect()->route('drink-choices.index', $eventId);
            case 'guest-book':
                return redirect()->route('book.index', $eventId);
            case 'invitations':
                return redirect()->route('invitation.index');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function redirectToDrinkChoices()
    {
        // Rediriger vers le premier événement disponible pour les choix de boissons
        $event = Event::first();
        if ($event) {
            return redirect()->route('drink-choices.index', $event->id);
        }
        return redirect()->route('event.selection')->with('error', 'Aucun événement trouvé.');
    }

    public function redirectToGuestBook()
    {
        // Rediriger vers le premier événement disponible pour le livre d'or
        $event = Event::first();
        if ($event) {
            return redirect()->route('book.index', $event->id);
        }
        return redirect()->route('event.selection')->with('error', 'Aucun événement trouvé.');
    }
}
