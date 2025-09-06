<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventDrink;

class EventDrinkController extends Controller
{
   public function store(Request $request)
{
    $eventId = $request->input('event_id');
    $drinkIds = $request->input('drinks', []);

    foreach ($drinkIds as $drink) {

        // Si $drink n'est pas numérique ou n'existe pas, sauter
        if (!is_numeric($drink) || !\App\Models\Drink::find($drink)) {
            continue;
        }

        EventDrink::create([
            'event_id' => $eventId,
            'drink_id' => $drink,
            'price' => 0,
            'available' => true,
            'limit_per_guest' => 1,
            'display_order' => 0,
        ]);
    }

    return redirect()->back()->with('success', 'Boissons enregistrées avec succès !');
}


}
