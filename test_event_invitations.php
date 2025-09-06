<?php
/**
 * EXEMPLE D'UTILISATION - Comment vérifier si un événement n'a pas d'invitations
 * 
 * Ce fichier montre toutes les façons de vérifier le statut des invitations d'un événement
 */

// ========================================
// 1. MÉTHODES DIRECTES DANS LE MODÈLE EVENT
// ========================================

// Récupérer un événement
$event = Event::find(1);

// ✅ Vérifier si l'événement a des invitations
if ($event->hasInvitations()) {
    echo "L'événement a des invitations\n";
} else {
    echo "L'événement n'a PAS d'invitations\n";
}

// ✅ Vérifier si l'événement n'a pas d'invitations (méthode directe)
if ($event->hasNoInvitations()) {
    echo "L'événement n'a PAS d'invitations\n";
}

// ✅ Compter le nombre d'invitations
$invitationsCount = $event->invitationsCount();
echo "Nombre d'invitations: {$invitationsCount}\n";

// ✅ Vérifier si l'événement a des invités
if ($event->hasGuests()) {
    echo "L'événement a des invités\n";
} else {
    echo "L'événement n'a PAS d'invités\n";
}

// ✅ Obtenir le statut complet
$status = $event->getInvitationsStatus();
echo "Statut: " . $status['message'] . "\n";
echo "Peut créer des invitations: " . ($status['can_create_invitations'] ? 'Oui' : 'Non') . "\n";

// ========================================
// 2. MÉTHODES AVEC REQUÊTES DIRECTES
// ========================================

// ✅ Vérifier avec une requête directe
$hasInvitations = Invitation::where('event_id', $event->id)->exists();
if ($hasInvitations) {
    echo "L'événement a des invitations (requête directe)\n";
}

// ✅ Compter avec une requête directe
$count = Invitation::where('event_id', $event->id)->count();
echo "Nombre d'invitations (requête directe): {$count}\n";

// ✅ Vérifier avec une requête sur la relation
$hasInvitations = $event->invitations()->exists();
if ($hasInvitations) {
    echo "L'événement a des invitations (relation)\n";
}

// ========================================
// 3. MÉTHODES AVEC CONDITIONS COMPLEXES
// ========================================

// ✅ Vérifier si l'événement a des invités mais pas d'invitations
if ($event->hasGuests() && $event->hasNoInvitations()) {
    echo "L'événement a des invités mais pas d'invitations - Peut créer des invitations\n";
}

// ✅ Vérifier si l'événement n'a ni invités ni invitations
if ($event->hasNoGuests() && $event->hasNoInvitations()) {
    echo "L'événement n'a ni invités ni invitations - Ajoutez d'abord des invités\n";
}

// ✅ Vérifier si toutes les invitations sont créées
$guestsCount = $event->guestsCount();
$invitationsCount = $event->invitationsCount();
if ($guestsCount > 0 && $invitationsCount === $guestsCount) {
    echo "Toutes les invitations sont créées pour cet événement\n";
} elseif ($guestsCount > 0 && $invitationsCount < $guestsCount) {
    echo "Il manque " . ($guestsCount - $invitationsCount) . " invitation(s)\n";
}

// ========================================
// 4. UTILISATION DANS UN CONTRÔLEUR
// ========================================

// Dans un contrôleur, vous pouvez faire :
public function checkEventStatus($eventId)
{
    $event = Event::findOrFail($eventId);
    $status = $event->getInvitationsStatus();
    
    switch ($status['status']) {
        case 'no_guests':
            return response()->json([
                'message' => 'Aucun invité ajouté à cet événement',
                'action' => 'add_guests'
            ]);
            
        case 'no_invitations':
            return response()->json([
                'message' => 'Aucune invitation créée',
                'action' => 'create_invitations',
                'guests_count' => $status['guests_count']
            ]);
            
        case 'has_invitations':
            return response()->json([
                'message' => 'Invitations existantes',
                'action' => 'manage_invitations',
                'invitations_count' => $status['invitations_count'],
                'guests_count' => $status['guests_count']
            ]);
    }
}

// ========================================
// 5. UTILISATION AVEC L'API ROUTE
// ========================================

// Vous pouvez maintenant utiliser cette route :
// GET /events/{event_id}/invitations-status

// Exemple de réponse JSON :
/*
{
    "success": true,
    "event": {
        "id": 1,
        "title": "Mariage de Marie & Jean"
    },
    "status": {
        "status": "no_invitations",
        "message": "Aucune invitation créée pour cet événement",
        "invitations_count": 0,
        "guests_count": 3,
        "can_create_invitations": true
    }
}
*/

// ========================================
// 6. UTILISATION DANS UNE VUE BLADE
// ========================================

/*
@if($event->hasNoInvitations())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Aucune invitation créée pour cet événement.
        <a href="{{ route('invitation.create-for-all-guests', $event->id) }}" class="btn btn-primary btn-sm">
            Créer des invitations
        </a>
    </div>
@elseif($event->hasInvitations())
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ $event->invitationsCount() }} invitation(s) créée(s)
    </div>
@endif
*/

// ========================================
// 7. UTILISATION AVEC JAVASCRIPT/AJAX
// ========================================

/*
// Vérifier le statut via AJAX
fetch(`/events/${eventId}/invitations-status`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const status = data.status;
            
            if (status.status === 'no_invitations') {
                showCreateInvitationsButton(status.guests_count);
            } else if (status.status === 'has_invitations') {
                showManageInvitations(status.invitations_count);
            } else {
                showAddGuestsMessage();
            }
        }
    });
*/
