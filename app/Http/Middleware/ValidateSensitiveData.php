<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSensitiveData
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
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        // Nettoyer et valider les données sensibles
        $this->sanitizeInput($request);

        // Vérifier les tentatives de manipulation d'URL
        $this->validateUrlIntegrity($request);

        return $next($request);
    }

    /**
     * Nettoyer les données d'entrée
     */
    private function sanitizeInput(Request $request): void
    {
        // Nettoyer les paramètres de route
        $routeParams = $request->route()->parameters();
        foreach ($routeParams as $key => $value) {
            if (is_string($value)) {
                $request->route()->setParameter($key, strip_tags(trim($value)));
            }
        }

        // Nettoyer les données de requête
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = strip_tags(trim($value));
            }
        }
        $request->merge($input);
    }

    /**
     * Vérifier l'intégrité de l'URL
     */
    private function validateUrlIntegrity(Request $request): void
    {
        // Vérifier les tentatives d'injection SQL dans les paramètres
        $suspiciousPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/script\s*>/i',
            '/javascript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i'
        ];

        $url = $request->fullUrl();
        $params = $request->all();

        // Exclure les routes de téléchargement de la validation stricte
        $routeName = $request->route() ? $request->route()->getName() : '';
        $isDownloadRoute = in_array($routeName, [
            'invitation.download',
            'invitations.download-all-pdf',
            'guests.download-pdf',
            'guests.download-guest-pdf',
            'drink-choices.download-pdf',
            'book.download-pdf'
        ]);

        if (!$isDownloadRoute) {
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $url) || $this->checkArrayForPattern($params, $pattern)) {
                    \Log::warning('Tentative d\'injection détectée', [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url' => $url,
                        'pattern' => $pattern
                    ]);
                    
                    abort(403, 'Accès refusé - Données suspectes détectées.');
                }
            }
        }
    }

    /**
     * Vérifier un tableau pour des patterns suspects
     */
    private function checkArrayForPattern(array $data, string $pattern): bool
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                if ($this->checkArrayForPattern($value, $pattern)) {
                    return true;
                }
            } elseif (is_string($value) && preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }
}