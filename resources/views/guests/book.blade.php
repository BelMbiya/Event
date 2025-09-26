@extends('admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Laisser un message au couple</h1>

        <form action="{{ route('book.store') }}" method="POST">
            @csrf
            <div class="row">

                <!-- Événement (event_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="event_id">Événement</label>
                    <select name="event_id" id="event_id" class="form-control select2" required>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Invité (guest_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="guest_id">Invité</label>
                    <select name="guest_id" id="guest_id" class="form-control select2">
                        @foreach ($guests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->first_name }} - {{ $guest->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Message -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="message">Message</label>
                    <textarea type="text" name="message" id="message" class="form-control summernote"
                        value="" cols="30" rows="10"></textarea>
                </div>

                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css" rel="stylesheet">

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialiser Summernote sur le champ message
            $('.summernote').summernote({
                height: 200,
                lang: 'fr-FR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Écrivez votre message ici...',
                callbacks: {
                    onInit: function() {
                        console.log('Summernote initialisé sur le champ message');
                    }
                }
            });
        });
    </script>
@endsection
