@extends('admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec titre et boutons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-book me-2"></i>
                Livre d'Or - {{ $event->title }}
            </h1>
            <p class="text-muted">Messages des invités pour l'événement</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="downloadPDF()">
                <i class="fas fa-download me-2"></i>
                Télécharger PDF
            </button>
            <a href="{{ route('book.create') }}?event_id={{ $event->id }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Ajouter un message
            </a>
        </div>
    </div>

    <!-- Informations de l'événement -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Événement
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $event->title }}
                            </div>
                            <div class="text-xs text-gray-600">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                            </div>
                            <div class="text-xs text-gray-600">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $event->location }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
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
                                Total Messages
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guestBooks->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
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
                                Messages Approuvés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guestBooks->where('status', 'approved')->count() }}
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
                                {{ $guestBooks->where('status', 'pending')->count() }}
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
                                Invités Uniques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guestBooks->pluck('guest_id')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages du livre d'or -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-comments me-2"></i>
                        Messages du Livre d'Or
                    </h6>
                </div>
                <div class="card-body">
                    @if($guestBooks->count() > 0)
                        <div class="row">
                            @foreach($guestBooks as $book)
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-{{ $book->status == 'approved' ? 'success' : ($book->status == 'pending' ? 'warning' : 'danger') }} shadow h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="font-weight-bold text-gray-800 mb-1">
                                                    {{ $book->guest->first_name ?? 'Anonyme' }} {{ $book->guest->last_name ?? '' }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($book->created_at)->locale('fr')->translatedFormat('d/m/Y à H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                @if($book->status == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Approuvé
                                                    </span>
                                                @elseif($book->status == 'pending')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>En attente
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Rejeté
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <p class="text-gray-800 mb-3">
                                            {{ Str::limit($book->message, 150) }}
                                        </p>
                                        
                                        @if($book->message && strlen($book->message) > 150)
                                            <button class="btn btn-sm btn-outline-primary" onclick="showFullMessage({{ $book->id }})">
                                                <i class="fas fa-eye me-1"></i>Voir plus
                                            </button>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="btn-group" role="group">
                                                @if($book->status == 'pending')
                                                    <button class="btn btn-success btn-sm" onclick="approveMessage({{ $book->id }})" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="rejectMessage({{ $book->id }})" title="Rejeter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-info btn-sm" onclick="viewMessage({{ $book->id }})" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="editMessage({{ $book->id }})" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Aucun message dans le livre d'or</h5>
                            <p class="text-gray-500">Les invités n'ont pas encore laissé de messages.</p>
                            <a href="{{ route('book.create') }}?event_id={{ $event->id }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter le premier message
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher le message complet -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message complet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Le contenu du message sera chargé ici -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    window.open('{{ route("book.download-pdf", $event->id) }}', '_blank');
}

function showFullMessage(bookId) {
    // Récupérer le message complet via AJAX
    fetch(`/guest_book/${bookId}/message`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('messageContent').innerHTML = `<p>${data.message}</p>`;
            $('#messageModal').modal('show');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement du message');
        });
}

function approveMessage(bookId) {
    if (confirm('Êtes-vous sûr de vouloir approuver ce message ?')) {
        updateMessageStatus(bookId, 'approved');
    }
}

function rejectMessage(bookId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter ce message ?')) {
        updateMessageStatus(bookId, 'rejected');
    }
}

function updateMessageStatus(bookId, status) {
    fetch(`/guest_book/${bookId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour du statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour du statut');
    });
}

function viewMessage(bookId) {
    // Rediriger vers la page de détail du message
    window.location.href = `/guest_book/${bookId}`;
}

function editMessage(bookId) {
    // Rediriger vers la page d'édition du message
    window.location.href = `/guest_book/${bookId}/edit`;
}
</script>
@endsection
