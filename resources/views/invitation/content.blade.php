@extends('admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Ajouter du contenu</h1>

        <form action="{{ route('content.store') }}" method="POST">
            @csrf
            <div class="row">

                <input type="hidden" name="invitation_id" value="{{ $invitation_id }}">

                <!-- Couple -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="couple">Couple</label>
                    <input type="text" name="couple" id="couple" class="form-control" required>
                </div>

                <!-- Epoux -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="epoux">Epoux</label>
                    <input type="text" name="epoux" id="epoux" class="form-control" required>
                </div>

                <!-- Epouse -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="epouse">Epouse</label>
                    <input type="text" name="epouse" id="epouse" class="form-control" required>
                </div>

                <!-- Famille Epoux -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="Famille_epoux">Famille époux</label>
                    <input type="text" name="Famille_epoux" id="Famille_epoux" class="form-control" required>
                </div>

                <!-- Famille Epouse -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="Famille_epouse">Famille épouse</label>
                    <input type="text" name="Famille_epouse" id="Famille_epouse" class="form-control" required>
                </div>

                <!-- ✅ Zone de texte avancée -->
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        Contenu de l'invitation
                    </label>
                    <textarea id="summernote" name="content" class="w-full border rounded p-2"></textarea>
                </div>


                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Ajouter le contenu</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <!-- Summernote CSS + JS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Écrivez votre message ici...',
                height: 200
            });
        });
    </script>
@endsection
