<?php

namespace App\Http\Controllers\Guest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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

    public function store(Request $request, $unique_code=null)
{
    $request->validate([
        'event_id' => 'nullable|exists:events,id',
        'guest_id' => 'nullable|exists:guests,id',
        'message' => 'required|string',
    ]);

    $invitation = Invitation::where('unique_code', $unique_code)->first();

    if (!$invitation) {
        return redirect()->back()->with('error', 'Invitation invalide.');
    }

    DB::table('guest_books')->insert([
        'event_id' => $invitation->event_id,
        'guest_id' => $invitation->guest_id,
        'message'  => $request->message,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Message enregistré avec succès !');
}

}
