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
            <p class="text-muted">Messages des invités pour cet événement</p>
        </div>
        <div>
            <a href="{{ route('book.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Ajouter un message
            </a>
            <button class="btn btn-success" onclick="downloadPDF()">
                <i class="fas fa-download me-2"></i>
                Télécharger PDF
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
                                Total Messages
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guest_books->count() }}
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
                                Messages Publics
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guest_books->where('visibility', 'public')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                Messages Privés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guest_books->where('visibility', 'private')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock fa-2x text-gray-300"></i>
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
                                Auteurs Uniques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $guest_books->unique('guest_id')->count() }}
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
    @if($guest_books->count() > 0)
        <div class="row">
            @foreach($guest_books as $book)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <!-- Header de la carte -->
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-dark">{{ $book->first_name }} {{ $book->last_name }}</h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($book->created_at)->locale('fr')->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Corps de la carte -->
                        <div class="card-body">
                            <p class="card-text text-dark">{{ Str::limit($book->message, 150) }}</p>
                            
                            @if(strlen($book->message) > 150)
                                <button class="btn btn-sm btn-outline-primary" onclick="showFullMessage({{ $book->id }})">
                                    Lire la suite
                                </button>
                            @endif
                        </div>

                        <!-- Footer de la carte -->
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($book->created_at)->locale('fr')->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Message vide -->
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-book fa-4x text-gray-300 mb-4"></i>
                <h3 class="text-gray-600">Aucun message dans le livre d'or</h3>
                <p class="text-gray-500 mb-4">
                    Les invités n'ont pas encore laissé de messages pour cet événement.
                </p>
                <a href="{{ route('book.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Ajouter le premier message
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Modal pour afficher le message complet -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message complet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="messageContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.empty-state {
    padding: 3rem 1rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

<script>
function showFullMessage(bookId) {
    // Récupérer le message complet via AJAX
    fetch(`/guest-book/${bookId}/full-message`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('messageContent').innerHTML = `<p>${data.message}</p>`;
            new bootstrap.Modal(document.getElementById('messageModal')).show();
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}


function downloadPDF() {
    // Implémenter le téléchargement PDF
    window.open(`/guest-book/{{ $event_id }}/download-pdf`, '_blank');
}
</script>
@endsection