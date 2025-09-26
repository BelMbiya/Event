<?php

namespace App\Http\Controllers\guest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * ========================================
 * GUEST CONTROLLER - GESTION DES INVITÉS
 * ========================================
 * 
 * Ce contrôleur gère l'administration des invités d'événements.
 * Il traite l'ajout, modification, RSVP et génération de rapports
 * pour le suivi et la gestion des invités.
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestType;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class GuestController extends Controller
{

    public function index()
    {
        $guests = Guest::paginate(10);
        return view('guests.index', compact('guests'));
    }

    /**
     * Afficher le formulaire pour ajouter un invité
     */
    public function create()
    {
        $events = Event::all();
        $guestTypes = GuestType::all();
        $guests = Guest::all();
        return view('guests.create', compact('events', 'guestTypes', 'guests'));
    }

    /**
     * Enregistrer un nouvel invité
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'       => 'required|exists:events,id',
            'guest_type_id'  => 'nullable|exists:guest_type,id',
            'event_table_id' => 'nullable|integer',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'nullable|email|max:255|unique:guests,email',
            'phone'          => 'nullable|string|max:20',
            'rsvp_status'    => 'nullable|in:pending,accepted,declined',
            'meal_choice'    => 'nullable|string|max:255',
            'unique_url'     => 'nullable|string|unique:guests,unique_url',
            'response_date'  => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // renvoie les valeurs du formulaire
        }

        $data = $validator->validated();

        if (empty($data['unique_url'])) {
            $data['unique_url'] = Str::uuid();
        }

        $data['response_date'] = now();

        Guest::create($data);

        return redirect()->back()->with('success', 'Invité enregistré avec succès !');
    }

    public function rsvp($guest_id)
    {
        $guest = Guest::findOrFail($guest_id);
        $guest->rsvp_status = 'confirmed';
        $guest->save();

        return redirect()->back()->with('success', 'Votre présence a été confirmée !');
    }

    public function downloadAllGuestsPDF()
    {
        $guests = Guest::with(['event', 'guestType'])->get();
        
        $pdf = Pdf::loadView('guests.all-guests-pdf', compact('guests'));
        
        return $pdf->download('tous_les_invites_' . date('Y-m-d') . '.pdf');
    }

    public function downloadGuestPDF($guestId)
    {
        $guest = Guest::with(['event', 'guestType', 'drinkChoices.eventDrink.drink'])->findOrFail($guestId);
        
        $pdf = Pdf::loadView('guests.single-guest-pdf', compact('guest'));
        
        return $pdf->download("invite_{$guest->first_name}_{$guest->last_name}_" . date('Y-m-d') . '.pdf');
    }
}
