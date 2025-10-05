<?php

/**
 * ========================================
 * ROUTES WEB - DÉFINITION DES ROUTES DE L'APPLICATION
 * ========================================
 *
 * Ce fichier définit toutes les routes de l'application web.
 * Il organise les routes par fonctionnalités : authentification, administration,
 * invitations, invités, boissons, et routes publiques.
 *
 * STRUCTURE :
 * - Routes d'authentification (login/logout)
 * - Routes d'administration (tableaux de bord)
 * - Routes des invitations (CRUD complet)
 * - Routes des invités et choix de boissons
 * - Routes publiques (sans authentification)
 */
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Invitation\ContentController;
use App\Http\Controllers\Guest\GuestDrinkChoiceController;
use App\Http\Controllers\guest\GuestController;
use App\Http\Controllers\guest\GuestBookController;
use App\Http\Controllers\Invitation\InvitationController;
use App\Http\Controllers\EventDrinkController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\Event\EventSelectionController;


Route::redirect('/', 'dashboard');

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'login')->name('login')->middleware('guest');
    Route::post('login', 'doLogin')->name('auth.login');
    Route::post('logout', 'logout')->name('auth.logout');
});

Route::controller(IndexController::class)->middleware('auth')->group(function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');
    // ⚠️ enlevé "invitation" ici car ce n'est pas IndexController qui doit le gérer
});

// Dashboard amélioré
Route::controller(App\Http\Controllers\Admin\EnhancedDashboardController::class)->middleware('auth')->group(function () {
    Route::get('dashboard-enhanced', 'index')->name('dashboard.enhanced');
    Route::get('api/dashboard/stats', 'getStats')->name('dashboard.stats');
    Route::get('api/dashboard/activity', 'getRecentActivity')->name('dashboard.activity');
});

Route::controller(EventSelectionController::class)->middleware('auth')->group(function () {
    Route::get('event/selection', 'index')->name('event.selection');
    Route::post('event/select', 'selectEvent')->name('event.select');
    Route::get('drink-choices', 'redirectToDrinkChoices')->name('drink-choices.redirect');
    Route::get('guest-book', 'redirectToGuestBook')->name('guest-book.redirect');
});

// ========================================
// ROUTES POUR LA GESTION DES INVITATIONS
// ========================================
Route::controller(App\Http\Controllers\Invitation\InvitationController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('invitation', 'index')->name('invitation.index');

        Route::get('invitation/create/{event_id}', 'create')
            ->name('invitation.create')
            ->middleware('prevent.duplicate.invitation');

        Route::post('invitation', 'store')->name('invitation.store');

        Route::get('invitation/{id}', 'showDynamic')
            ->name('invitation.show.dynamic');

        Route::get('invitation/general/{unique_code}', 'showGeneralInvitation')
            ->name('invitation.show.general');

        Route::get('invitation/{unique_code}', 'show')
            ->name('invitation.show');

        // Route pour afficher l'invitation générale d'un événement
        Route::get('invitation/event/{event_id}', 'showGeneric')
            ->name('invitation.show.generic');

        Route::get('invitation/{id}/edit', 'update')
            ->name('invitation.edit');
        Route::put('invitation/{id}', 'update')
            ->name('invitation.update');

        Route::delete('invitation/{id}', 'destroy')
            ->name('invitation.destroy');

        Route::post('events/{event_id}/create-invitations-for-all-guests', 'createInvitationsForAllGuests')
            ->name('invitation.create-for-all-guests')
            ->middleware('prevent.duplicate.invitation');

        Route::post('events/{event_id}/create-invitations-for-selected-guests', 'createInvitationsForSelectedGuests')
            ->name('invitation.create-for-selected-guests')
            ->middleware('prevent.duplicate.invitation');

        Route::get('events/{event_id}/invitations-status', 'checkEventInvitationsStatus')
            ->name('invitation.check-status');

        Route::post('invitation/{invitation_id}/duplicate', 'duplicateInvitation')
            ->name('invitation.duplicate');

        Route::get('invitation/{invitation_id}/preview', 'preview')
            ->name('invitation.preview');

        Route::post('invitation/{invitation_id}/publish', 'publish')
            ->name('invitation.publish');

        Route::post('invitation/{invitation_id}/archive', 'archive')
            ->name('invitation.archive');

        Route::get('invitation/stats/{event_id?}', 'getStats')
            ->name('invitation.stats');

        Route::get('invitation/{event_id}/generate-links', 'generateDynamicLinks')
            ->name('invitation.generate-links');

        // ✅ NOUVELLES ROUTES : Association automatique des invités aux invitations générales
        Route::post('events/{event_id}/guests/{guest_id}/associate-invitation', 'associateGuestToGeneralInvitation')
            ->name('invitation.associate-guest');

        Route::post('events/{event_id}/associate-all-guests', 'associateAllGuestsToGeneralInvitation')
            ->name('invitation.associate-all-guests');
    });
Route::middleware('auth')->group(function () {
    Route::get('/invitation/{unique_code}/download', [InvitationController::class, 'downloadInvitation'])
         ->name('invitation.download');
    Route::get('/invitations/download-all-pdf', [InvitationController::class, 'downloadAllInvitationsPDF'])
         ->name('invitations.download-all-pdf');
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('/guests/create', [GuestController::class, 'create'])->name('guests.create');
    Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
    Route::post('/guests/{guest}/rsvp', [GuestController::class, 'rsvp'])->name('guests.rsvp');
    Route::get('/guests/download-pdf', [GuestController::class, 'downloadAllGuestsPDF'])->name('guests.download-pdf');
    Route::get('/guests/{guest}/download-pdf', [GuestController::class, 'downloadGuestPDF'])->name('guests.download-guest-pdf');
});

// Routes pour les choix de boissons
Route::middleware('auth')->group(function () {
    Route::get('/events/{event_id}/drink-choices', [App\Http\Controllers\Guest\DrinkChoicesController::class, 'index'])->name('drink-choices.index');
    Route::get('/events/{event_id}/drink-choices/download-pdf', [App\Http\Controllers\Guest\DrinkChoicesController::class, 'downloadPDF'])->name('drink-choices.download-pdf');
    Route::get('/events/{event_id}/drink-choices/export-excel', [App\Http\Controllers\Guest\DrinkChoicesController::class, 'exportExcel'])->name('drink-choices.export-excel');

    Route::get('/edit-content/{invitation_id}', [ContentController::class, 'create'])->name('content.create');
    Route::post('/edit-content', [ContentController::class, 'store'])->name('content.store');

    Route::get('/invitation/{invitation_id}/content/edit', [App\Http\Controllers\Invitation\ContentController::class, 'edit'])->name('invitation.content.edit');

    Route::put('/invitation/{invitation_id}/content', [App\Http\Controllers\Invitation\ContentController::class, 'update'])->name('invitation.content.update');

    Route::post('/content/{content_id}/duplicate', [App\Http\Controllers\Invitation\ContentController::class, 'duplicate'])->name('content.duplicate');

    Route::post('/content/{content_id}/apply-template', [App\Http\Controllers\Invitation\ContentController::class, 'applyTemplate'])->name('content.apply-template');

    Route::post('/invitation/{invitation_id}/content/save-draft', [App\Http\Controllers\Invitation\ContentController::class, 'saveDraft'])->name('content.save-draft');

    Route::get('/guest_book/event/{event_id}', [GuestBookController::class, 'index'])
        ->name('book.index');
    Route::get('/guest_book/create', [GuestBookController::class, 'create'])->name('book.create');

    // ✅ NOUVELLE ROUTE : Livre d'or avec logique dynamique
    Route::post('/guest_book/store/{guest_id}', [GuestBookController::class, 'store'])
        ->name('book.store.dynamic');

    // ✅ ROUTE LEGACY : Livre d'or avec code unique (pour compatibilité)
    Route::post('/guest_book/store/{unique_code?}', [GuestBookController::class, 'store'])
        ->name('book.store');

    Route::get('/guest_book/event/{event_id}/download-pdf', [GuestBookController::class, 'downloadPDF'])
        ->name('book.download-pdf');

    Route::post('/event-drinks/store', [EventDrinkController::class, 'store'])->name('event-drinks.store');
    Route::post('/event-drinks/store-bulk', [EventDrinkController::class, 'storeBulk'])->name('event-drinks.store-bulk');
});

// Routes publiques (sans authentification)
Route::post('/guest-drink-choices', [GuestDrinkChoiceController::class, 'store'])
    ->name('guest_drink_choices.store');
Route::delete('/guests/{guest_id}/drink-choices', [GuestDrinkChoiceController::class, 'destroyByGuest'])
    ->name('guest-drink-choices.destroy-by-guest');

    // ✅ NOUVELLE ROUTE PUBLIQUE : Livre d'or dynamique
Route::post('/guest-book/{guest_id}', [App\Http\Controllers\Guest\GuestBookController::class, 'store'])
    ->name('guest-book.store.dynamic');

//Route::post('/guest/drink-choices', [GuestDrinkChoiceController::class, 'store'])
//      ->name('guest.drink.choices.store');

// Routes pour la gestion des boissons d'événement
Route::middleware('auth')->group(function () {
    Route::get('events/{event_id}/drinks', [EventDrinkController::class, 'index'])
        ->name('event-drinks.index');
    Route::post('events/drinks', [EventDrinkController::class, 'store'])->name('event-drinks.store');
    Route::put('events/{event_id}/drinks/{drink_id}', [EventDrinkController::class, 'update'])
        ->name('event-drinks.update');
    Route::delete('events/{event_id}/drinks/{drink_id}', [EventDrinkController::class, 'destroy'])
        ->name('event-drinks.destroy');
    Route::get('events/{event_id}/drinks/export-pdf', [EventDrinkController::class, 'exportPDF'])
        ->name('event-drinks.export-pdf');
    Route::get('events/{event_id}/drinks/export-excel', [EventDrinkController::class, 'exportExcel'])
        ->name('event-drinks.export-excel');
    Route::post('events/{event_id}/drinks/bulk-assign', [EventDrinkController::class, 'bulkAssign'])
        ->name('event-drinks.bulk-assign');
    Route::post('events/{event_id}/drinks/duplicate', [EventDrinkController::class, 'duplicateFromEvent'])
        ->name('event-drinks.duplicate');

    // Routes pour la gestion des boissons
    Route::get('drinks', [DrinkController::class, 'index'])->name('drinks.index');
    Route::post('drinks', [DrinkController::class, 'store'])->name('drinks.store');
    Route::get('drinks/{id}/edit', [DrinkController::class, 'edit'])->name('drinks.edit');
    Route::put('drinks/{id}', [DrinkController::class, 'update'])->name('drinks.update');
    Route::delete('drinks/{id}', [DrinkController::class, 'destroy'])->name('drinks.destroy');
});

