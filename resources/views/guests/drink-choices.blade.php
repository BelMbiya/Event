@extends('admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec titre et boutons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-glass-cheers me-2"></i>
                Choix de Boissons - {{ $event->title }}
            </h1>
            <p class="text-muted">Gestion des préférences de boissons des invités</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="downloadPDF()">
                <i class="fas fa-download me-2"></i>
                Télécharger PDF
            </button>
            <button class="btn btn-info" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-2"></i>
                Exporter Excel
            </button>
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
                                Total Invités
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_guests'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Choix Effectués
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['with_choices'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['without_choices'] }}
                            </div>
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
                                Boissons Disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_choices'] }}/{{ number_format($stats['total_choices_price'], 0) }}€
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wine-bottle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recherche simplifiée -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Recherche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('drink-choices.index', $event->id) }}" class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Rechercher</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom, prénom ou email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Tableau des invités et leurs choix -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des invités et leurs choix de boissons</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="guestsTable">
                    <thead>
                        <tr>
                            <th>Invité</th>
                            <th>Contact</th>
                            <th>Choix de boissons</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guests as $guest)
                            <tr data-status="{{ $guest->has_drink_choices ? 'completed' : 'pending' }}" 
                                data-name="{{ strtolower($guest->first_name . ' ' . $guest->last_name) }}"
                                data-drinks="{{ $guest->drinkChoices->pluck('event_drink_id')->implode(',') }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $guest->first_name }} {{ $guest->last_name }}</h6>
                                            <small class="text-muted">{{ $guest->guestType->name ?? 'Invité' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($guest->email)
                                            <div class="mb-1">
                                                <i class="fas fa-envelope me-1"></i>
                                                <a href="mailto:{{ $guest->email }}" class="text-decoration-none">{{ $guest->email }}</a>
                                            </div>
                                        @endif
                                        @if($guest->phone)
                                            <div>
                                                <i class="fas fa-phone me-1"></i>
                                                <a href="tel:{{ $guest->phone }}" class="text-decoration-none">{{ $guest->phone }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($guest->drinkChoices->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($guest->drinkChoices as $choice)
                                                <span class="badge bg-primary" title="Prix: {{ $choice->eventDrink->price ?? 'N/A' }}€">
                                                    {{ $choice->eventDrink->drink->name }}
                                                    @if($choice->eventDrink->price)
                                                        <small>({{ $choice->eventDrink->price }}€)</small>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Total: {{ $guest->drinkChoices->count() }} boisson(s)
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-times-circle me-1"></i>Aucun choix
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($guest->has_drink_choices)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Complété
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>En attente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-danger btn-sm" onclick="deleteGuestDrinkChoices({{ $guest->id }})" 
                                                title="Supprimer les choix de boissons">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="viewGuestDetails({{ $guest->id }})" 
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editDrinkChoices({{ $guest->id }})" 
                                                title="Modifier les choix">
                                            <i class="fas fa-edit"></i>
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


<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    margin-right: 2px;
}
</style>

<script>
// Données des invités pour JavaScript
const guests = @json($guests);
const event = @json($event);
const invitations = @json($invitations ?? []);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const drinkFilter = document.getElementById('drinkFilter').value;
    
    const rows = document.querySelectorAll('#guestsTable tbody tr');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const name = row.getAttribute('data-name');
        const drinks = row.getAttribute('data-drinks');
        
        let showRow = true;
        
        // Filtre par statut
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        // Filtre par recherche
        if (searchInput && !name.includes(searchInput)) {
            showRow = false;
        }
        
        // Filtre par boisson
        if (drinkFilter && !drinks.includes(drinkFilter)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('drinkFilter').value = '';
    filterTable();
}

function deleteGuestDrinkChoices(guestId) {
    const guest = guests.find(g => g.id === guestId);
    if (!guest) {
        alert('Invité non trouvé.');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer tous les choix de boissons de ${guest.first_name} ${guest.last_name} ?`)) {
        // Envoyer une requête AJAX pour supprimer les choix
        fetch(`/guests/${guestId}/drink-choices`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Choix de boissons supprimés avec succès !');
                location.reload(); // Recharger la page pour mettre à jour l'affichage
            } else {
                alert('Erreur lors de la suppression : ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression des choix de boissons.');
        });
    }
}

function viewGuestDetails(guestId) {
    // Implémenter la vue des détails de l'invité
    console.log('Voir détails invité:', guestId);
}

function editDrinkChoices(guestId) {
    // Implémenter l'édition des choix de boissons
    console.log('Modifier choix boissons:', guestId);
}

function downloadPDF() {
    window.open(`/events/{{ $event->id }}/drink-choices/download-pdf`, '_blank');
}

function exportToExcel() {
    window.open(`/events/{{ $event->id }}/drink-choices/export-excel`, '_blank');
}

// Mettre à jour l'aperçu du message quand le message personnalisé change
document.getElementById('customMessage').addEventListener('input', function() {
    const phone = document.getElementById('recipientPhone').value;
    if (phone) {
        const guest = guests.find(g => g.phone === phone);
        if (guest) {
            generateMessagePreview(guest);
        }
    }
});
</script>
@endsection
