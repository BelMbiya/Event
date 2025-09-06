<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
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
            // Rediriger vers la page de connexion avec un message
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.')
                ->with('intended', $request->url());
        }

        return $next($request);
    }
}