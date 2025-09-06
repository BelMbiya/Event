<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\EventDrink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Event::with(['eventType', 'guests', 'invitations', 'eventDrinks']);
        
        // Filtres de recherche
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
        
        // Filtre par statut d'événement
        if ($request->filled('event_status')) {
            $today = Carbon::today();
            switch ($request->event_status) {
                case 'upcoming':
                    $query->where('event_date', '>', $today);
                    break;
                case 'past':
                    $query->where('event_date', '<', $today);
                    break;
                case 'today':
                    $query->whereDate('event_date', $today);
                    break;
            }
        }
        
        // Filtre par type d'événement
        if ($request->filled('event_type')) {
            $query->where('event_type_id', $request->event_type);
        }
        
        // Filtre par dates
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }
        
        // Filtre par lieu
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }
        
        // Filtre par statut RSVP
        if ($request->filled('rsvp_status')) {
            $query->whereHas('guests', function($guestQuery) use ($request) {
                $guestQuery->where('rsvp_status', $request->rsvp_status);
            });
        }
        
        // Filtre par statut d'invitations
        if ($request->filled('invitation_status')) {
            switch ($request->invitation_status) {
                case 'sent':
                    $query->whereHas('invitations', function($invQuery) {
                        $invQuery->whereNotNull('sent_at');
                    });
                    break;
                case 'not_sent':
                    $query->whereHas('invitations', function($invQuery) {
                        $invQuery->whereNull('sent_at');
                    });
                    break;
                case 'opened':
                    $query->whereHas('invitations', function($invQuery) {
                        $invQuery->whereNotNull('opened_at');
                    });
                    break;
            }
        }
        
        // Filtre par statut des boissons
        if ($request->filled('drinks_status')) {
            switch ($request->drinks_status) {
                case 'with_drinks':
                    $query->whereHas('eventDrinks');
                    break;
                case 'without_drinks':
                    $query->whereDoesntHave('eventDrinks');
                    break;
            }
        }
        
        $events = $query->orderBy('event_date', 'desc')->get();
        
        // Statistiques pour les filtres
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
