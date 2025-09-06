<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class CheckEventAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette ressource.');
        }

        // Récupérer l'ID de l'événement depuis la route ou la requête
        $eventId = $request->route('event_id') ?? $request->input('event_id');
        
        if ($eventId) {
            // Vérifier si l'événement existe
            $event = Event::find($eventId);
            
            if (!$event) {
                return redirect()->route('dashboard')
                    ->with('error', 'L\'événement demandé n\'existe pas.');
            }

            // Ajouter l'événement à la requête pour utilisation dans les contrôleurs
            $request->merge(['event' => $event]);
        }

        return $next($request);
    }
}