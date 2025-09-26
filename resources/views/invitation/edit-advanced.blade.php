{{--
========================================
INVITATION EDIT ADVANCED VIEW - ÉDITION D'INVITATION AVANCÉE
========================================

Cette vue permet de modifier des invitations existantes avec :
- Formulaire multi-onglets (basique, lieu, contenu, fonctionnalités, thème)
- Upload d'images (héros, galerie, OG image)
- Personnalisation des thèmes et couleurs
- Gestion des fonctionnalités (RSVP, boissons, livre d'or)
- Validation en temps réel et sauvegarde

UTILISATION : Modification complète d'invitations avec toutes les options
--}}
@extends('admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">✏️ Modifier l'Invitation - {{ $event->title }}</h2>

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

    <form action="{{ route('invitation.update', $invitation->id) }}" method="POST" enctype="multipart/form-data" id="invitationForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        
        <!-- Champs cachés pour les informations importantes de l'événement -->
        <input type="hidden" name="couple" value="{{ $event->title }}">
        <input type="hidden" name="event_datetime" value="{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i') : '' }}">
        <input type="hidden" name="timezone" value="Africa/Kinshasa">
        <input type="hidden" name="guest_id" value="">
        <input type="hidden" name="status" value="sent">
        <input type="hidden" name="content_status" value="published">
        <input type="hidden" name="venue_name" value="{{ $event->location }}">
        <input type="hidden" name="venue_address_line1" value="{{ $event->address ?? '' }}">
        <input type="hidden" name="venue_city" value="{{ $event->city ?? '' }}">
        <input type="hidden" name="venue_country" value="{{ $event->country ?? 'RDC' }}">
        <input type="hidden" name="locale" value="fr">
        <input type="hidden" name="slug" value="{{ Str::slug($event->title) }}">
        <input type="hidden" name="unique_code" value="{{ $invitation->unique_code }}">
        <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">

        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-4" id="invitationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invitation-content-tab" data-bs-toggle="tab" data-bs-target="#invitation-content-panel" type="button" role="tab" aria-controls="invitation-content-panel" aria-selected="true">
                    <i class="fas fa-edit me-2"></i>Contenu
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-design-tab" data-bs-toggle="tab" data-bs-target="#invitation-design-panel" type="button" role="tab" aria-controls="invitation-design-panel" aria-selected="false">
                    <i class="fas fa-palette me-2"></i>Design & Thème
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-features-tab" data-bs-toggle="tab" data-bs-target="#invitation-features-panel" type="button" role="tab" aria-controls="invitation-features-panel" aria-selected="false">
                    <i class="fas fa-cogs me-2"></i>Fonctionnalités
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-media-tab" data-bs-toggle="tab" data-bs-target="#invitation-media-panel" type="button" role="tab" aria-controls="invitation-media-panel" aria-selected="false">
                    <i class="fas fa-images me-2"></i>Images de Sections
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invitation-review-tab" data-bs-toggle="tab" data-bs-target="#invitation-review-panel" type="button" role="tab" aria-controls="invitation-review-panel" aria-selected="false">
                    <i class="fas fa-check-circle me-2"></i>Modification
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="invitationTabsContent">
            <!-- Onglet 1 : Contenu -->
            <div class="tab-pane fade show active" id="invitation-content-panel" role="tabpanel" aria-labelledby="invitation-content-tab">
                <h5><i class="fas fa-edit me-2"></i>Contenu de l'Invitation</h5>
                <p class="text-muted">Personnalisez le contenu de votre invitation</p>

                <!-- Introduction -->
                <div class="mb-4">
                    <label class="form-label">Introduction <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote" name="intro_1" rows="3" placeholder="Message d'introduction...">{{ old('intro_1', $content->intro_1 ?? '') }}</textarea>
                </div>

                <!-- Message principal -->
                <div class="mb-4">
                    <label class="form-label">Message Principal <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote" name="intro_2" rows="4" placeholder="Message principal de l'invitation...">{{ old('intro_2', $content->intro_2 ?? '') }}</textarea>
                </div>

                <!-- Programme de l'événement avec Summernote -->
                <div class="mb-4">
                    <label class="form-label">Programme de l'événement</label>
                    <textarea class="form-control summernote" name="program_html" id="program_html" rows="10" placeholder="Décrivez le programme de votre événement...">{{ old('program_html', $content->program_html ?? '') }}</textarea>
                </div>
            </div>

            <!-- Onglet 2 : Design & Thème -->
            <div class="tab-pane fade" id="invitation-design-panel" role="tabpanel" aria-labelledby="invitation-design-tab">
                <h5><i class="fas fa-palette me-2"></i>Design & Thème</h5>
                <p class="text-muted">Personnalisez l'apparence de votre invitation</p>

                <!-- Thème -->
                <div class="mb-4">
                    <label class="form-label">Thème de l'invitation</label>
                    <select class="form-select" name="theme">
                        <option value="elegant" {{ old('theme', $content->theme ?? 'elegant') == 'elegant' ? 'selected' : '' }}>Élégant</option>
                        <option value="modern" {{ old('theme', $content->theme ?? 'elegant') == 'modern' ? 'selected' : '' }}>Moderne</option>
                        <option value="classic" {{ old('theme', $content->theme ?? 'elegant') == 'classic' ? 'selected' : '' }}>Classique</option>
                        <option value="romantic" {{ old('theme', $content->theme ?? 'elegant') == 'romantic' ? 'selected' : '' }}>Romantique</option>
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="mb-4">
                    <label class="form-label">Boutons d'action</label>
                    <select class="form-select" name="cta">
                        <option value="rsvp" {{ old('cta', $content->cta ?? 'rsvp') == 'rsvp' ? 'selected' : '' }}>RSVP</option>
                        <option value="confirmation" {{ old('cta', $content->cta ?? 'rsvp') == 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                        <option value="participation" {{ old('cta', $content->cta ?? 'rsvp') == 'participation' ? 'selected' : '' }}>Participation</option>
                    </select>
                </div>

                <!-- Sélection de couleurs -->
                <h6 class="mt-4 mb-3">🎨 Couleurs</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur principale</label>
                        <input type="color" class="form-control form-control-color" name="primary_color" value="{{ old('primary_color', $content->primary_color ?? '#e11d48') }}">
                        <small class="form-text text-muted">Couleur dominante de l'invitation</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur secondaire</label>
                        <input type="color" class="form-control form-control-color" name="secondary_color" value="{{ old('secondary_color', $content->secondary_color ?? '#f43f5e') }}">
                        <small class="form-text text-muted">Couleur d'accent</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur d'accent</label>
                        <input type="color" class="form-control form-control-color" name="accent_color" value="{{ old('accent_color', $content->accent_color ?? '#fb7185') }}">
                        <small class="form-text text-muted">Couleur pour les détails</small>
                    </div>
                </div>

            </div>

            <!-- Onglet 3 : Fonctionnalités -->
            <div class="tab-pane fade" id="invitation-features-panel" role="tabpanel" aria-labelledby="invitation-features-tab">
                <h5><i class="fas fa-cogs me-2"></i>Fonctionnalités</h5>
                <p class="text-muted">Activez ou désactivez les fonctionnalités de votre invitation</p>

                <!-- Livre d'or -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guestbook_enabled" value="1" id="guestbook_enabled" {{ old('guestbook_enabled', $content->guestbook_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="guestbook_enabled">
                            <strong>Livre d'or</strong>
                        </label>
                    </div>
                    <small class="text-muted">Permet aux invités de laisser des messages</small>
                </div>

                <!-- Choix de boissons -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="drinks_enabled" value="1" id="drinks_enabled" {{ old('drinks_enabled', $content->drinks_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="drinks_enabled">
                            <strong>Choix de boissons</strong>
                        </label>
                    </div>
                    <small class="text-muted">Permet aux invités de choisir leurs boissons</small>
                </div>
            </div>

            <!-- Onglet 4 : Images de Sections -->
            <div class="tab-pane fade" id="invitation-media-panel" role="tabpanel" aria-labelledby="invitation-media-tab">
                <h5><i class="fas fa-images me-2"></i>Images de Sections</h5>
                <p class="text-muted">Ajoutez des images de fond pour chaque section de votre invitation</p>

                <!-- Images de sections - Layout 2 par ligne -->
                <div class="row">
                    <!-- Image Hero (En-tête) -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">🖼️ Image de fond - En-tête (Hero)</label>
                        <div class="drag-drop-zone" data-target="hero_image_path">
                            <input type="file" class="form-control d-none" name="hero_image_path" accept="image/*" id="hero_image_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Glissez-déposez votre image ici ou <span class="text-primary click-to-upload">cliquez pour sélectionner</span></p>
                                <small class="text-muted">Image qui apparaît en arrière-plan de l'en-tête</small>
                                @if($content && $content->hero_image_path)
                                    <div class="mt-2">
                                        <small class="text-success">Image actuelle : {{ basename($content->hero_image_path) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="image-preview" id="hero_image_preview" style="{{ $content && $content->hero_image_path ? 'display: block;' : 'display: none;' }}">
                                <img id="hero_image_preview_img" src="{{ $content && $content->hero_image_path ? asset('storage/' . $content->hero_image_path) : '' }}" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="hero_image_path">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Programme -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">📅 Image de fond - Programme</label>
                        <div class="drag-drop-zone" data-target="program_background_image">
                            <input type="file" class="form-control d-none" name="program_background_image" accept="image/*" id="program_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Glissez-déposez votre image ici ou <span class="text-primary click-to-upload">cliquez pour sélectionner</span></p>
                                <small class="text-muted">Image de fond pour la section programme</small>
                                @if($content && $content->program_background_image)
                                    <div class="mt-2">
                                        <small class="text-success">Image actuelle : {{ basename($content->program_background_image) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="image-preview" id="program_background_preview" style="{{ $content && $content->program_background_image ? 'display: block;' : 'display: none;' }}">
                                <img id="program_background_preview_img" src="{{ $content && $content->program_background_image ? asset('storage/' . $content->program_background_image) : '' }}" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="program_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Image Livre d'or -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">📖 Image de fond - Livre d'or</label>
                        <div class="drag-drop-zone" data-target="guestbook_background_image">
                            <input type="file" class="form-control d-none" name="guestbook_background_image" accept="image/*" id="guestbook_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Glissez-déposez votre image ici ou <span class="text-primary click-to-upload">cliquez pour sélectionner</span></p>
                                <small class="text-muted">Image de fond pour la section livre d'or</small>
                                @if($content && $content->guestbook_background_image)
                                    <div class="mt-2">
                                        <small class="text-success">Image actuelle : {{ basename($content->guestbook_background_image) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="image-preview" id="guestbook_background_preview" style="{{ $content && $content->guestbook_background_image ? 'display: block;' : 'display: none;' }}">
                                <img id="guestbook_background_preview_img" src="{{ $content && $content->guestbook_background_image ? asset('storage/' . $content->guestbook_background_image) : '' }}" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="guestbook_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Choix de boissons -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">🍷 Image de fond - Choix de boissons</label>
                        <div class="drag-drop-zone" data-target="drinks_background_image">
                            <input type="file" class="form-control d-none" name="drinks_background_image" accept="image/*" id="drinks_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Glissez-déposez votre image ici ou <span class="text-primary click-to-upload">cliquez pour sélectionner</span></p>
                                <small class="text-muted">Image de fond pour la section choix de boissons</small>
                                @if($content && $content->drinks_background_image)
                                    <div class="mt-2">
                                        <small class="text-success">Image actuelle : {{ basename($content->drinks_background_image) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="image-preview" id="drinks_background_preview" style="{{ $content && $content->drinks_background_image ? 'display: block;' : 'display: none;' }}">
                                <img id="drinks_background_preview_img" src="{{ $content && $content->drinks_background_image ? asset('storage/' . $content->drinks_background_image) : '' }}" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="drinks_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Image Pied de page -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">🦶 Image de fond - Pied de page</label>
                        <div class="drag-drop-zone" data-target="footer_background_image">
                            <input type="file" class="form-control d-none" name="footer_background_image" accept="image/*" id="footer_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Glissez-déposez votre image ici ou <span class="text-primary click-to-upload">cliquez pour sélectionner</span></p>
                                <small class="text-muted">Image de fond pour le pied de page</small>
                                @if($content && $content->footer_background_image)
                                    <div class="mt-2">
                                        <small class="text-success">Image actuelle : {{ basename($content->footer_background_image) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="image-preview" id="footer_background_preview" style="{{ $content && $content->footer_background_image ? 'display: block;' : 'display: none;' }}">
                                <img id="footer_background_preview_img" src="{{ $content && $content->footer_background_image ? asset('storage/' . $content->footer_background_image) : '' }}" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="footer_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne vide pour équilibrer la dernière ligne -->
                    <div class="col-md-6 mb-4">
                        <!-- Espace vide pour équilibrer la mise en page -->
                    </div>
                </div>
            </div>

            <!-- Onglet 5 : Modification -->
            <div class="tab-pane fade" id="invitation-review-panel" role="tabpanel" aria-labelledby="invitation-review-tab">
                <h5><i class="fas fa-check-circle me-2"></i>Modification de l'invitation</h5>
                <p class="text-muted">Modifiez votre invitation avec les paramètres configurés</p>

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

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Mettre à jour l'invitation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<style>
/* Styles pour les zones de drag & drop */
.drag-drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
    position: relative;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-drop-zone:hover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.drag-drop-zone.drag-over {
    border-color: #28a745;
    background-color: #d4edda;
    transform: scale(1.02);
}

.drag-drop-content {
    width: 100%;
}

.drag-drop-content i {
    color: #6c757d;
    transition: color 0.3s ease;
}

.drag-drop-zone:hover .drag-drop-content i {
    color: #007bff;
}

.drag-drop-zone.drag-over .drag-drop-content i {
    color: #28a745;
}

.click-to-upload {
    cursor: pointer;
    text-decoration: underline;
    font-weight: 500;
    transition: all 0.3s ease;
}

.click-to-upload:hover {
    color: #0056b3 !important;
    text-decoration: none;
}

.image-preview {
    position: relative;
    width: 100%;
    text-align: center;
}

.preview-image {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    z-index: 10;
}

.gallery-thumbnail {
    position: relative;
    display: inline-block;
    margin: 5px;
}

.gallery-thumbnail img {
    border-radius: 4px;
    transition: transform 0.2s ease;
}

.gallery-thumbnail:hover img {
    transform: scale(1.05);
}

.remove-gallery-image {
    position: absolute;
    top: -5px;
    right: -5px;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    z-index: 10;
}

/* Animation pour le drag over */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.drag-drop-zone.drag-over {
    animation: pulse 0.6s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
    .drag-drop-zone {
        padding: 1rem;
        min-height: 120px;
    }
    
    .preview-image {
        max-height: 150px;
    }
    
    .gallery-thumbnail {
        height: 120px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser Summernote
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Initialiser le drag & drop
    initializeDragDrop();
    
    // Gérer l'affichage des images existantes
    handleExistingImages();
});

function handleExistingImages() {
    console.log('🖼️ Gestion des images existantes...');
    
    // Vérifier chaque zone de drag & drop pour les images existantes
    document.querySelectorAll('.drag-drop-zone').forEach(zone => {
        const target = zone.dataset.target;
        const preview = document.getElementById(`${target}_preview`);
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && preview.style.display === 'block') {
            // Masquer le contenu de drag & drop si une image existe
            if (content) {
                content.style.display = 'none';
            }
            console.log(`Image existante affichée pour: ${target}`);
        }
    });
}

function initializeDragDrop() {
    console.log('🖱️ Initialisation du drag & drop...');
    
    document.querySelectorAll('.drag-drop-zone').forEach((zone, index) => {
        const input = zone.querySelector('input[type="file"]');
        const target = zone.dataset.target;
        const isMultiple = zone.dataset.multiple === 'true';
        
        console.log(`Zone ${index + 1}: ${target} (multiple: ${isMultiple})`);
        
        // Gestion du clic pour ouvrir le sélecteur de fichiers
        zone.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log(`Clic sur zone: ${target}`);
            
            // Vérifier si on clique sur le bouton de suppression
            if (e.target.closest('.remove-image')) {
                console.log('Clic sur bouton de suppression, ignoré');
                return;
            }
            
            // Ouvrir le sélecteur de fichiers
            console.log(`Ouverture du sélecteur de fichiers pour: ${target}`);
            input.click();
        });
        
        // Gestion du drag & drop - dragover
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
            console.log(`Drag over: ${target}`);
        });
        
        // Gestion du drag & drop - dragenter
        zone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
            console.log(`Drag enter: ${target}`);
        });
        
        // Gestion du drag & drop - dragleave
        zone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            // Vérifier si on quitte vraiment la zone
            if (!zone.contains(e.relatedTarget)) {
                zone.classList.remove('drag-over');
                console.log(`Drag leave: ${target}`);
            }
        });
        
        // Gestion du drag & drop - drop
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('drag-over');
            
            console.log(`Drop sur: ${target}`);
            const files = e.dataTransfer.files;
            console.log(`Fichiers reçus: ${files.length}`);
            
            if (files.length > 0) {
                // Vérifier que ce sont des images
                const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
                console.log(`Images valides: ${imageFiles.length}`);
                
                if (imageFiles.length > 0) {
                    if (isMultiple) {
                        // Créer un DataTransfer pour les fichiers multiples
                        const dt = new DataTransfer();
                        imageFiles.forEach(file => dt.items.add(file));
                        input.files = dt.files;
                    } else {
                        // Prendre seulement le premier fichier
                        const dt = new DataTransfer();
                        dt.items.add(imageFiles[0]);
                        input.files = dt.files;
                    }
                    
                    console.log(`Fichiers assignés à l'input: ${input.files.length}`);
                    handleFileSelect(input, target);
                } else {
                    alert('Veuillez glisser uniquement des fichiers images.');
                }
            }
        });
        
        // Gestion de la sélection de fichier classique
        input.addEventListener('change', (e) => {
            console.log(`Sélection classique: ${target}, fichiers: ${e.target.files.length}`);
            handleFileSelect(input, target);
        });
    });
    
    // Gestion des boutons de suppression
    document.querySelectorAll('.remove-image').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const target = btn.dataset.target;
            console.log(`Suppression: ${target}`);
            removeImage(target);
        });
    });
    
    console.log('✅ Drag & drop initialisé');
}

// Gérer la sélection de fichier
function handleFileSelect(input, target) {
    const files = input.files;
    handleSingleImagePreview(files[0], target);
}

// Prévisualiser une image unique
function handleSingleImagePreview(file, target) {
    console.log(`Prévisualisation: ${target}, fichier: ${file ? file.name : 'null'}`);
    
    if (!file) {
        console.log('❌ Aucun fichier à prévisualiser');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        console.log(`Image chargée pour ${target}`);
        
        const zone = document.querySelector(`[data-target="${target}"]`);
        if (!zone) {
            console.error(`❌ Zone non trouvée: ${target}`);
            return;
        }
        
        const preview = zone.querySelector('.image-preview');
        const previewImg = preview.querySelector('img');
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && previewImg && content) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            content.style.display = 'none';
            console.log(`✅ Prévisualisation affichée pour ${target}`);
        } else {
            console.error(`❌ Éléments de prévisualisation non trouvés pour ${target}`);
        }
    };
    
    reader.onerror = function() {
        console.error(`❌ Erreur lors du chargement de l'image pour ${target}`);
    };
    
    reader.readAsDataURL(file);
}

// Prévisualiser plusieurs images (galerie)
function handleGalleryPreview(files) {
    const preview = document.getElementById('gallery_preview');
    const previewImages = document.getElementById('gallery_preview_images');
    
    previewImages.innerHTML = '';
    
    if (files.length > 0) {
        preview.style.display = 'block';
        
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" alt="Aperçu ${index + 1}" class="img-thumbnail gallery-thumbnail">
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

// Supprimer une image
function removeImage(target) {
    const zone = document.querySelector(`[data-target="${target}"]`);
    const input = zone.querySelector('input[type="file"]');
    const preview = zone.querySelector('.image-preview');
    const content = zone.querySelector('.drag-drop-content');
    
    input.value = '';
    preview.style.display = 'none';
    content.style.display = 'block';
}
</script>
@endsection
