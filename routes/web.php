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
        // 📋 AFFICHAGE DE LA LISTE DES INVITATIONS
        // Route: GET /invitation
        // Fonction: Affiche la liste de toutes les invitations avec filtres
        // Vue: invitation.index
        Route::get('invitation', 'index')->name('invitation.index');
        
        // 🎨 CRÉATION D'UNE NOUVELLE INVITATION
        // Route: GET /invitation/create/{event_id}
        // Fonction: Affiche le formulaire de création d'invitation pour un événement spécifique
        // Vue: invitation.create-advanced
        // Middleware: Empêche la création si l'événement a déjà une invitation
        Route::get('invitation/create/{event_id}', 'create')
            ->name('invitation.create')
            ->middleware('prevent.duplicate.invitation');
        
        // 💾 SAUVEGARDE D'UNE NOUVELLE INVITATION
        // Route: POST /invitation
        // Fonction: Sauvegarde une nouvelle invitation avec son contenu
        // Retour: JSON (AJAX) ou redirection
        // Middleware: Empêche la création si l'événement a déjà une invitation
        Route::post('invitation', 'store')->name('invitation.store')
            ->middleware('prevent.duplicate.invitation');
        
        // 👁️ AFFICHAGE D'UNE INVITATION DYNAMIQUE
        // Route: GET /invitation/{guest_id}
        // Fonction: Affiche une invitation publique qui s'adapte selon l'invité
        // Vue: invitation.invitation_paper.Invitation_3
        Route::get('invitation/{guest_id}', 'showDynamic')
            ->name('invitation.show.dynamic');
        
        // 👁️ AFFICHAGE D'UNE INVITATION (LEGACY - pour compatibilité)
        // Route: GET /invitation/{unique_code}
        // Fonction: Affiche une invitation publique via son code unique
        // Vue: invitation.invitation_paper.Invitation_3
        Route::get('invitation/{unique_code}', 'show')
            ->name('invitation.show');
        
        // ✏️ ÉDITION D'UNE INVITATION
        // Route: GET /invitation/{id}/edit
        // Fonction: Affiche le formulaire d'édition d'une invitation
        // Vue: invitation.edit-advanced
        Route::get('invitation/{id}/edit', 'edit')
            ->name('invitation.edit');
        
        // 💾 MISE À JOUR D'UNE INVITATION
        // Route: PUT/PATCH /invitation/{id}
        // Fonction: Met à jour une invitation existante
        // Retour: Redirection vers la liste
        Route::put('invitation/{id}', 'update')
            ->name('invitation.update');
        
        // 🗑️ SUPPRESSION D'UNE INVITATION
        // Route: DELETE /invitation/{id}
        // Fonction: Supprime une invitation et son contenu associé
        // Retour: Redirection vers la liste
        Route::delete('invitation/{id}', 'destroy')
            ->name('invitation.destroy');
        
        // 🚀 CRÉATION AUTOMATIQUE POUR TOUS LES INVITÉS
        // Route: POST /events/{event_id}/create-invitations-for-all-guests
        // Fonction: Crée automatiquement des invitations pour tous les invités d'un événement
        // Retour: JSON avec statistiques de création
        // Middleware: Empêche la création si l'événement a déjà une invitation
        Route::post('events/{event_id}/create-invitations-for-all-guests', 'createInvitationsForAllGuests')
            ->name('invitation.create-for-all-guests')
            ->middleware('prevent.duplicate.invitation');
        
        // 🎯 CRÉATION POUR INVITÉS SÉLECTIONNÉS
        // Route: POST /events/{event_id}/create-invitations-for-selected-guests
        // Fonction: Crée des invitations pour des invités spécifiquement sélectionnés
        // Retour: JSON avec détails de création
        // Middleware: Empêche la création si l'événement a déjà une invitation
        Route::post('events/{event_id}/create-invitations-for-selected-guests', 'createInvitationsForSelectedGuests')
            ->name('invitation.create-for-selected-guests')
            ->middleware('prevent.duplicate.invitation');
        
        // 🔍 VÉRIFICATION DU STATUT DES INVITATIONS
        // Route: GET /events/{event_id}/invitations-status
        // Fonction: Vérifie si un événement a des invitations et leur statut
        // Retour: JSON avec statut détaillé
        Route::get('events/{event_id}/invitations-status', 'checkEventInvitationsStatus')
            ->name('invitation.check-status');
        
        // 📋 DUPLICATION D'UNE INVITATION
        // Route: POST /invitation/{invitation_id}/duplicate
        // Fonction: Duplique une invitation existante avec son contenu
        // Retour: JSON avec ID de la nouvelle invitation
        Route::post('invitation/{invitation_id}/duplicate', 'duplicateInvitation')
            ->name('invitation.duplicate');
        
        // 👀 PRÉVISUALISATION D'UNE INVITATION
        // Route: GET /invitation/{invitation_id}/preview
        // Fonction: Affiche une prévisualisation d'une invitation avant publication
        // Vue: invitation.preview
        Route::get('invitation/{invitation_id}/preview', 'preview')
            ->name('invitation.preview');
        
        // 📢 PUBLICATION D'UNE INVITATION
        // Route: POST /invitation/{invitation_id}/publish
        // Fonction: Publie une invitation (change le statut à 'published')
        // Retour: JSON de confirmation
        Route::post('invitation/{invitation_id}/publish', 'publish')
            ->name('invitation.publish');
        
        // 📦 ARCHIVAGE D'UNE INVITATION
        // Route: POST /invitation/{invitation_id}/archive
        // Fonction: Archive une invitation (change le statut à 'archived')
        // Retour: JSON de confirmation
        Route::post('invitation/{invitation_id}/archive', 'archive')
            ->name('invitation.archive');
        
        // 📊 STATISTIQUES DES INVITATIONS
        // Route: GET /invitation/stats/{event_id?}
        // Fonction: Retourne les statistiques des invitations (total, pending, sent, etc.)
        // Retour: JSON avec statistiques
        Route::get('invitation/stats/{event_id?}', 'getStats')
            ->name('invitation.stats');
        
        // 🔗 GÉNÉRATION DE LIENS DYNAMIQUES
        // Route: GET /invitation/{event_id}/generate-links
        // Fonction: Génère les liens d'invitation dynamiques pour tous les invités
        // Retour: JSON avec tous les liens personnalisés
        Route::get('invitation/{event_id}/generate-links', 'generateDynamicLinks')
            ->name('invitation.generate-links');
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

    // ========================================
    // ROUTES POUR LA GESTION DU CONTENU D'INVITATION (ANCIEN SYSTÈME)
    // ========================================
    
    // 🎨 CRÉATION DE CONTENU (ANCIEN SYSTÈME - COMPATIBILITÉ)
    // Route: GET /edit-content/{invitation_id}
    // Fonction: Affiche le formulaire de création de contenu pour une invitation
    // Vue: invitation.content
    // Note: Système legacy, utilise ContentController (fusionné)
    Route::get('/edit-content/{invitation_id}', [ContentController::class, 'create'])->name('content.create');
    
    // 💾 SAUVEGARDE DE CONTENU (ANCIEN SYSTÈME - COMPATIBILITÉ)
    // Route: POST /edit-content
    // Fonction: Sauvegarde le contenu d'une invitation
    // Retour: Redirection vers la liste des invitations
    // Note: Système legacy, utilise ContentController (fusionné)
    Route::post('/edit-content', [ContentController::class, 'store'])->name('content.store');

    // ========================================
    // ROUTES POUR LA GESTION DU CONTENU D'INVITATION (NOUVEAU SYSTÈME)
    // ========================================
    
    // ✏️ ÉDITION DU CONTENU D'UNE INVITATION
    // Route: GET /invitation/{invitation_id}/content/edit
    // Fonction: Affiche le formulaire d'édition de contenu pour une invitation spécifique
    // Vue: invitation.edit-content
    // Note: Système moderne avec templates et fonctionnalités avancées
    Route::get('/invitation/{invitation_id}/content/edit', [App\Http\Controllers\Invitation\ContentController::class, 'edit'])->name('invitation.content.edit');
    
    // 💾 MISE À JOUR DU CONTENU D'UNE INVITATION
    // Route: PUT /invitation/{invitation_id}/content
    // Fonction: Met à jour le contenu d'une invitation avec validation complète
    // Retour: JSON (AJAX) ou redirection vers l'invitation
    // Note: Système moderne avec gestion des fichiers, thèmes, etc.
    Route::put('/invitation/{invitation_id}/content', [App\Http\Controllers\Invitation\ContentController::class, 'update'])->name('invitation.content.update');
    
    
    // 📋 DUPLICATION DE CONTENU
    // Route: POST /content/{content_id}/duplicate
    // Fonction: Duplique un contenu d'invitation existant
    // Retour: JSON avec ID de la nouvelle invitation créée
    Route::post('/content/{content_id}/duplicate', [App\Http\Controllers\Invitation\ContentController::class, 'duplicate'])->name('content.duplicate');
    
    // 🎨 APPLICATION DE TEMPLATE
    // Route: POST /content/{content_id}/apply-template
    // Fonction: Applique un template prédéfini à un contenu
    // Retour: JSON de confirmation
    Route::post('/content/{content_id}/apply-template', [App\Http\Controllers\Invitation\ContentController::class, 'applyTemplate'])->name('content.apply-template');
    
    // 💾 SAUVEGARDE COMME BROUILLON
    // Route: POST /invitation/{invitation_id}/content/save-draft
    // Fonction: Sauvegarde le contenu comme brouillon sans validation complète
    // Retour: JSON de confirmation
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

