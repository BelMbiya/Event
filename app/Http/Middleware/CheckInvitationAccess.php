<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class CheckInvitationAccess
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

        // Récupérer l'ID de l'invitation depuis la route
        $invitationId = $request->route('id') ?? $request->route('invitation_id');
        $uniqueCode = $request->route('unique_code');
        
        if ($invitationId) {
            // Vérifier si l'invitation existe
            $invitation = Invitation::find($invitationId);
            
            if (!$invitation) {
                return redirect()->route('invitation.index')
                    ->with('error', 'L\'invitation demandée n\'existe pas.');
            }

            // Vérifier si l'événement associé existe
            if (!$invitation->event) {
                return redirect()->route('invitation.index')
                    ->with('error', 'L\'événement associé à cette invitation n\'existe plus.');
            }

            // Ajouter l'invitation à la requête
            $request->merge(['invitation' => $invitation]);
        }

        if ($uniqueCode) {
            // Vérifier si l'invitation avec ce code unique existe
            $invitation = Invitation::where('unique_code', $uniqueCode)->first();
            
            if (!$invitation) {
                return redirect()->route('dashboard')
                    ->with('error', 'L\'invitation demandée n\'existe pas ou a expiré.');
            }

            // Vérifier si l'événement associé existe
            if (!$invitation->event) {
                return redirect()->route('dashboard')
                    ->with('error', 'L\'événement associé à cette invitation n\'existe plus.');
            }

            // Ajouter l'invitation à la requête
            $request->merge(['invitation' => $invitation]);
        }

        return $next($request);
    }
}