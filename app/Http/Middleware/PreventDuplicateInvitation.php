<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer l'ID de l'événement depuis la route
        $eventId = $request->route('event_id') ?? $request->route('event');
        
        if ($eventId) {
            // Vérifier si l'événement existe
            $event = Event::with('invitations')->find($eventId);
            
            if (!$event) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Événement introuvable.');
            }
            
            // Vérifier si l'événement a déjà une invitation
            if ($event->invitations && $event->invitations->count() > 0) {
                return redirect()->route('admin.dashboard')
                    ->with('warning', 'Cet événement a déjà une invitation. Vous ne pouvez pas en créer une nouvelle.');
            }
        }
        
        return $next($request);
    }
}