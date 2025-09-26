<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Drink;
use App\Models\EventDrink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ========================================
 * EVENT DRINK CONTROLLER - GESTION UNIFIÉE DES BOISSONS D'ÉVÉNEMENT
 * ========================================
 * 
 * Ce contrôleur gère TOUS les aspects des boissons pour les événements :
 * - Gestion complète des boissons d'événement
 * - Statistiques et rapports avancés
 * - Export PDF et Excel
 * - Assignation en masse
 * - Duplication entre événements
 * - Création en masse (fusionnée depuis Invitation/EventDrinkController)
 * 
 * FONCTIONNALITÉS PRINCIPALES :
 * - ✅ CRUD complet des boissons d'événement
 * - ✅ Statistiques détaillées et rapports
 * - ✅ Export PDF et Excel
 * - ✅ Assignation en masse
 * - ✅ Duplication entre événements
 * - ✅ Gestion des choix des invités
 * 
 * @author Assistant IA - Optimisation et fusion
 * @version 2.0 - Unifié et optimisé
 */
class EventDrinkController extends Controller
{
    /**
     * Afficher la page de gestion des boissons pour un événement
     */
    public function index($event_id)
    {
        $event = Event::findOrFail($event_id);
        $eventDrinks = EventDrink::with('drink')
            ->where('event_id', $event_id)
            ->get();
        
        $allDrinks = Drink::where('active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
        // Statistiques avancées
        $stats = [
            'total_drinks' => $eventDrinks->count(),
            'alcoholic_drinks' => $eventDrinks->where('drink.alcoholic', true)->count(),
            'non_alcoholic_drinks' => $eventDrinks->where('drink.alcoholic', false)->count(),
            'total_guests' => \App\Models\Guest::where('event_id', $event_id)->count(),
            'guests_with_choices' => \App\Models\GuestDrinkChoice::whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })->distinct('guest_id')->count(),
        ];
        
        // Choix de boissons des invités
        $drinkChoices = \App\Models\GuestDrinkChoice::with(['guest', 'eventDrink.drink'])
            ->whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })
            ->get()
            ->groupBy('event_drink_id');
        
        // Top des boissons les plus demandées
        $popularDrinks = $drinkChoices->map(function($choices, $eventDrinkId) {
            $eventDrink = \App\Models\EventDrink::with('drink')->find($eventDrinkId);
            return [
                'drink' => $eventDrink->drink ?? null,
                'count' => $choices->count(),
                'guests' => $choices->pluck('guest')
            ];
        })->sortByDesc('count')->take(5);
        
        return view('events.drinks', compact('event', 'eventDrinks', 'allDrinks', 'stats', 'drinkChoices', 'popularDrinks'));
    }

    /**
     * Ajouter une boisson à un événement
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'drink_id' => 'required|exists:drinks,id',
            'quantity' => 'nullable|integer|min:1',
            'price_override' => 'nullable|numeric|min:0',
        ]);

        try {
            // Vérifier si la boisson n'est pas déjà ajoutée à cet événement
            $existingEventDrink = EventDrink::where('event_id', $request->event_id)
                ->where('drink_id', $request->drink_id)
                ->first();

            if ($existingEventDrink) {
                return redirect()->back()
                    ->with('error', 'Cette boisson est déjà ajoutée à cet événement.');
            }

            $drink = Drink::findOrFail($request->drink_id);

            EventDrink::create([
                'event_id' => $request->event_id,
                'drink_id' => $request->drink_id,
                'price' => $request->price_override ?? null,
                'limit_per_guest' => $request->quantity ?? null,
                'available' => true,
            ]);

            return redirect()->back()
                ->with('success', "Boisson '{$drink->name}' ajoutée avec succès à l'événement !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ajout de la boisson: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une boisson d'un événement
     */
    public function destroy($event_id, $drink_id)
    {
        try {
            $eventDrink = EventDrink::where('event_id', $event_id)
                ->where('drink_id', $drink_id)
                ->firstOrFail();

            $drinkName = $eventDrink->drink->name;
            $eventDrink->delete();

            return redirect()->back()
                ->with('success', "Boisson '{$drinkName}' supprimée de l'événement !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une boisson d'un événement
     */
    public function update(Request $request, $event_id, $drink_id)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'price_override' => 'nullable|numeric|min:0',
        ]);

        try {
            $eventDrink = EventDrink::where('event_id', $event_id)
                ->where('drink_id', $drink_id)
                ->firstOrFail();

            $eventDrink->update([
                'price' => $request->price_override,
                'limit_per_guest' => $request->quantity,
            ]);

            return redirect()->back()
                ->with('success', "Boisson '{$eventDrink->drink->name}' mise à jour avec succès !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les choix de boissons en PDF
     */
    public function exportPDF($event_id)
    {
        $event = Event::findOrFail($event_id);
        $eventDrinks = EventDrink::with('drink')
            ->where('event_id', $event_id)
            ->get();
        
        $drinkChoices = \App\Models\GuestDrinkChoice::with(['guest', 'eventDrink.drink'])
            ->whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })
            ->get()
            ->groupBy('guest_id');
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.drinks-pdf', compact('event', 'eventDrinks', 'drinkChoices'));
        
        return $pdf->download('choix-boissons-' . \Illuminate\Support\Str::slug($event->title) . '.pdf');
    }

    /**
     * Exporter les choix de boissons en Excel
     */
    public function exportExcel($event_id)
    {
        $event = Event::findOrFail($event_id);
        $drinkChoices = \App\Models\GuestDrinkChoice::with(['guest', 'eventDrink.drink'])
            ->whereHas('guest', function($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })
            ->get();
        
        $filename = 'choix-boissons-' . \Illuminate\Support\Str::slug($event->title) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($drinkChoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invité', 'Email', 'Boisson', 'Type', 'Quantité', 'Date de choix']);
            
            foreach ($drinkChoices as $choice) {
                fputcsv($file, [
                    $choice->guest->first_name . ' ' . $choice->guest->last_name,
                    $choice->guest->email ?? '',
                    $choice->eventDrink->drink->name,
                    $choice->eventDrink->drink->type,
                    $choice->quantity,
                    $choice->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Assigner des boissons en masse à un événement
     */
    public function bulkAssign(Request $request, $event_id)
    {
        $request->validate([
            'drink_ids' => 'required|array',
            'drink_ids.*' => 'exists:drinks,id',
            'default_quantity' => 'nullable|integer|min:1',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            $assigned = 0;
            $skipped = 0;
            
            foreach ($request->drink_ids as $drink_id) {
                // Vérifier si déjà assigné
                $existing = EventDrink::where('event_id', $event_id)
                    ->where('drink_id', $drink_id)
                    ->first();
                
                if (!$existing) {
                    EventDrink::create([
                        'event_id' => $event_id,
                        'drink_id' => $drink_id,
                        'limit_per_guest' => $request->default_quantity,
                        'price' => $request->default_price,
                        'available' => true,
                    ]);
                    $assigned++;
                } else {
                    $skipped++;
                }
            }
            
            DB::commit();
            
            $message = "{$assigned} boisson(s) assignée(s) avec succès !";
            if ($skipped > 0) {
                $message .= " {$skipped} boisson(s) déjà assignée(s) ignorée(s).";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de l\'assignation: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer les boissons d'un autre événement
     */
    public function duplicateFromEvent(Request $request, $event_id)
    {
        $request->validate([
            'source_event_id' => 'required|exists:events,id',
        ]);

        try {
            DB::beginTransaction();
            
            $sourceEventDrinks = EventDrink::where('event_id', $request->source_event_id)->get();
            $duplicated = 0;
            $skipped = 0;
            
            foreach ($sourceEventDrinks as $sourceDrink) {
                // Vérifier si déjà assigné
                $existing = EventDrink::where('event_id', $event_id)
                    ->where('drink_id', $sourceDrink->drink_id)
                    ->first();
                
                if (!$existing) {
                    EventDrink::create([
                        'event_id' => $event_id,
                        'drink_id' => $sourceDrink->drink_id,
                        'limit_per_guest' => $sourceDrink->limit_per_guest,
                        'price' => $sourceDrink->price,
                        'available' => $sourceDrink->available,
                    ]);
                    $duplicated++;
                } else {
                    $skipped++;
                }
            }
            
            DB::commit();
            
            $message = "{$duplicated} boisson(s) dupliquée(s) avec succès !";
            if ($skipped > 0) {
                $message .= " {$skipped} boisson(s) déjà assignée(s) ignorée(s).";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de la duplication: ' . $e->getMessage());
        }
    }

    /**
     * Méthode de création en masse pour les invitations (fusionnée depuis Invitation/EventDrinkController)
     * Crée plusieurs boissons d'événement en une seule fois
     */
    public function storeBulk(Request $request)
    {
        $eventId = $request->input('event_id');
        $drinkIds = $request->input('drinks', []);

        try {
            $created = 0;
            $skipped = 0;

            foreach ($drinkIds as $drink) {
                // Si $drink n'est pas numérique ou n'existe pas, sauter
                if (!is_numeric($drink) || !\App\Models\Drink::find($drink)) {
                    continue;
                }

                // Vérifier si déjà assigné
                $existing = EventDrink::where('event_id', $eventId)
                    ->where('drink_id', $drink)
                    ->first();

                if (!$existing) {
                    EventDrink::create([
                        'event_id' => $eventId,
                        'drink_id' => $drink,
                        'price' => 0,
                        'available' => true,
                        'limit_per_guest' => 1,
                        'display_order' => 0,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            $message = "{$created} boisson(s) enregistrée(s) avec succès !";
            if ($skipped > 0) {
                $message .= " {$skipped} boisson(s) déjà assignée(s) ignorée(s).";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
}
