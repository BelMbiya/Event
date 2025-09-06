@extends('admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">🎨 Personnaliser l'Invitation - {{ $invitation->unique_code }}</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
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

    <!-- Note sur les champs obligatoires -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires pour la validation et l'enregistrement.
        <br><small class="text-muted">
            <i class="fas fa-magic me-1"></i> 
            La sauvegarde se fait côté serveur PHP/Laravel. Cliquez sur "Sauvegarder les modifications" pour enregistrer vos changements.
        </small>
    </div>

    <form action="{{ route('invitation.content.update', $invitation->id) }}" method="POST" enctype="multipart/form-data" id="editContentForm">
        @csrf
        @method('PUT')

        <!-- Onglets Bootstrap avec IDs uniques -->
        <ul class="nav nav-pills mb-4" id="editContentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="edit-basic-tab" data-bs-toggle="pill" data-bs-target="#edit-basic-content" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>Informations de base
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-content-tab" data-bs-toggle="pill" data-bs-target="#edit-content-panel" type="button" role="tab">
                    <i class="fas fa-edit me-2"></i>Contenu & Textes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-design-tab" data-bs-toggle="pill" data-bs-target="#edit-design-panel" type="button" role="tab">
                    <i class="fas fa-palette me-2"></i>Design & Thème
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-features-tab" data-bs-toggle="pill" data-bs-target="#edit-features-panel" type="button" role="tab">
                    <i class="fas fa-cogs me-2"></i>Fonctionnalités
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-media-tab" data-bs-toggle="pill" data-bs-target="#edit-media-panel" type="button" role="tab">
                    <i class="fas fa-images me-2"></i>Médias & Images
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-review-tab" data-bs-toggle="pill" data-bs-target="#edit-review-panel" type="button" role="tab">
                    <i class="fas fa-check-circle me-2"></i>Révision & Sauvegarde
                </button>
            </li>
        </ul>

        <!-- Contenus des onglets -->
        <div class="tab-content p-4 border rounded bg-white shadow-sm" id="editContentTabsContent">

            <!-- Onglet 1 : Informations de base -->
            <div class="tab-pane fade show active" id="edit-basic-content" role="tabpanel" aria-labelledby="edit-basic-tab">
                <h5><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
                <p class="text-muted">Configurez les informations essentielles de votre invitation</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du couple <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="couple" value="{{ old('couple', $content->couple) }}" required>
                        <small class="form-text text-muted">Ex: Marie & Jean, Sophie & Pierre</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date et heure de l'événement <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="event_datetime" 
                               value="{{ old('event_datetime', $content->event_datetime ? \Carbon\Carbon::parse($content->event_datetime)->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fuseau horaire</label>
                        <select class="form-select" name="timezone">
                            <option value="Africa/Kinshasa" {{ old('timezone', $content->timezone) == 'Africa/Kinshasa' ? 'selected' : '' }}>Afrique/Kinshasa</option>
                            <option value="Europe/Paris" {{ old('timezone', $content->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="America/New_York" {{ old('timezone', $content->timezone) == 'America/New_York' ? 'selected' : '' }}>Amérique/New York</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut du contenu</label>
                        <select class="form-select" name="status">
                            <option value="draft" {{ old('status', $content->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status', $content->status) == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="archived" {{ old('status', $content->status) == 'archived' ? 'selected' : '' }}>Archivé</option>
                        </select>
                        <small class="form-text text-muted">Statut de publication du contenu</small>
                    </div>
                </div>

                <h6 class="mt-4">Lieu de l'événement</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du lieu</label>
                        <input type="text" class="form-control" name="venue_name" value="{{ old('venue_name', $content->venue_name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adresse</label>
                        <input type="text" class="form-control" name="venue_address_line1" value="{{ old('venue_address_line1', $content->venue_address_line1) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" class="form-control" name="venue_city" value="{{ old('venue_city', $content->venue_city) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Région</label>
                        <input type="text" class="form-control" name="venue_region" value="{{ old('venue_region', $content->venue_region) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pays</label>
                        <input type="text" class="form-control" name="venue_country" value="{{ old('venue_country', $content->venue_country) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Google Maps</label>
                    <input type="url" class="form-control" name="google_maps_url" value="{{ old('google_maps_url', $content->google_maps_url) }}" placeholder="https://maps.google.com/...">
                    <small class="form-text text-muted">Généré automatiquement à partir de l'adresse de l'événement</small>
                </div>
            </div>

            <!-- Onglet 2 : Contenu & Textes -->
            <div class="tab-pane fade" id="edit-content-panel" role="tabpanel" aria-labelledby="edit-content-tab">
                <h5><i class="fas fa-edit me-2"></i>Contenu & Textes</h5>
                <p class="text-muted">Personnalisez les textes de votre invitation</p>

                <div class="mb-3">
                    <label class="form-label">Message d'introduction (Hero)</label>
                    <textarea class="form-control" name="intro_1" rows="3" placeholder="C'est avec une immense joie...">{{ old('intro_1', $content->intro_1) }}</textarea>
                    <small class="form-text text-muted">Le message principal qui apparaît en haut de l'invitation</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message secondaire</label>
                    <textarea class="form-control" name="intro_2" rows="3" placeholder="Nous avons le plaisir de vous inviter...">{{ old('intro_2', $content->intro_2) }}</textarea>
                    <small class="form-text text-muted">Message complémentaire</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contenu principal de l'invitation</label>
                    <textarea class="form-control summernote" name="body_html" rows="6" placeholder="C'est avec une immense joie...">{{ old('body_html', $content->body_html) }}</textarea>
                    <small class="form-text text-muted">Le message principal de votre invitation</small>
                </div>

                <h6 class="mt-4">📅 Programme de l'événement</h6>
                <p class="text-muted">Définissez le déroulement de votre événement (jusqu'à 5 créneaux)</p>
                
                @php
                    $schedule = $content->schedule ? json_decode($content->schedule, true) : [];
                    // ✅ CORRECTION : S'assurer que $schedule est un array et extraire les données correctement
                    $scheduleItems = [];
                    if (is_array($schedule)) {
                        foreach ($schedule as $item) {
                            if (is_array($item) && isset($item['time']) && isset($item['event'])) {
                                $scheduleItems[] = $item;
                            }
                        }
                    }
                @endphp
                
                @for($i = 1; $i <= 5; $i++)
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Créneau {{ $i }} - Heure</label>
                        <input type="time" class="form-control" name="schedule_time_{{ $i }}" 
                               value="{{ old("schedule_time_$i", $scheduleItems[$i-1]['time'] ?? '') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Créneau {{ $i }} - Événement</label>
                        <input type="text" class="form-control" name="schedule_event_{{ $i }}" 
                               value="{{ old("schedule_event_$i", $scheduleItems[$i-1]['event'] ?? '') }}" 
                               placeholder="Description de l'événement">
                    </div>
                </div>
                @endfor
            </div>

            <!-- Onglet 3 : Design & Thème -->
            <div class="tab-pane fade" id="edit-design-panel" role="tabpanel" aria-labelledby="edit-design-tab">
                <h5><i class="fas fa-palette me-2"></i>Design & Thème</h5>
                <p class="text-muted">Personnalisez les couleurs et le style de votre invitation</p>

                @php
                    $theme = $content->theme ? json_decode($content->theme, true) : [];
                    $colors = $theme['colors'] ?? [];
                    $fonts = $theme['fonts'] ?? [];
                    $decorations = $theme['decorations'] ?? [];
                @endphp

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur principale</label>
                        <input type="color" class="form-control form-control-color" name="theme_primary_color" value="{{ old('theme_primary_color', $colors['primary'] ?? '#e11d48') }}">
                        <small class="form-text text-muted">Couleur dominante de l'invitation</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur secondaire</label>
                        <input type="color" class="form-control form-control-color" name="theme_secondary_color" value="{{ old('theme_secondary_color', $colors['secondary'] ?? '#f43f5e') }}">
                        <small class="form-text text-muted">Couleur d'accent</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur d'accent</label>
                        <input type="color" class="form-control form-control-color" name="theme_accent_color" value="{{ old('theme_accent_color', $colors['accent'] ?? '#fb7185') }}">
                        <small class="form-text text-muted">Couleur pour les détails</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police des titres</label>
                        <select class="form-select" name="theme_heading_font">
                            <option value="Alex Brush" {{ old('theme_heading_font', $fonts['headings'] ?? 'Alex Brush') == 'Alex Brush' ? 'selected' : '' }}>Alex Brush (Élégant)</option>
                            <option value="Dancing Script" {{ old('theme_heading_font', $fonts['headings'] ?? 'Alex Brush') == 'Dancing Script' ? 'selected' : '' }}>Dancing Script (Romantique)</option>
                            <option value="Great Vibes" {{ old('theme_heading_font', $fonts['headings'] ?? 'Alex Brush') == 'Great Vibes' ? 'selected' : '' }}>Great Vibes (Classique)</option>
                            <option value="Montserrat" {{ old('theme_heading_font', $fonts['headings'] ?? 'Alex Brush') == 'Montserrat' ? 'selected' : '' }}>Montserrat (Moderne)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police du texte</label>
                        <select class="form-select" name="theme_body_font">
                            <option value="Cormorant Garamond" {{ old('theme_body_font', $fonts['body'] ?? 'Cormorant Garamond') == 'Cormorant Garamond' ? 'selected' : '' }}>Cormorant Garamond (Élégant)</option>
                            <option value="Playfair Display" {{ old('theme_body_font', $fonts['body'] ?? 'Cormorant Garamond') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Classique)</option>
                            <option value="Crimson Text" {{ old('theme_body_font', $fonts['body'] ?? 'Cormorant Garamond') == 'Crimson Text' ? 'selected' : '' }}>Crimson Text (Traditionnel)</option>
                            <option value="Open Sans" {{ old('theme_body_font', $fonts['body'] ?? 'Cormorant Garamond') == 'Open Sans' ? 'selected' : '' }}>Open Sans (Moderne)</option>
                        </select>
                    </div>
                </div>

                <h6 class="mt-4">Décorations</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_floral" value="1" {{ old('theme_floral', $decorations['floral'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Éléments floraux</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_overlay" value="1" {{ old('theme_overlay', $decorations['overlay'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Effet overlay</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_parallax" value="1" {{ old('theme_parallax', $decorations['parallax'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Effet parallax</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 4 : Fonctionnalités -->
            <div class="tab-pane fade" id="edit-features-panel" role="tabpanel" aria-labelledby="edit-features-tab">
                <h5><i class="fas fa-cogs me-2"></i>Fonctionnalités</h5>
                <p class="text-muted">Activez ou désactivez les fonctionnalités de votre invitation</p>

                @php
                    $cta = $content->cta ? json_decode($content->cta, true) : [];
                    $drinks = $content->drinks ? json_decode($content->drinks, true) : [];
                @endphp

                <div class="row">
                    <div class="col-md-6">
                        <h6>Livre d'or</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="guestbook_enabled" value="1" {{ old('guestbook_enabled', $content->guestbook_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer le livre d'or</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre du livre d'or</label>
                            <input type="text" class="form-control" name="guestbook_title" value="{{ old('guestbook_title', $content->guestbook_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sous-titre du livre d'or</label>
                            <input type="text" class="form-control" name="guestbook_subtitle" value="{{ old('guestbook_subtitle', $content->guestbook_subtitle) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6>Choix de boissons</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="drinks_enabled" value="1" {{ old('drinks_enabled', $content->drinks_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer les choix de boissons</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre de la section boissons</label>
                            <input type="text" class="form-control" name="drinks_title" value="{{ old('drinks_title', $drinks['title'] ?? 'Choisissez vos boissons') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Texte du bouton de validation</label>
                            <input type="text" class="form-control" name="drinks_submit_label" value="{{ old('drinks_submit_label', $drinks['submit_label'] ?? 'Valider mes choix') }}">
                        </div>
                    </div>
                </div>

                <h6 class="mt-4">Boutons d'action</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_rsvp_enabled" value="1" {{ old('cta_rsvp_enabled', $cta['rsvp']['enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton RSVP</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_rsvp_label" value="{{ old('cta_rsvp_label', $cta['rsvp']['label'] ?? 'Confirmer ma présence 💌') }}" placeholder="Texte du bouton">
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_map_enabled" value="1" {{ old('cta_map_enabled', $cta['map']['enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton Carte</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_map_label" value="{{ old('cta_map_label', $cta['map']['label'] ?? 'Voir sur la carte 📍') }}" placeholder="Texte du bouton">
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cta_download_enabled" value="1" {{ old('cta_download_enabled', $cta['download']['enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Bouton Téléchargement</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="cta_download_label" value="{{ old('cta_download_label', $cta['download']['label'] ?? 'Télécharger l\'invitation (PDF)') }}" placeholder="Texte du bouton">
                    </div>
                </div>
            </div>

            <!-- Onglet 5 : Médias & Images -->
            <div class="tab-pane fade" id="edit-media-panel" role="tabpanel" aria-labelledby="edit-media-tab">
                <h5><i class="fas fa-images me-2"></i>Médias & Images</h5>
                <p class="text-muted">Ajoutez des images à votre invitation</p>

                <div class="mb-3">
                    <label class="form-label">Image principale (Hero)</label>
                    <input type="file" class="form-control" name="hero_image_path" accept="image/*">
                    <small class="form-text text-muted">Image qui apparaît en haut de l'invitation</small>
                    @if($content->hero_image_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $content->hero_image_path) }}" alt="Image actuelle" class="img-thumbnail" style="max-width: 200px;">
                            <small class="d-block text-muted">Image actuelle</small>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Galerie d'images</label>
                    <input type="file" class="form-control" name="gallery[]" accept="image/*" multiple>
                    <small class="form-text text-muted">Sélectionnez plusieurs images pour la galerie</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image pour les réseaux sociaux (OG)</label>
                    <input type="file" class="form-control" name="og_image" accept="image/*">
                    <small class="form-text text-muted">Image qui apparaît lors du partage sur les réseaux sociaux</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Texte alternatif de l'image principale</label>
                    <input type="text" class="form-control" name="hero_image_alt" value="{{ old('hero_image_alt', $content->hero_image_alt) }}" placeholder="Description de l'image">
                </div>
            </div>

            <!-- Onglet 6 : Révision & Sauvegarde -->
            <div class="tab-pane fade" id="edit-review-panel" role="tabpanel" aria-labelledby="edit-review-tab">
                <h5><i class="fas fa-check-circle me-2"></i>Révision & Sauvegarde</h5>
                <p class="text-muted">Vérifiez vos informations avant de sauvegarder</p>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Résumé de vos modifications</h6>
                    <ul class="mb-0">
                        <li><strong>Couple:</strong> {{ old('couple', $content->couple) }}</li>
                        <li><strong>Date:</strong> {{ old('event_datetime', $content->event_datetime) }}</li>
                        <li><strong>Lieu:</strong> {{ old('venue_name', $content->venue_name) }}</li>
                        <li><strong>Statut:</strong> {{ old('content_status', $content->status) }}</li>
                    </ul>
                </div>

                <!-- ✅ NOUVEAU : Option de synchronisation -->
                <div class="alert alert-warning mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sync_to_all_invitations" id="syncToAllInvitations" value="1">
                        <label class="form-check-label" for="syncToAllInvitations">
                            <i class="fas fa-sync-alt me-2"></i>
                            <strong>Synchroniser avec toutes les autres invitations de cet événement</strong>
                        </label>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Si coché, toutes les modifications apportées à cette invitation seront automatiquement appliquées à toutes les autres invitations du même événement ({{ $invitation->event->title }}).
                            <br><small class="text-muted">Cela inclut : contenu, design, lieu, horaires, mais pas les informations spécifiques à chaque invité.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('invitation.show', $invitation->unique_code) }}" class="btn btn-secondary">
                        <i class="fas fa-eye me-2"></i>Prévisualiser l'invitation
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="saveButton">
                        <i class="fas fa-save me-2"></i>Sauvegarder les modifications
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ NOUVEAU : Gestion de la checkbox de synchronisation
    const syncCheckbox = document.getElementById('syncToAllInvitations');
    const saveButton = document.getElementById('saveButton');
    
    if (syncCheckbox && saveButton) {
        syncCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Changer le texte du bouton pour indiquer la synchronisation
                saveButton.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Sauvegarder et synchroniser';
                saveButton.classList.remove('btn-success');
                saveButton.classList.add('btn-warning');
                
                // Afficher une alerte de confirmation
                if (!document.getElementById('syncWarning')) {
                    const warningDiv = document.createElement('div');
                    warningDiv.id = 'syncWarning';
                    warningDiv.className = 'alert alert-warning mt-2';
                    warningDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i><strong>Attention :</strong> Cette action va modifier toutes les invitations de cet événement. Êtes-vous sûr de vouloir continuer ?';
                    syncCheckbox.parentNode.appendChild(warningDiv);
                }
            } else {
                // Remettre le bouton normal
                saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Sauvegarder les modifications';
                saveButton.classList.remove('btn-warning');
                saveButton.classList.add('btn-success');
                
                // Supprimer l'alerte
                const warningDiv = document.getElementById('syncWarning');
                if (warningDiv) {
                    warningDiv.remove();
                }
            }
        });
    }

    // Initialiser les onglets Bootstrap
    var triggerTabList = [].slice.call(document.querySelectorAll('#editContentTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
    
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
</script>

<!-- Plus de modal AJAX - messages gérés côté serveur -->

<script>
// La soumission du formulaire se fait maintenant côté serveur PHP/Laravel normalement
// Plus d'AJAX - soumission classique du formulaire
</script>
@endsection