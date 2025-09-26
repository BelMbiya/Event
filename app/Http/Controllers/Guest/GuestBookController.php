<?php

namespace App\Http\Controllers\Guest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * ========================================
 * GUEST BOOK CONTROLLER - GESTION DU LIVRE D'OR
 * ========================================
 * 
 * Ce contrôleur gère le livre d'or des événements.
 * Il permet aux invités de laisser des messages et aux organisateurs
 * de consulter et exporter les messages laissés.
 */
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;

class GuestBookController extends Controller
{
    public function index($event_id)
    {
        $event = Event::findOrFail($event_id);
        
        $guestBooks = \App\Models\GuestBook::with('guest')
            ->where('event_id', $event_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest_book.index', compact('guestBooks', 'event'));
    }

    public function downloadPDF($event_id)
    {
        $event = Event::findOrFail($event_id);
        
        $guestBooks = \App\Models\GuestBook::with('guest')
            ->where('event_id', $event_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('guest_book.pdf', compact('guestBooks', 'event'));
        
        return $pdf->download('livre_dor_' . $event->title . '_' . date('Y-m-d') . '.pdf');
    }


    public function create()
    {
        $events = Event::all();
        $guests = Guest::all();
        return view('guests.book', compact('events', 'guests'));
    }

    public function store(Request $request, $guest_id=null)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'message' => 'required|string',
        ]);

        // ✅ NOUVELLE LOGIQUE : Utiliser directement le guest_id de l'URL
        $guestId = $guest_id ?? $request->guest_id;

        // Récupérer l'invité avec son événement
        $guest = Guest::with('event')->findOrFail($guestId);

        DB::table('guest_books')->insert([
            'event_id' => $guest->event_id,
            'guest_id' => $guestId,
            'message'  => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ✅ NOUVELLE LOGIQUE : Redirection vers l'invitation dynamique
        return redirect()
            ->route('invitation.show.dynamic', $guestId)
            ->with('success', 'Message enregistré avec succès !');
    }

}
