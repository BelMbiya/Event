<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enregistrer les middlewares personnalisés
        $middleware->alias([
            'check.event.access' => \App\Http\Middleware\CheckEventAccess::class,
            'check.invitation.access' => \App\Http\Middleware\CheckInvitationAccess::class,
            'redirect.if.not.authenticated' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
            'validate.sensitive.data' => \App\Http\Middleware\ValidateSensitiveData::class,
            'prevent.duplicate.invitation' => \App\Http\Middleware\PreventDuplicateInvitation::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
