<?php

namespace App\Http\Controllers\Guest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GuestDrinkChoice;

class GuestDrinkChoiceController extends Controller
{

    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id'    => 'required|exists:guests,id',
            'drinks'      => 'required|array',
            'unique_code' => 'required|exists:invitations,unique_code',
        ]);

        foreach ($request->drinks as $eventDrinkId) {
            GuestDrinkChoice::updateOrCreate(
                [
                    'guest_id'       => $request->guest_id,
                    'event_drink_id' => $eventDrinkId,
                ],
                [
                    'quantity' => 1,               // quantité par défaut
                    'comment'  => null,            // commentaire par défaut
                ]
            );
        }

        return redirect()
            ->route('invitation.show', $request->unique_code)
            ->with('success', 'Vos choix ont été enregistrés !');
    }

    public function destroyByGuest($guest_id)
    {
        try {
            // Supprimer tous les choix de boissons pour cet invité
            $deletedCount = GuestDrinkChoice::where('guest_id', $guest_id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} choix de boissons supprimés avec succès."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression des choix de boissons.'
            ], 500);
        }
    }
}
