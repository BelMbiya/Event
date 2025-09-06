<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Guest;
use App\Models\EventDrink;
use App\Models\Invitation;
use Barryvdh\DomPDF\Facade\Pdf;

class DrinkChoicesController extends Controller
{
    public function index(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        
        // Récupérer tous les invités de l'événement avec leurs choix de boissons depuis guest_drink_choices
        $guestsQuery = Guest::with(['drinkChoices.eventDrink.drink', 'guestType'])
            ->where('event_id', $event_id);
        
        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'with_choices') {
                $guestsQuery->whereHas('drinkChoices');
            } elseif ($request->status === 'without_choices') {
                $guestsQuery->whereDoesntHave('drinkChoices');
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $guestsQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('drink_id')) {
            $guestsQuery->whereHas('drinkChoices', function($q) use ($request) {
                $q->where('event_drink_id', $request->drink_id);
            });
        }
        
        $guests = $guestsQuery->get()->map(function ($guest) {
            $guest->has_drink_choices = $guest->drinkChoices->count() > 0;
            return $guest;
        });
        
        // Récupérer les boissons de l'événement
        $eventDrinks = EventDrink::with('drink')
            ->where('event_id', $event_id)
            ->get();
        
        // Récupérer les invitations pour les liens WhatsApp
        $invitations = Invitation::where('event_id', $event_id)
            ->whereNotNull('guest_id')
            ->get();
        
        // Statistiques
        $stats = [
            'total_guests' => Guest::where('event_id', $event_id)->count(),
            'with_choices' => Guest::where('event_id', $event_id)->whereHas('drinkChoices')->count(),
            'without_choices' => Guest::where('event_id', $event_id)->whereDoesntHave('drinkChoices')->count(),
            'total_choices' => \App\Models\GuestDrinkChoice::whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })->count(),
            'available_drinks_count' => $eventDrinks->count(),
            'available_drinks_total_price' => $eventDrinks->sum('price'),
            'total_choices_price' => \App\Models\GuestDrinkChoice::whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })->with('eventDrink')->get()->sum(function($choice) {
                return $choice->eventDrink->price ?? 0;
            })
        ];
        
        return view('guests.drink-choices', compact('event', 'guests', 'eventDrinks', 'invitations', 'stats'));
    }
    
    public function downloadPDF($event_id)
    {
        $event = Event::findOrFail($event_id);
        
        $guests = Guest::with(['drinkChoices.eventDrink.drink', 'guestType'])
            ->where('event_id', $event_id)
            ->get()
            ->map(function ($guest) {
                $guest->has_drink_choices = $guest->drinkChoices->count() > 0;
                return $guest;
            });
        
        $eventDrinks = EventDrink::with('drink')
            ->where('event_id', $event_id)
            ->get();
        
        $pdf = Pdf::loadView('guests.drink-choices-pdf', compact('event', 'guests', 'eventDrinks'));
        
        return $pdf->download("choix_boissons_{$event->title}_" . date('Y-m-d') . '.pdf');
    }
    
    public function exportExcel($event_id)
    {
        $event = Event::findOrFail($event_id);
        
        $guests = Guest::with(['drinkChoices.eventDrink.drink', 'guestType'])
            ->where('event_id', $event_id)
            ->get();
        
        // Créer un fichier CSV (simulation d'Excel)
        $filename = "choix_boissons_{$event->title}_" . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($guests) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, ['Nom', 'Prénom', 'Email', 'Téléphone', 'Type d\'invité', 'Choix de boissons', 'Statut']);
            
            // Données
            foreach ($guests as $guest) {
                $drinkChoices = $guest->drinkChoices->map(function ($choice) {
                    return $choice->eventDrink->drink->name;
                })->implode(', ');
                
                fputcsv($file, [
                    $guest->last_name,
                    $guest->first_name,
                    $guest->email,
                    $guest->phone,
                    $guest->guestType->name ?? 'Invité',
                    $drinkChoices ?: 'Aucun choix',
                    $guest->drinkChoices->count() > 0 ? 'Complété' : 'En attente'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
