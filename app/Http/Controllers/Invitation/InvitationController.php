<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Guest;

class InvitationController extends Controller
{

    public function index()
    {
        return view('invitation.index');
    }

    public function create()
    {
        // Logic to show the form for creating an invitation
        $events = Event::all();
        $guests = Guest::all();

        return view('invitation.create', compact('events', 'guests'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        // Créer l'invitation
        DB::table('events')->insert([
            'title' => $request->title,
            'event_date' => $request->event_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('invitation.index')->with('success', 'Invitation créée avec succès !');
    }

    public function show($unique_code)
    {
        // Chercher l'invitation correspondante
        $invitation = DB::table('invitations')
            ->where('unique_code', $unique_code)
            ->first();

        if (!$invitation) {
            abort(404); // Si le code n'existe pas
        }

        return view('invitation.show', compact('invitation'));
    }
}
