@extends('admin')
@section('content')
    <!-- Messages de session -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {!! session('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {!! session('info') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i>
            Dashboard
        </h1>
        <div>
            <button class="btn btn-success" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtres de Recherche
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Recherche par nom</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom d'événement, invité..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut Événement</label>
                        <select name="event_status" class="form-select">
                            <option value="">Tous</option>
                            <option value="upcoming" {{ request('event_status') == 'upcoming' ? 'selected' : '' }}>À venir</option>
                            <option value="past" {{ request('event_status') == 'past' ? 'selected' : '' }}>Passés</option>
                            <option value="today" {{ request('event_status') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut RSVP</label>
                        <select name="rsvp_status" class="form-select">
                            <option value="">Tous</option>
                            <option value="confirmed" {{ request('rsvp_status') == 'confirmed' ? 'selected' : '' }}>Confirmés</option>
                            <option value="pending" {{ request('rsvp_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="declined" {{ request('rsvp_status') == 'declined' ? 'selected' : '' }}>Déclinés</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type d'Événement</label>
                        <select name="event_type" class="form-select">
                            <option value="">Tous</option>
                            @foreach(\App\Models\EventType::all() as $type)
                                <option value="{{ $type->id }}" {{ request('event_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-2">
                        <label class="form-label">Date de fin</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Invitations</label>
                        <select name="invitation_status" class="form-select">
                            <option value="">Tous</option>
                            <option value="sent" {{ request('invitation_status') == 'sent' ? 'selected' : '' }}>Envoyées</option>
                            <option value="not_sent" {{ request('invitation_status') == 'not_sent' ? 'selected' : '' }}>Non envoyées</option>
                            <option value="opened" {{ request('invitation_status') == 'opened' ? 'selected' : '' }}>Ouvertes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Boissons</label>
                        <select name="drinks_status" class="form-select">
                            <option value="">Tous</option>
                            <option value="with_drinks" {{ request('drinks_status') == 'with_drinks' ? 'selected' : '' }}>Avec boissons</option>
                            <option value="without_drinks" {{ request('drinks_status') == 'without_drinks' ? 'selected' : '' }}>Sans boissons</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lieu</label>
                        <input type="text" name="location" class="form-control" placeholder="Ville, lieu..." value="{{ request('location') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Effacer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Événements
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Événements à Venir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['upcoming_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Invités
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_guests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                RSVP Confirmés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['confirmed_rsvp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($events as $event)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $event->title }}</h6>
                                    <p class="card-text small text-muted">
                                        {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->format('d/m/Y') }}
                                    </p>
                                    <!-- Première ligne de boutons -->
                                    <div class="mb-2">
                                        <a href="{{ route('invitation.create', $event->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Invitation
                                        </a>
                                        <form action="{{ route('invitation.create-for-all-guests', $event->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" 
                                                    title="Vérifier et créer automatiquement les invitations manquantes">
                                                <i class="fas fa-magic"></i> Auto-Créer
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Deuxième ligne de boutons -->
                                    <div class="mb-2">
                                        <a href="{{ route('event-drinks.index', $event->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-glass-cheers"></i> Gérer Boissons
                                        </a>
                                        <a href="{{ route('drink-choices.index', $event->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-list"></i> Choix Invités
                                        </a>
                                    </div>
                                    
                                    <!-- Troisième ligne de boutons -->
                                    <div>
                                        <a href="{{ route('book.index', $event->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-book"></i> Livre d'or
                                        </a>
                                        <a href="{{ route('invitation.index', ['event_id' => $event->id]) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-list"></i> Toutes Invitations
                                        </a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Nombre de tables</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Nombre d'invitations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Nombre d'invités
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">0</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: 0" aria-valuenow="0" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Durée avant événement</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
function refreshDashboard() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualisation...';
    btn.disabled = true;
    
    // Recharger la page pour actualiser les données
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Auto-submit du formulaire de filtres avec délai
let filterTimeout;
document.querySelectorAll('#filterForm input, #filterForm select').forEach(element => {
    element.addEventListener('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
});

// Auto-submit pour les champs de recherche avec délai plus long
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 1000);
});

// Plus de JavaScript AJAX - soumission normale du formulaire

// Fonction pour afficher le modal de chargement
function showCreationModal(eventTitle) {
    const modal = document.getElementById('creationModal');
    const content = document.getElementById('creationContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <h5>Création des invitations en cours...</h5>
            <p class="text-muted">Vérification et création des invitations pour l'événement : <strong>${eventTitle}</strong></p>
        </div>
    `;
    
    $('#creationModal').modal('show');
}

// Fonction pour afficher le résultat de la création
function showCreationResult(data, type) {
    const modal = document.getElementById('creationModal');
    const content = document.getElementById('creationContent');
    const title = document.querySelector('#creationModalLabel');
    
    if (type === 'success') {
        title.innerHTML = '<i class="fas fa-check-circle text-success"></i> Création Réussie !';
        title.className = 'modal-title text-success';
        
        let resultHtml = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle me-2"></i>Opération terminée avec succès !</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
        
        if (data.count > 0) {
            resultHtml += `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Détails de la création :</h6>
                    <ul class="mb-0">
                        <li><strong>${data.count}</strong> nouvelle(s) invitation(s) créée(s)</li>
                        <li><strong>${data.existing_count}</strong> invitation(s) existante(s) (ignorée(s))</li>
                    </ul>
                </div>
            `;
        }
        
        content.innerHTML = resultHtml;
    } else {
        title.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Erreur';
        title.className = 'modal-title text-danger';
        
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la création</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
    }
}

// Fonction pour actualiser le dashboard
function refreshDashboard() {
    location.reload();
}
</script>

<!-- Plus de modal AJAX - messages gérés côté serveur -->
