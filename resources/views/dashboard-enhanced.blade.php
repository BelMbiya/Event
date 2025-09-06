@extends('admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i>
            Tableau de Bord
        </h1>
        <div>
            <button class="btn btn-success" onclick="refreshStats()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Événements Actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeEvents">{{ $stats['active_events'] }}</div>
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
                                Total Invités
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalGuests">{{ $stats['total_guests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Invitations Envoyées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="sentInvitations">{{ $stats['sent_invitations'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="confirmedRSVP">{{ $stats['confirmed_rsvp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements récents -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-week"></i> Événements Récents
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Événement</th>
                                        <th>Date</th>
                                        <th>Invitations</th>
                                        <th>RSVP</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvents as $event)
                                        <tr>
                                            <td>
                                                <strong>{{ $event->title }}</strong>
                                                <br><small class="text-muted">{{ $event->location }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $event->invitations_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">{{ $event->confirmed_rsvp_count }}</span>
                                                / <span class="badge badge-secondary">{{ $event->total_guests_count }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('event-drinks.index', $event->id) }}" class="btn btn-sm btn-info" title="Boissons">
                                                        <i class="fas fa-glass-cheers"></i>
                                                    </a>
                                                    <a href="{{ route('invitation.index', ['event_id' => $event->id]) }}" class="btn btn-sm btn-primary" title="Invitations">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucun événement récent.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-bolt"></i> Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('event.selection') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Créer un Événement
                        </a>
                        <a href="{{ route('invitation.index') }}" class="btn btn-info">
                            <i class="fas fa-envelope"></i> Gérer les Invitations
                        </a>
                        <a href="{{ route('guests.index') }}" class="btn btn-warning">
                            <i class="fas fa-users"></i> Gérer les Invités
                        </a>
                        <a href="{{ route('drinks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-glass-cheers"></i> Catalogue Boissons
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-history"></i> Activité Récente
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        @foreach($recentActivity as $activity)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-gray-800">{{ $activity['message'] }}</div>
                                    <div class="small text-gray-500">{{ $activity['time'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune activité récente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshStats() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualisation...';
    btn.disabled = true;
    
    // Simuler une actualisation (dans une vraie app, ce serait une requête AJAX)
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        // Ici vous pourriez recharger les statistiques via AJAX
    }, 2000);
}
</script>
@endsection
