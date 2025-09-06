@extends('admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">🎨 Créer une Invitation Personnalisée - {{ $event->title }}</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    <!-- Note sur les champs obligatoires -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires pour la validation et l'enregistrement.
    </div>

    <form action="{{ route('invitation.store') }}" method="POST" enctype="multipart/form-data" id="invitationForm">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">

        <!-- Onglets Bootstrap avec IDs uniques -->
        <ul class="nav nav-pills mb-4" id="invitationCreationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invitation-basic-tab" data-bs-toggle="pill" data-bs-target="#invitation-basic-content" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>Informations de base
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-content-tab" data-bs-toggle="pill" data-bs-target="#invitation-content-panel" type="button" role="tab">
                    <i class="fas fa-edit me-2"></i>Contenu & Textes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-design-tab" data-bs-toggle="pill" data-bs-target="#invitation-design-panel" type="button" role="tab">
                    <i class="fas fa-palette me-2"></i>Design & Thème
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-features-tab" data-bs-toggle="pill" data-bs-target="#invitation-features-panel" type="button" role="tab">
                    <i class="fas fa-cogs me-2"></i>Fonctionnalités
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-media-tab" data-bs-toggle="pill" data-bs-target="#invitation-media-panel" type="button" role="tab">
                    <i class="fas fa-images me-2"></i>Médias & Images
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-review-tab" data-bs-toggle="pill" data-bs-target="#invitation-review-panel" type="button" role="tab">
                    <i class="fas fa-check-circle me-2"></i>Révision & Enregistrement
                </button>
            </li>
        </ul>

        <!-- Contenus des onglets -->
        <div class="tab-content p-4 border rounded bg-white shadow-sm" id="invitationTabsContent">

            <!-- Onglet 1 : Informations de base -->
            <div class="tab-pane fade show active" id="invitation-basic-content" role="tabpanel" aria-labelledby="invitation-basic-tab">
                <h5><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
                <p class="text-muted">Configurez les informations essentielles de votre invitation</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du couple <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="couple" value="{{ old('couple', $event->title) }}" required>
                        <small class="form-text text-muted">Ex: Marie & Jean, Sophie & Pierre</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date et heure de l'événement <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="event_datetime" 
                               value="{{ old('event_datetime', $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fuseau horaire</label>
                        <select class="form-select" name="timezone">
                            <option value="Africa/Kinshasa" {{ old('timezone') == 'Africa/Kinshasa' ? 'selected' : '' }}>Afrique/Kinshasa</option>
                            <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Amérique/New York</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Invité concerné</label>
                        <select class="form-select" name="guest_id">
                            <option value="">Invitation générale</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                                    {{ $guest->first_name }} {{ $guest->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut de l'invitation <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : 'selected' }}>En attente</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                            <option value="opened" {{ old('status') == 'opened' ? 'selected' : '' }}>Ouverte</option>
                            <option value="responded" {{ old('status') == 'responded' ? 'selected' : '' }}>Répondue</option>
                            <option value="called" {{ old('status') == 'called' ? 'selected' : '' }}>Appelée</option>
                        </select>
                        <small class="form-text text-muted">Statut initial de l'invitation</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut du contenu <span class="text-danger">*</span></label>
                        <select class="form-select" name="content_status" required>
                            <option value="draft" {{ old('content_status', 'draft') == 'draft' ? 'selected' : 'selected' }}>Brouillon</option>
                            <option value="published" {{ old('content_status') == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="archived" {{ old('content_status') == 'archived' ? 'selected' : '' }}>Archivé</option>
                        </select>
                        <small class="form-text text-muted">Statut de publication du contenu</small>
                    </div>
                </div>

                <h6 class="mt-4">Lieu de l'événement</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du lieu</label>
                        <input type="text" class="form-control" name="venue_name" value="{{ old('venue_name', $event->location) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adresse</label>
                        <input type="text" class="form-control" name="venue_address_line1" value="{{ old('venue_address_line1', $event->address) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" class="form-control" name="venue_city" value="{{ old('venue_city', $event->city) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Région</label>
                        <input type="text" class="form-control" name="venue_region" value="{{ old('venue_region', $event->region) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pays</label>
                        <input type="text" class="form-control" name="venue_country" value="{{ old('venue_country', $event->country ?? 'République Démocratique du Congo') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Google Maps</label>
                    <input type="url" class="form-control" name="google_maps_url" value="{{ old('google_maps_url', $event->google_maps_url) }}" placeholder="https://maps.google.com/...">
                    <small class="form-text text-muted">Généré automatiquement à partir de l'adresse de l'événement</small>
                </div>
            </div>

            <!-- Onglet 2 : Contenu & Textes -->
            <div class="tab-pane fade" id="invitation-content-panel" role="tabpanel" aria-labelledby="invitation-content-tab">
                <h5><i class="fas fa-edit me-2"></i>Contenu & Textes</h5>
                <p class="text-muted">Personnalisez les textes de votre invitation</p>

                <div class="mb-3">
                    <label class="form-label">Message d'introduction (Hero)</label>
                    <input type="text" class="form-control" name="intro_1" value="{{ old('intro_1', '💍 Le grand jour arrive 💍') }}" placeholder="💍 Le grand jour arrive 💍">
                    <small class="form-text text-muted">Texte qui apparaît en haut de l'invitation</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sous-titre de l'invitation</label>
                    <input type="text" class="form-control" name="intro_2" value="{{ old('intro_2', '💌 Invitation 💌') }}" placeholder="💌 Invitation 💌">
                    <small class="form-text text-muted">Texte qui apparaît avant le contenu principal</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contenu principal de l'invitation</label>
                    <textarea class="form-control summernote" name="body_html" rows="6" placeholder="C'est avec une immense joie...">{{ old('body_html') }}</textarea>
                    <small class="form-text text-muted">Le message principal de votre invitation</small>
                </div>

                <h6 class="mt-4">📅 Programme de l'événement</h6>
                <p class="text-muted">Définissez le déroulement de votre événement (jusqu'à 5 créneaux)</p>
                
                @for($i = 1; $i <= 5; $i++)
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Créneau {{ $i }} - Heure</label>
                        <input type="time" class="form-control" name="schedule_time_{{ $i }}" 
                               value="{{ old("schedule_time_$i", $i == 1 ? '14:00' : ($i == 2 ? '16:00' : ($i == 3 ? '18:00' : ($i == 4 ? '20:00' : '22:00')))) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Créneau {{ $i }} - Événement</label>
                        <input type="text" class="form-control" name="schedule_event_{{ $i }}" 
                               value="{{ old("schedule_event_$i", $i == 1 ? 'Cérémonie religieuse' : ($i == 2 ? 'Cocktail de bienvenue' : ($i == 3 ? 'Réception et dîner' : ($i == 4 ? 'Ouverture du bal' : 'Soirée dansante')))) }}" 
                               placeholder="Description de l'événement">
                    </div>
                </div>
                @endfor
            </div>

            <!-- Onglet 3 : Design & Thème -->
            <div class="tab-pane fade" id="invitation-design-panel" role="tabpanel" aria-labelledby="invitation-design-tab">
                <h5><i class="fas fa-palette me-2"></i>Design & Thème</h5>
                <p class="text-muted">Personnalisez les couleurs et le style de votre invitation</p>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur principale</label>
                        <input type="color" class="form-control form-control-color" name="theme_primary_color" value="{{ old('theme_primary_color', '#e11d48') }}">
                        <small class="form-text text-muted">Couleur dominante de l'invitation</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur secondaire</label>
                        <input type="color" class="form-control form-control-color" name="theme_secondary_color" value="{{ old('theme_secondary_color', '#f43f5e') }}">
                        <small class="form-text text-muted">Couleur d'accent</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur d'accent</label>
                        <input type="color" class="form-control form-control-color" name="theme_accent_color" value="{{ old('theme_accent_color', '#fb7185') }}">
                        <small class="form-text text-muted">Couleur pour les détails</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police des titres</label>
                        <select class="form-select" name="theme_heading_font">
                            <option value="Alex Brush" {{ old('theme_heading_font') == 'Alex Brush' ? 'selected' : '' }}>Alex Brush (Élégant)</option>
                            <option value="Dancing Script" {{ old('theme_heading_font') == 'Dancing Script' ? 'selected' : '' }}>Dancing Script (Romantique)</option>
                            <option value="Great Vibes" {{ old('theme_heading_font') == 'Great Vibes' ? 'selected' : '' }}>Great Vibes (Classique)</option>
                            <option value="Montserrat" {{ old('theme_heading_font') == 'Montserrat' ? 'selected' : '' }}>Montserrat (Moderne)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police du texte</label>
                        <select class="form-select" name="theme_body_font">
                            <option value="Cormorant Garamond" {{ old('theme_body_font') == 'Cormorant Garamond' ? 'selected' : '' }}>Cormorant Garamond (Élégant)</option>
                            <option value="Playfair Display" {{ old('theme_body_font') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Classique)</option>
                            <option value="Crimson Text" {{ old('theme_body_font') == 'Crimson Text' ? 'selected' : '' }}>Crimson Text (Traditionnel)</option>
                            <option value="Open Sans" {{ old('theme_body_font') == 'Open Sans' ? 'selected' : '' }}>Open Sans (Moderne)</option>
                        </select>
                    </div>
                </div>

                <h6 class="mt-4">Décorations</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_floral" value="1" {{ old('theme_floral') ? 'checked' : '' }}>
                            <label class="form-check-label">Éléments floraux</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_overlay" value="1" {{ old('theme_overlay') ? 'checked' : '' }}>
                            <label class="form-check-label">Effet de superposition</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_parallax" value="1" {{ old('theme_parallax') ? 'checked' : '' }}>
                            <label class="form-check-label">Effet parallaxe</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 4 : Fonctionnalités -->
            <div class="tab-pane fade" id="invitation-features-panel" role="tabpanel" aria-labelledby="invitation-features-tab">
                <h5><i class="fas fa-cogs me-2"></i>Fonctionnalités</h5>
                <p class="text-muted">Activez ou désactivez les fonctionnalités de votre invitation</p>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Livre d'or</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="guestbook_enabled" value="1" {{ old('guestbook_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer le livre d'or</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre du livre d'or</label>
                            <input type="text" class="form-control" name="guestbook_title" value="{{ old('guestbook_title', 'Livre d\'or') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sous-titre du livre d'or</label>
                            <input type="text" class="form-control" name="guestbook_subtitle" value="{{ old('guestbook_subtitle', 'Laissez-nous un mot...') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6>Choix de boissons</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="drinks_enabled" value="1" {{ old('drinks_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer les choix de boissons</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre de la section boissons</label>
                            <input type="text" class="form-control" name="drinks_title" value="{{ old('drinks_title', 'Choisissez vos boissons') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Texte du bouton de validation</label>
                            <input type="text" class="form-control" name="drinks_submit_label" value="{{ old('drinks_submit_label', 'Valider mes choix') }}">
                        </div>
                    </div>
                </div>

                <h6 class="mt-4">Boutons d'action</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_rsvp_enabled" value="1" {{ old('cta_rsvp_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton RSVP</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_rsvp_label" value="{{ old('cta_rsvp_label', 'Confirmer ma présence 💌') }}" placeholder="Texte du bouton">
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_map_enabled" value="1" {{ old('cta_map_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton Carte</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_map_label" value="{{ old('cta_map_label', 'Voir sur la carte 📍') }}" placeholder="Texte du bouton">
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_download_enabled" value="1" {{ old('cta_download_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton Téléchargement</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_download_label" value="{{ old('cta_download_label', 'Télécharger l\'invitation (PDF)') }}" placeholder="Texte du bouton">
                    </div>
                </div>
            </div>

            <!-- Onglet 5 : Médias & Images -->
            <div class="tab-pane fade" id="invitation-media-panel" role="tabpanel" aria-labelledby="invitation-media-tab">
                <h5><i class="fas fa-images me-2"></i>Médias & Images</h5>
                <p class="text-muted">Ajoutez des images à votre invitation</p>

                <div class="mb-3">
                    <label class="form-label">Image principale (Hero)</label>
                    <input type="file" class="form-control" name="hero_image_path" accept="image/*" id="hero_image_input" onchange="previewImage(this, 'hero_image_preview')">
                    <small class="form-text text-muted">Image qui apparaît en arrière-plan de l'en-tête</small>
                    <div id="hero_image_preview" class="mt-2" style="display: none;">
                        <img id="hero_image_preview_img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Texte alternatif de l'image</label>
                    <input type="text" class="form-control" name="hero_image_alt" value="{{ old('hero_image_alt') }}" placeholder="Description de l'image">
                </div>

                <div class="mb-3">
                    <label class="form-label">Galerie d'images</label>
                    <input type="file" class="form-control" name="gallery[]" multiple accept="image/*" id="gallery_input" onchange="previewMultipleImages(this, 'gallery_preview')">
                    <small class="form-text text-muted">Sélectionnez plusieurs images pour la galerie</small>
                    <div id="gallery_preview" class="mt-2" style="display: none;">
                        <div class="row" id="gallery_preview_images"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image pour le partage social (OpenGraph)</label>
                    <input type="file" class="form-control" name="og_image" accept="image/*" id="og_image_input" onchange="previewImage(this, 'og_image_preview')">
                    <small class="form-text text-muted">Image qui apparaît lors du partage sur les réseaux sociaux</small>
                    <div id="og_image_preview" class="mt-2" style="display: none;">
                        <img id="og_image_preview_img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
            </div>

            <!-- Onglet 6 : Révision & Enregistrement -->
            <div class="tab-pane fade" id="invitation-review-panel" role="tabpanel" aria-labelledby="invitation-review-tab">
                <h5><i class="fas fa-check-circle me-2"></i>Révision & Enregistrement</h5>
                <p class="text-muted">Vérifiez vos informations avant d'enregistrer</p>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Résumé de votre invitation</h6>
                    <ul class="mb-0">
                        <li><strong>Événement :</strong> <span id="review-event">{{ $event->title }}</span></li>
                        <li><strong>Couple :</strong> <span id="review-couple">-</span></li>
                        <li><strong>Date :</strong> <span id="review-date">-</span></li>
                        <li><strong>Lieu :</strong> <span id="review-venue">-</span></li>
                        <li><strong>Fonctionnalités activées :</strong> <span id="review-features">-</span></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <label class="form-label">Statut de publication <span class="text-danger">*</span></label>
                    <select class="form-select" name="content_status" required>
                        <option value="draft" selected>Brouillon</option>
                        <option value="published">Publié</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta Title (SEO)</label>
                    <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}" placeholder="Titre pour les moteurs de recherche">
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta Description (SEO)</label>
                    <textarea class="form-control" name="meta_description" rows="3" placeholder="Description pour les moteurs de recherche">{{ old('meta_description') }}</textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Créer l'invitation
                    </button>
                    <button type="button" class="btn btn-warning btn-lg" onclick="createInvitationsForAllGuests({{ $event->id }}, '{{ $event->title }}')">
                        <i class="fas fa-magic me-2"></i>Créer automatiquement pour tous les invités
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>

<script>
$(document).ready(function() {
    // Initialiser Summernote
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

    // Mise à jour du résumé en temps réel
    function updateReview() {
        $('#review-couple').text($('input[name="couple"]').val() || '-');
        $('#review-date').text($('input[name="event_datetime"]').val() || '-');
        $('#review-venue').text($('input[name="venue_name"]').val() || '-');
        
        let features = [];
        if ($('input[name="guestbook_enabled"]').is(':checked')) features.push('Livre d\'or');
        if ($('input[name="drinks_enabled"]').is(':checked')) features.push('Choix de boissons');
        if ($('input[name="cta_rsvp_enabled"]').is(':checked')) features.push('RSVP');
        if ($('input[name="cta_map_enabled"]').is(':checked')) features.push('Carte');
        if ($('input[name="cta_download_enabled"]').is(':checked')) features.push('Téléchargement');
        
        $('#review-features').text(features.join(', ') || 'Aucune');
    }

    // Écouter les changements
    $('input, select, textarea').on('change keyup', updateReview);
    
    // Mise à jour initiale
    updateReview();
    
    // Initialiser Summernote
    $('.summernote').summernote({
        height: 200,
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
        placeholder: 'Rédigez votre message d\'invitation...',
        lang: 'fr-FR'
    });
});

// Fonction pour prévisualiser une image unique
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            const previewImg = document.getElementById(previewId + '_img');
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Fonction pour prévisualiser plusieurs images
function previewMultipleImages(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImages = document.getElementById(previewId + '_images');
    
    // Vider le conteneur précédent
    previewImages.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" alt="Aperçu ${index + 1}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                        <small class="text-muted d-block text-center mt-1">Image ${index + 1}</small>
                    </div>
                `;
                previewImages.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

<!-- Modal de retour pour la création d'invitations -->
<div class="modal fade" id="creationResultModal" tabindex="-1" role="dialog" aria-labelledby="creationResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creationResultModalLabel">
                    <i class="fas fa-check-circle text-success"></i> Résultat de la Création
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="creationResultContent">
                    <!-- Le contenu sera rempli dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <a href="{{ route('invitation.index') }}" class="btn btn-primary" id="viewInvitationsBtn">
                    <i class="fas fa-list"></i> Voir les Invitations
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion du formulaire avec AJAX
document.getElementById('invitationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Désactiver le bouton et afficher le loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
    
    // Envoyer la requête AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Succès
            showCreationResult(data, 'success');
        } else {
            // Erreur
            showCreationResult(data, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showCreationResult({
            message: 'Une erreur inattendue s\'est produite. Veuillez réessayer.'
        }, 'error');
    })
    .finally(() => {
        // Réactiver le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function showCreationResult(data, type) {
    const modal = document.getElementById('creationResultModal');
    const content = document.getElementById('creationResultContent');
    const title = document.querySelector('#creationResultModalLabel');
    
    if (type === 'success') {
        title.innerHTML = '<i class="fas fa-check-circle text-success"></i> Création Réussie !';
        title.className = 'modal-title text-success';
        
        let html = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle"></i> ${data.count} invitation(s) créée(s) avec succès !</h6>
            </div>
        `;
        
        if (data.duplicates && data.duplicates.length > 0) {
            html += `
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Invitations déjà existantes</h6>
                    <p>Les invités suivants avaient déjà des invitations et ont été ignorés :</p>
                    <ul>
                        ${data.duplicates.map(guest => `<li>${guest}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        html += `
            <div class="text-center mt-3">
                <i class="fas fa-party-horn fa-3x text-success mb-3"></i>
                <p class="lead">Vos invitations sont prêtes !</p>
                <p>Vous pouvez maintenant les personnaliser et les envoyer à vos invités.</p>
            </div>
        `;
        
        content.innerHTML = html;
    } else {
        title.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Erreur de Création';
        title.className = 'modal-title text-danger';
        
        let html = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle"></i> Erreur lors de la création</h6>
                <p>${data.message}</p>
            </div>
        `;
        
        if (data.errors) {
            html += `
                <div class="alert alert-warning">
                    <h6>Erreurs de validation :</h6>
                    <ul>
                        ${Object.values(data.errors).flat().map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        content.innerHTML = html;
    }
    
    // Afficher le modal
    $(modal).modal('show');
}

// Fonction pour créer automatiquement des invitations pour tous les invités
function createInvitationsForAllGuests(eventId, eventTitle) {
    if (!confirm(`🔍 Vérifier et créer les invitations manquantes pour l'événement "${eventTitle}" ?\n\n✅ Créera seulement pour les invités qui n'ont pas encore d'invitation\nℹ️ Ignorera ceux qui en ont déjà une`)) {
        return;
    }
    
    // Afficher le modal de chargement
    showAutoCreationModal(eventTitle);
    
    // Envoyer la requête AJAX
    fetch(`/events/${eventId}/create-invitations-for-all-guests`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAutoCreationResult(data, 'success');
        } else {
            showAutoCreationResult(data, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAutoCreationResult({
            message: 'Une erreur inattendue s\'est produite. Veuillez réessayer.'
        }, 'error');
    });
}

// Fonction pour afficher le modal de chargement pour la création automatique
function showAutoCreationModal(eventTitle) {
    const modal = document.getElementById('autoCreationModal');
    const content = document.getElementById('autoCreationContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <h5>Création automatique des invitations en cours...</h5>
            <p class="text-muted">Vérification et création des invitations pour l'événement : <strong>${eventTitle}</strong></p>
        </div>
    `;
    
    $('#autoCreationModal').modal('show');
}

// Fonction pour afficher le résultat de la création automatique
function showAutoCreationResult(data, type) {
    const modal = document.getElementById('autoCreationModal');
    const content = document.getElementById('autoCreationContent');
    const title = document.querySelector('#autoCreationModalLabel');
    
    if (type === 'success') {
        title.innerHTML = '<i class="fas fa-check-circle text-success"></i> Création Automatique Réussie !';
        title.className = 'modal-title text-success';
        
        let resultHtml = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle me-2"></i>Opération terminée avec succès !</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
        
        if (data.count > 0) {
            resultHtml += `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Détails de la création :</h6>
                    <ul class="mb-0">
                        <li><strong>${data.count}</strong> nouvelle(s) invitation(s) créée(s)</li>
                        <li><strong>${data.existing_count}</strong> invitation(s) existante(s) (ignorée(s))</li>
                    </ul>
                </div>
            `;
        }
        
        content.innerHTML = resultHtml;
    } else {
        title.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Erreur';
        title.className = 'modal-title text-danger';
        
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la création automatique</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
    }
}
</script>

<!-- Modal pour la création automatique d'invitations -->
<div class="modal fade" id="autoCreationModal" tabindex="-1" role="dialog" aria-labelledby="autoCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autoCreationModalLabel">
                    <i class="fas fa-magic text-primary"></i> Création Automatique des Invitations
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="autoCreationContent">
                    <!-- Le contenu sera rempli dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <a href="{{ route('invitation.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Voir les Invitations
                </a>
            </div>
        </div>
    </div>
</div>