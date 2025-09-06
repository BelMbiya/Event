<?php
/**
 * Script de test pour vérifier le statut des invitations d'un événement
 */

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Models\Invitation;
use App\Models\Guest;

echo "=== TEST DU STATUT DES INVITATIONS D'UN ÉVÉNEMENT ===\n\n";

// Récupérer le premier événement
$event = Event::first();

if (!$event) {
    echo "❌ Aucun événement trouvé dans la base de données\n";
    exit;
}

echo "📅 Événement: {$event->title}\n";
echo "🆔 ID: {$event->id}\n";
echo "📅 Date: {$event->event_date}\n\n";

// ========================================
// 1. MÉTHODES DIRECTES
// ========================================
echo "=== 1. MÉTHODES DIRECTES ===\n";

echo "✅ A des invitations: " . ($event->hasInvitations() ? 'OUI' : 'NON') . "\n";
echo "❌ N'a pas d'invitations: " . ($event->hasNoInvitations() ? 'OUI' : 'NON') . "\n";
echo "📊 Nombre d'invitations: " . $event->invitationsCount() . "\n";
echo "👥 A des invités: " . ($event->hasGuests() ? 'OUI' : 'NON') . "\n";
echo "👥 N'a pas d'invités: " . ($event->hasNoGuests() ? 'OUI' : 'NON') . "\n";
echo "📊 Nombre d'invités: " . $event->guestsCount() . "\n\n";

// ========================================
// 2. STATUT COMPLET
// ========================================
echo "=== 2. STATUT COMPLET ===\n";
$status = $event->getInvitationsStatus();
echo "📋 Statut: {$status['status']}\n";
echo "💬 Message: {$status['message']}\n";
echo "📊 Invitations: {$status['invitations_count']}\n";
echo "👥 Invités: {$status['guests_count']}\n";
echo "🔧 Peut créer des invitations: " . ($status['can_create_invitations'] ? 'OUI' : 'NON') . "\n\n";

// ========================================
// 3. REQUÊTES DIRECTES
// ========================================
echo "=== 3. REQUÊTES DIRECTES ===\n";

$hasInvitationsDirect = Invitation::where('event_id', $event->id)->exists();
echo "✅ A des invitations (requête directe): " . ($hasInvitationsDirect ? 'OUI' : 'NON') . "\n";

$countDirect = Invitation::where('event_id', $event->id)->count();
echo "📊 Nombre d'invitations (requête directe): {$countDirect}\n";

$hasGuestsDirect = Guest::where('event_id', $event->id)->exists();
echo "👥 A des invités (requête directe): " . ($hasGuestsDirect ? 'OUI' : 'NON') . "\n";

$guestsCountDirect = Guest::where('event_id', $event->id)->count();
echo "📊 Nombre d'invités (requête directe): {$guestsCountDirect}\n\n";

// ========================================
// 4. CONDITIONS COMPLEXES
// ========================================
echo "=== 4. CONDITIONS COMPLEXES ===\n";

if ($event->hasNoGuests() && $event->hasNoInvitations()) {
    echo "⚠️  L'événement n'a ni invités ni invitations - Ajoutez d'abord des invités\n";
} elseif ($event->hasGuests() && $event->hasNoInvitations()) {
    echo "✅ L'événement a des invités mais pas d'invitations - Peut créer des invitations\n";
} elseif ($event->hasInvitations()) {
    $guestsCount = $event->guestsCount();
    $invitationsCount = $event->invitationsCount();
    
    if ($invitationsCount === $guestsCount) {
        echo "🎉 Toutes les invitations sont créées pour cet événement\n";
    } elseif ($invitationsCount < $guestsCount) {
        $missing = $guestsCount - $invitationsCount;
        echo "⚠️  Il manque {$missing} invitation(s) pour cet événement\n";
    } else {
        echo "🤔 Il y a plus d'invitations que d'invités (situation inhabituelle)\n";
    }
}

echo "\n=== FIN DU TEST ===\n";
