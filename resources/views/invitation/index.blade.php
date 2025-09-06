@extends('admin')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des invitations</h1>
    <div>
        <button class="btn btn-success me-2" onclick="downloadFilteredPDF()">
            <i class="fas fa-download me-2"></i>
            Télécharger PDF
        </button>
        <a href="{{ route("dashboard") }}" class="btn btn-primary">Créer une nouvelle invitation</a>
    </div>
</div>

<!-- Filtres - Masqués si on est dans un événement spécifique -->
@if(!request('event_id'))
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filtres
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('invitation.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="event_id">Événement</label>
                        <select name="event_id" id="event_id" class="form-control">
                            <option value="">Tous les événements</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="guest_name">Nom de l'invité</label>
                        <input type="text" name="guest_name" id="guest_name" class="form-control" 
                               value="{{ request('guest_name') }}" placeholder="Rechercher par nom...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_from">Date de début</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_to">Date de fin</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('invitation.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    <span class="ml-3 text-muted">
                        <i class="fas fa-info-circle"></i> 
                        {{ $invitations->count() }} invitation(s) trouvée(s)
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
@endif


    <!-- Ici tu peux mettre tes cards, tables, etc. -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i>
                    Liste des invitations
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    @if(!request('event_id'))
                                    <th>Événement</th>
                                    @endif
                                    <th>Invité</th>
                                    <th>Code</th>
                                    <th>URL</th>
                                    <th>RSVP</th>
                                    <th>Envoyé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemple de ligne d'invitation -->
                                @foreach ($invitations as $invitation)
                                <tr>
                                    @if(!request('event_id'))
                                    <td>
                                        @if($invitation->event)
                                            {{ $invitation->event->title }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        @if($invitation->guest)
                                            {{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}
                                        @else
                                            Invitation générale
                                        @endif
                                    </td>
                                    <td><code>{{ $invitation->unique_code }}</code></td>
                                    <td>
                                        <a href="{{ $invitation->invitation_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> Voir
                                        </a>
                                    </td>
                                    <td>
                                        @if($invitation->guest && $invitation->guest->rsvp_status)
                                            <span class="badge 
                                                @if($invitation->guest->rsvp_status == 'confirmed') badge-success
                                                @elseif($invitation->guest->rsvp_status == 'declined') badge-danger
                                                @elseif($invitation->guest->rsvp_status == 'pending') badge-warning
                                                @else badge-secondary
                                                @endif">
                                                @if($invitation->guest->rsvp_status == 'confirmed')
                                                    <i class="fas fa-check"></i> Confirmé
                                                @elseif($invitation->guest->rsvp_status == 'declined')
                                                    <i class="fas fa-times"></i> Décliné
                                                @elseif($invitation->guest->rsvp_status == 'pending')
                                                    <i class="fas fa-clock"></i> En attente
                                                @else
                                                    {{ ucfirst($invitation->guest->rsvp_status) }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-question"></i> Non défini
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->sent_at)
                                            {{ \Carbon\Carbon::parse($invitation->sent_at)->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invitation.show', $invitation->unique_code) }}" class="btn btn-success btn-sm" title="Voir l'invitation">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invitation.content.edit', $invitation->id) }}" class="btn btn-warning btn-sm" title="Éditer le contenu">
                                                <i class="fas fa-palette"></i>
                                            </a>
                                            <a href="{{ route('content.create', $invitation->id) }}" class="btn btn-primary btn-sm" title="Éditer l'invitation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('invitation.download', $invitation->unique_code) }}" class="btn btn-info btn-sm" title="Télécharger PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteInvitation({{ $invitation->id }})" title="Supprimer l'invitation">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function downloadFilteredPDF() {
    // Récupérer les paramètres de filtrage actuels
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    // Ajouter tous les paramètres de filtrage
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Construire l'URL de téléchargement avec les filtres
    const downloadUrl = `/invitations/download-all-pdf?${params.toString()}`;
    window.open(downloadUrl, '_blank');
}

function deleteInvitation(invitationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette invitation ? Cette action est irréversible.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/invitation/${invitationId}`;
        form.submit();
    }
}
</script>
@endsection