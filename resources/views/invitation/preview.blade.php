@extends('layouts.app')

@section('title', 'Prévisualisation de l\'invitation')

@section('content')
<div class="container-fluid">
    <!-- Header avec actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-eye me-2"></i>Prévisualisation de l'Invitation
                    </h1>
                    <p class="text-muted mb-0">
                        Invitation pour : <strong>{{ $invitation->guest->first_name ?? 'Invité' }} {{ $invitation->guest->last_name ?? '' }}</strong>
                    </p>
                </div>
                <div>
                    <a href="{{ route('invitation.content.edit', $invitation->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <button class="btn btn-success me-2" onclick="publishInvitation({{ $invitation->id }})">
                        <i class="fas fa-paper-plane"></i> Publier
                    </button>
                    <a href="{{ route('invitation.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de l'invitation -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations de l'Invitation
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Code unique :</strong></td>
                            <td><code>{{ $invitation->unique_code }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Statut :</strong></td>
                            <td>
                                <span class="badge badge-{{ $invitation->status == 'pending' ? 'warning' : ($invitation->status == 'sent' ? 'success' : 'info') }}">
                                    {{ ucfirst($invitation->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Événement :</strong></td>
                            <td>{{ $invitation->event->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date de création :</strong></td>
                            <td>{{ $invitation->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($invitation->content)
                        <tr>
                            <td><strong>Statut du contenu :</strong></td>
                            <td>
                                <span class="badge badge-{{ $invitation->content->status == 'draft' ? 'secondary' : ($invitation->content->status == 'published' ? 'success' : 'info') }}">
                                    {{ ucfirst($invitation->content->status) }}
                                </span>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Informations de l'Invité
                    </h6>
                </div>
                <div class="card-body">
                    @if($invitation->guest)
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nom :</strong></td>
                            <td>{{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email :</strong></td>
                            <td>{{ $invitation->guest->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Téléphone :</strong></td>
                            <td>{{ $invitation->guest->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Type :</strong></td>
                            <td>{{ $invitation->guest->guestType->label ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>RSVP :</strong></td>
                            <td>
                                @if($invitation->guest->rsvp_status)
                                <span class="badge badge-{{ $invitation->guest->rsvp_status == 'confirmed' ? 'success' : ($invitation->guest->rsvp_status == 'declined' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($invitation->guest->rsvp_status) }}
                                </span>
                                @else
                                <span class="badge badge-secondary">En attente</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    @else
                    <p class="text-muted">Aucune information d'invité disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Prévisualisation de l'invitation -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-eye me-2"></i>Prévisualisation
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="preview-container" style="max-height: 80vh; overflow-y: auto;">
                        @if($invitation->content)
                            <!-- Utiliser le template d'invitation existant -->
                            @include('invitation.invitation_paper.Invitation_3', [
                                'invitation' => $invitation,
                                'guest' => $invitation->guest,
                                'content' => $invitation->content,
                                'event' => $invitation->event,
                                'eventDrinks' => $invitation->event->eventDrinks ?? collect(),
                                'unique_code' => $invitation->unique_code
                            ])
                        @else
                            <div class="text-center p-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4>Aucun contenu trouvé</h4>
                                <p class="text-muted">Cette invitation n'a pas encore de contenu personnalisé.</p>
                                <a href="{{ route('invitation.content.edit', $invitation->id) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Créer le contenu
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de publication -->
<div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="publishModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="publishModalLabel">
                    <i class="fas fa-paper-plane text-success"></i> Confirmer la Publication
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir publier cette invitation ?</p>
                <p class="text-muted">Une fois publiée, l'invitation sera visible publiquement et pourra être partagée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirmPublish">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function publishInvitation(invitationId) {
    $('#publishModal').modal('show');
    
    $('#confirmPublish').off('click').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Publication...');
        
        fetch(`/invitation/${invitationId}/publish`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher un message de succès
                showNotification('success', data.message);
                
                // Recharger la page pour mettre à jour le statut
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('error', 'Une erreur est survenue lors de la publication.');
        })
        .finally(() => {
            btn.prop('disabled', false);
            btn.html(originalText);
            $('#publishModal').modal('hide');
        });
    });
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}
</script>
@endsection
