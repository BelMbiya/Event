@extends('admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sélection d'Événement</h1>
    </div>

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Sélectionnez un événement
                    </h6>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="row">
                            @foreach($events as $event)
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ $event->title }}
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    <i class="fas fa-users me-1"></i>{{ $event->guests()->count() }} invités
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('drink-choices.index', $event->id) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-glass-cheers me-1"></i>Boissons
                                                </a>
                                                <a href="{{ route('book.index', $event->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-book me-1"></i>Livre d'Or
                                                </a>
                                                <a href="{{ route('invitation.create', $event->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-envelope me-1"></i>Invitation
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Aucun événement trouvé</h5>
                            <p class="text-gray-500">Créez votre premier événement pour commencer.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Créer un événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                        </a>
                        <a href="{{ route('guests.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Gestion des invités
                        </a>
                        <a href="{{ route('invitation.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-envelope me-2"></i>Gestion des invitations
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 font-weight-bold text-primary">{{ $events->count() }}</div>
                            <div class="text-xs text-gray-600">Événements</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 font-weight-bold text-success">{{ $events->sum(function($event) { return $event->guests()->count(); }) }}</div>
                            <div class="text-xs text-gray-600">Total invités</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
