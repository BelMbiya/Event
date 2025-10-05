<?php

namespace App\Observers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Content;
use Illuminate\Support\Str;

/**
 * ========================================
 * GUEST OBSERVER - GESTION AUTOMATIQUE DES INVITATIONS
 * ========================================
 * 
 * Cet observer gère automatiquement l'association des invités
 * aux invitations générales existantes lors de leur création.
 */
class GuestObserver
{
    /**
     * Handle the Guest "created" event.
     */
    public function created(Guest $guest): void
    {
        // Vérifier s'il existe une invitation générale pour cet événement
        $generalInvitation = Invitation::where('event_id', $guest->event_id)
            ->whereNull('guest_id')
            ->first();
        
        if ($generalInvitation) {
            // Vérifier si l'invité n'a pas déjà une invitation
            $existingInvitation = Invitation::where('event_id', $guest->event_id)
                ->where('guest_id', $guest->id)
                ->first();
            
            if (!$existingInvitation) {
                // Créer une nouvelle invitation pour cet invité basée sur l'invitation générale
                $guestUuid = Str::uuid();
                $newInvitation = Invitation::create([
                    'event_id' => $guest->event_id,
                    'guest_id' => $guest->id,
                    'unique_code' => $guestUuid,
                    'status' => 'sent',
                    'invitation_url' => url("/invitation/{$guestUuid}"),
                ]);
                
                // Copier le contenu de l'invitation générale vers la nouvelle invitation
                $generalContent = Content::where('invitation_id', $generalInvitation->id)->first();
                if ($generalContent) {
                    $contentData = $generalContent->toArray();
                    unset($contentData['id'], $contentData['created_at'], $contentData['updated_at']);
                    $contentData['invitation_id'] = $newInvitation->id;
                    $contentData['guest_id'] = $guest->id;
                    $contentData['unique_code'] = $guestUuid;
                    $contentData['slug'] = Str::slug($contentData['couple'] . '-' . $guestUuid);
                    
                    Content::create($contentData);
                }
                
                \Log::info("Invitation automatiquement créée pour le nouvel invité {$guest->id} basée sur l'invitation générale de l'événement {$guest->event_id}");
            }
        }
    }

    /**
     * Handle the Guest "updated" event.
     */
    public function updated(Guest $guest): void
    {
        // Logique pour les mises à jour si nécessaire
    }

    /**
     * Handle the Guest "deleted" event.
     */
    public function deleted(Guest $guest): void
    {
        // Supprimer l'invitation associée à cet invité
        $invitation = Invitation::where('event_id', $guest->event_id)
            ->where('guest_id', $guest->id)
            ->first();
        
        if ($invitation) {
            // Supprimer le contenu associé
            if ($invitation->content) {
                $invitation->content->delete();
            }
            
            // Supprimer l'invitation
            $invitation->delete();
            
            \Log::info("Invitation supprimée automatiquement pour l'invité supprimé {$guest->id}");
        }
    }

    /**
     * Handle the Guest "restored" event.
     */
    public function restored(Guest $guest): void
    {
        // Logique pour la restauration si nécessaire
    }

    /**
     * Handle the Guest "force deleted" event.
     */
    public function forceDeleted(Guest $guest): void
    {
        // Logique pour la suppression forcée si nécessaire
    }
}
