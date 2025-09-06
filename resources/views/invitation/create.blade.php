@extends('admin')

@section('content')
    <div class="container my-4">
        <h2 class="mb-4">🎉 Créer une nouvelle invitation</h2>

        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@php
$defaultCta = [
    "rsvp" => ["enabled" => true, "label" => "Confirmer ma présence 💌"],
    "map" => ["enabled" => true, "label" => "Voir sur la carte 📍"],
    "download" => ["enabled" => true, "label" => "Télécharger l'invitation (PDF)"]
];

// Si l'ancien input existe, on l'utilise pour cocher les cases
$oldCta = old('cta') ? json_decode(old('cta'), true) : $defaultCta;
@endphp


        <form action="{{ route('invitation.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Onglets Bootstrap (PILLS) -->
            <ul class="nav nav-pills mb-3" id="invitationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab">
                        Infos générales
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">
                        Contenus
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button"
                        role="tab">
                        Médias & Design
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="guests-tab" data-bs-toggle="tab" data-bs-target="#guests" type="button"
                        role="tab">
                        Options invités
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button"
                        role="tab">
                        SEO & Métadonnées
                    </button>
                </li>
            </ul>

            <!-- Contenus des onglets -->
            <div class="tab-content p-4 border rounded bg-white shadow-sm" id="invitationTabsContent">

                <!-- Onglet 1 : Infos générales -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <h5>Infos générales</h5>

                    <div class="mb-3">
                        <label class="form-label">Événement</label>
                        <input type="text" class="form-control" value="{{ $event->title }}" readonly>
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date et heure</label>
                        <!-- Date et heure -->
<input type="datetime-local" class="form-control" name="event_datetime"
    value="{{ old('event_datetime', $event->event_date?->format('Y-m-d\TH:i')) }}">
                    </div>

                    <div class="mb-3">
    <label class="form-label">Fuseau horaire</label>
    <select class="form-select" name="timezone">
        @php
            $timezones = DateTimeZone::listIdentifiers();
            $selectedTimezone = old('timezone', 'Africa/Kinshasa');
        @endphp

        @foreach($timezones as $tz)
            @php
                $tzObj = new DateTimeZone($tz);
                $offset = $tzObj->getOffset(new DateTime('now'));
                $hours = intdiv($offset, 3600);
                $minutes = abs(($offset % 3600) / 60);
                $sign = $hours >= 0 ? '+' : '-';
                $formattedOffset = sprintf('GMT%s%02d:%02d', $sign, abs($hours), $minutes);
            @endphp
            <option value="{{ $tz }}" {{ $tz === $selectedTimezone ? 'selected' : '' }}>
                {{ $tz }} ({{ $formattedOffset }})
            </option>
        @endforeach
    </select>
</div>


                    <div class="mb-3">
                        <label class="form-label">Nom du lieu</label>
                        <input type="text" class="form-control" name="venue_name"
    value="{{ old('venue_name', $event->location ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse principal</label>
                        <input type="text" class="form-control" name="venue_address_line1"
                            value="{{ old('venue_address_line1') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse complementeraire</label>
                        <input type="text" class="form-control" name="venue_address_line2"
                            value="{{ old('venue_address_line2') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" class="form-control" name="venue_city" value="{{ old('venue_city') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Région / État</label>
                        <input type="text" class="form-control" name="venue_region" value="{{ old('venue_region') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pays</label>
                        <input type="text" class="form-control" name="venue_country"
                            value="{{ old('venue_country') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" class="form-control" name="google_maps_url"
                            value="{{ old('google_maps_url') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" name="venue_lat" value="{{ old('venue_lat') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control" name="venue_lng" value="{{ old('venue_lng') }}">
                        </div>
                    </div>
                </div>

                <!-- Onglet 2 : Contenus -->
                <div class="tab-pane fade" id="tab-content" role="tabpanel" aria-labelledby="content-tab">
                    <h5>Contenus</h5>

                    <div class="mb-3">
                        <label class="form-label">Nom du couple</label>
                        <input type="text" class="form-control" name="couple" value="{{ old('couple') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nom de l'epoux</label>
                        <input type="text" class="form-control" name="epoux" value="{{ old('epoux') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nom de l'epouse</label>
                        <input type="text" class="form-control" name="epouse" value="{{ old('epouse') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contenu de l'invitation</label>
                        <textarea class="form-control summernote" name="body_html" rows="6">{{ old('body_html') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Texte suplementaire</label>
                        <textarea class="form-control summernote" name="intro_1" rows="3">{{ old('intro_1') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Introduction 2</label>
                        <textarea class="form-control summernote" name="intro_2" rows="3">{{ old('intro_2') }}</textarea>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Programme / Schedule</label>
                        <textarea class="form-control" name="schedule" rows="4">{{ old('schedule') }}</textarea>
                    </div>

                    <div class="mb-3">
    <label class="form-label">Call to Action</label>
    <div class="form-check">
        @foreach($defaultCta as $key => $cta)
            <input class="form-check-input" type="checkbox" 
                   name="cta[{{ $key }}][enabled]" 
                   value="1"
                   id="cta_{{ $key }}"
                   {{ isset($oldCta[$key]['enabled']) && $oldCta[$key]['enabled'] ? 'checked' : '' }}>
            <label class="form-check-label" for="cta_{{ $key }}">
                {{ $cta['label'] }}
            </label><br>
        @endforeach
    </div>
</div>
                </div>

                <!-- Onglet 3 : Médias & Design -->
                <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                    <h5>Médias & Design</h5>

                    <div class="mb-3">
                        <label class="form-label">Image principale (hero)</label>
                        <input type="file" class="form-control" name="hero_image_path">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Texte alternatif</label>
                        <input type="text" class="form-control" name="hero_image_alt"
                            value="{{ old('hero_image_alt') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Galerie</label>
                        <input type="file" class="form-control" name="gallery[]" multiple>
                    </div>

                    <div class="mb-3">
    <label class="form-label">Couleurs du thème</label>
    <div class="d-flex gap-2">
        <div>
            <label>Primaire</label>
            <input type="color" class="form-control form-control-color" id="color_primary" value="#e11d48">
        </div>
        <div>
            <label>Secondaire</label>
            <input type="color" class="form-control form-control-color" id="color_secondary" value="#f43f5e">
        </div>
        <div>
            <label>Accent</label>
            <input type="color" class="form-control form-control-color" id="color_accent" value="#fb7185">
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Polices</label>
    <div class="d-flex gap-2">
        <div>
            <label>Titres</label>
            <input type="text" class="form-control" id="font_headings" value="Alex Brush">
        </div>
        <div>
            <label>Corps</label>
            <input type="text" class="form-control" id="font_body" value="Cormorant Garamond">
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Décorations</label>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="decor_floral" checked>
        <label class="form-check-label">Floral</label>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="decor_overlay" checked>
        <label class="form-check-label">Overlay</label>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="decor_parallax">
        <label class="form-check-label">Parallax</label>
    </div>
</div>

<!-- Hidden input qui contiendra le JSON final -->
<input type="hidden" name="theme" id="theme_json">


                    <div class="mb-3">
                        <label class="form-label">Police du texte</label>
                        <select class="form-select" name="font_family">
                            <option value="Arial">Arial</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Roboto">Roboto</option>
                        </select>
                    </div>
                </div>

                <!-- Onglet 4 : Options invités -->
                <div class="tab-pane fade" id="guests" role="tabpanel" aria-labelledby="guests-tab">
                    <h5>Options invités</h5>

                    <div class="mb-3">
                        <label class="form-label">Livre d’invités activé ?</label>
                        <select class="form-select" name="guestbook_enabled">
                            <option value="0">Non</option>
                            <option value="1">Oui</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titre livre d’invités</label>
                        <input type="text" class="form-control" name="guestbook_title"
                            value="{{ old('guestbook_title') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sous-titre livre d’invités</label>
                        <input type="text" class="form-control" name="guestbook_subtitle"
                            value="{{ old('guestbook_subtitle') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Boissons activées ?</label>
                        <select class="form-select" name="drinks_enabled">
                            <option value="0">Non</option>
                            <option value="1">Oui</option>
                        </select>
                    </div>

                    <div class="mb-3">
    <label for="drinks" class="form-label">Choisir les boissons</label>
    <select name="drinks[]" id="drinks" class="form-control" multiple>
        @foreach($drinks as $eventDrink)
            <option value="{{ $eventDrink->id }}"
                {{ in_array($eventDrink->id, old('drinks', $selectedDrinkIds)) ? 'selected' : '' }}>
                {{ $eventDrink->drink->name }} - {{ $eventDrink->price }}€
            </option>
        @endforeach
    </select>
</div>


                </div>

                <!-- Onglet 5 : SEO & Métadonnées -->
                <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                    <h5>SEO & Métadonnées</h5>

                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image OpenGraph</label>
                        <input type="file" class="form-control" name="og_image">
                    </div>

                    <select name="status" class="form-control">
    <option value="pending" {{ old('status', $invitation->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
    <option value="sent" {{ old('status', $invitation->status ?? 'pending') == 'sent' ? 'selected' : '' }}>Sent</option>
    <option value="opened" {{ old('status', $invitation->status ?? 'pending') == 'opened' ? 'selected' : '' }}>Opened</option>
    <option value="responded" {{ old('status', $invitation->status ?? 'pending') == 'responded' ? 'selected' : '' }}>Responded</option>
    <option value="called" {{ old('status', $invitation->status ?? 'pending') == 'called' ? 'selected' : '' }}>Called</option>
</select>


                    <div class="mb-3">
                        <label class="form-label">Date de publication</label>
                        <input type="datetime-local" class="form-control" name="published_at"
                            value="{{ old('published_at') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Langue / Locale</label>
                        <input type="text" class="form-control" name="locale" value="{{ old('locale', 'fr') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}">
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
    <button type="submit" id="saveButton" class="btn btn-primary" style="display: none;">💾 Enregistrer</button>
</div>
        </form>
    </div>

    <!-- Summernote CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });

        $(document).ready(function() {
        // Fonction pour vérifier l'onglet actif
        function toggleSaveButton() {
            const activeTab = $('#invitationTabs .nav-link.active').attr('id');
            if(activeTab === 'seo-tab') {
                $('#saveButton').fadeIn();
            } else {
                $('#saveButton').fadeOut();
            }
        }

        // Initial check au chargement
        toggleSaveButton();

        // Listener sur le changement de tab
        $('#invitationTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            toggleSaveButton();
        });
    });

    function updateThemeJson() {
    const theme = {
        colors: {
            primary: document.getElementById('color_primary').value,
            secondary: document.getElementById('color_secondary').value,
            accent: document.getElementById('color_accent').value
        },
        fonts: {
            headings: document.getElementById('font_headings').value,
            body: document.getElementById('font_body').value
        },
        decorations: {
            floral: document.getElementById('decor_floral').checked,
            overlay: document.getElementById('decor_overlay').checked,
            parallax: document.getElementById('decor_parallax').checked
        }
    };
    document.getElementById('theme_json').value = JSON.stringify(theme);
}

// Mettre à jour le JSON à chaque changement
document.querySelectorAll('#color_primary, #color_secondary, #color_accent, #font_headings, #font_body, #decor_floral, #decor_overlay, #decor_parallax')
    .forEach(el => el.addEventListener('input', updateThemeJson));

// Initialiser à la première ouverture du formulaire
updateThemeJson();
    </script>
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
