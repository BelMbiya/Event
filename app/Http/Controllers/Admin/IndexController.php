<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * ========================================
 * INDEX CONTROLLER - TABLEAU DE BORD ADMINISTRATEUR
 * ========================================
 * 
 * Ce contrôleur gère le tableau de bord principal de l'administration.
 * Il affiche les statistiques globales, filtres avancés et vue d'ensemble
 * de tous les événements avec leurs métriques (invités, invitations, boissons).
 */
use App\Models\Event;
use App\Models\EventType;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\EventDrink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Event::with(['eventType', 'guests', 'invitations', 'eventDrinks']);
        
        // Recherche simple
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('guests', function($guestQuery) use ($search) {
                      $guestQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $events = $query->orderBy('event_date', 'desc')->get();
        
        // Statistiques pour les filtres et paiements/factures
        $stats = [
            'total_events' => Event::count(),
            'upcoming_events' => Event::where('event_date', '>', Carbon::today())->count(),
            'total_guests' => Guest::count(),
            'confirmed_rsvp' => Guest::where('rsvp_status', 'confirmed')->count(),
            'pending_rsvp' => Guest::where('rsvp_status', 'pending')->count(),
            'sent_invitations' => Invitation::whereNotNull('sent_at')->count(),
            'events_with_drinks' => Event::whereHas('eventDrinks')->count(),
        ];
        
        return view('admin.index', compact('events', 'stats'));
    }
}
