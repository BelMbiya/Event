@extends('admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Ajouter un Message au Livre d'Or
            </h1>
            <p class="text-muted">Laissez un message pour l'événement</p>
        </div>
        <div>
            <a href="{{ route('guest_book.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>
                        Nouveau Message
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('guest_book.store') }}">
                        @csrf
                        
                        <!-- Sélection de l'événement -->
                        <div class="form-group mb-3">
                            <label for="event_id" class="form-label">Événement *</label>
                            <select class="form-control" id="event_id" name="event_id" required>
                                <option value="">Sélectionnez un événement</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ (request('event_id') == $event->id) ? 'selected' : '' }}>
                                        {{ $event->title }} - {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sélection de l'invité -->
                        <div class="form-group mb-3">
                            <label for="guest_id" class="form-label">Invité *</label>
                            <select class="form-control" id="guest_id" name="guest_id" required>
                                <option value="">Sélectionnez un invité</option>
                                @foreach($guests as $guest)
                                    <option value="{{ $guest->id }}">
                                        {{ $guest->first_name }} {{ $guest->last_name }} ({{ $guest->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Message -->
                        <div class="form-group mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Écrivez votre message ici..." required></textarea>
                        </div>

                        <!-- Visibilité -->
                        <div class="form-group mb-3">
                            <label class="form-label">Visibilité</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="visibility_public" value="public" checked>
                                <label class="form-check-label" for="visibility_public">
                                    <i class="fas fa-eye text-success me-1"></i>
                                    Public (visible par tous)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="visibility_private" value="private">
                                <label class="form-check-label" for="visibility_private">
                                    <i class="fas fa-lock text-warning me-1"></i>
                                    Privé (visible par les organisateurs seulement)
                                </label>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer le Message
                            </button>
                            <a href="{{ route('guest_book.index') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aide -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Conseils
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Écrivez un message personnel et sincère pour l'événement.
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-users me-2 text-info"></i>
                        Les messages publics seront visibles par tous les invités.
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-lock me-2 text-warning"></i>
                        Les messages privés ne seront visibles que par les organisateurs.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
