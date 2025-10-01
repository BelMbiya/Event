{{--
========================================
ADMIN INDEX VIEW - TABLEAU DE BORD ADMINISTRATEUR
========================================

Cette vue affiche le tableau de bord principal avec :
- Statistiques globales (événements, invités, invitations, paiements)
- Filtres avancés pour la recherche d'événements
- Liste des événements avec métriques détaillées
- Actions rapides et liens vers les fonctionnalités

UTILISATION : Page d'accueil de l'administration
--}}
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

    <!-- Recherche simplifiée -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-search"></i> Recherche
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" id="searchForm">
                <div class="row">
                    <div class="col-md-10">
                        <label class="form-label">Rechercher</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom d'événement, invité..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                </div>
                @if(request('search'))
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Recherche : "<strong>{{ request('search') }}</strong>"
                        </div>
                    </div>
                </div>
                @endif
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
                                        @if($event->invitations->count() == 0)
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
                                        @else
                                            <span class="badge bg-success me-2">
                                                <i class="fas fa-check"></i> Invitation créée
                                            </span>
                                            <a href="{{ route('invitation.edit', $event->invitations->first()->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                        @endif
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

@endsection

<script src="{{ asset('js/admin-dashboard.js') }}"></script>



<!-- Plus de modal AJAX - messages gérés côté serveur -->
