@extends('admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Créer une nouvelle invitation</h1>

        <form action="{{ route('invitation.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Titre de l'invitation -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="title">Titre de l'invitation</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <!-- Événement (event_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="event_id">Événement</label>
                    <select name="event_id" id="event_id" class="form-control select2" required>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Invité (guest_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="guest_id">Invité</label>
                    <select name="guest_id" id="guest_id" class="form-control select2" required>
                        @foreach ($guests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Code unique (unique_code) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="unique_code">Code unique</label>
                    <input type="text" name="unique_code" id="unique_code" class="form-control"
                        value="{{ Str::uuid() }}" readonly>
                </div>

                <!-- URL de l'invitation (invitation_url) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="invitation_url">URL de l'invitation</label>
                    <input type="text" name="invitation_url" id="invitation_url" class="form-control"
                        value="{{ url('/invitation/' . Str::random(10)) }}" readonly>
                </div>

                <!-- Statut -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="status">Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending">En attente</option>
                        <option value="sent">Envoyée</option>
                        <option value="opened">Ouverte</option>
                    </select>
                </div>

                <!-- Date d'envoi -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="sent_at">Date d'envoi</label>
                    <input type="datetime-local" name="sent_at" id="sent_at" class="form-control">
                </div>

                <!-- Date ouverture -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="opened_at">Date d'ouverture</label>
                    <input type="datetime-local" name="opened_at" id="opened_at" class="form-control">
                </div>

                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Créer l'invitation</button>
                </div>
            </div>
        </form>

        <!-- Scripts pour select2 -->
        @push('scripts')
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('.select2').select2({
                        tags: true, // Permet de saisir une nouvelle valeur
                        placeholder: "Sélectionnez ou tapez...",
                        allowClear: true
                    });
                });
            </script>
        @endpush

    </div>
@endsection
