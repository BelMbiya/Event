{{--
========================================
INVITATION CREATE ADVANCED VIEW - CRÉATION D'INVITATION AVANCÉE
========================================

Cette vue permet de créer des invitations personnalisées avec :
- Formulaire multi-onglets (basique, lieu, contenu, fonctionnalités, thème)
- Upload d'images (héros, galerie, OG image)
- Personnalisation des thèmes et couleurs
- Gestion des fonctionnalités (RSVP, boissons, livre d'or)
- Validation en temps réel et sauvegarde

UTILISATION : Création complète d'invitations avec toutes les options
--}}
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
        <input type="hidden" name="venue_region" value="{{ $event->region ?? '' }}">
        <input type="hidden" name="venue_country" value="{{ $event->country ?? 'République Démocratique du Congo' }}">
        <input type="hidden" name="google_maps_url" value="{{ $event->google_maps_url ?? '' }}">

        <!-- Onglets Bootstrap avec IDs uniques -->
        <ul class="nav nav-pills mb-4" id="invitationCreationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invitation-content-tab" data-bs-toggle="pill" data-bs-target="#invitation-content-panel" type="button" role="tab">
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

            <!-- Onglet 1 : Contenu & Textes -->
            <div class="tab-pane fade show active" id="invitation-content-panel" role="tabpanel" aria-labelledby="invitation-content-tab">
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
                <p class="text-muted">Définissez le déroulement de votre événement avec un éditeur riche</p>
                
                <div class="mb-3">
                    <label class="form-label">Programme détaillé</label>
                    <textarea class="form-control summernote" name="program_html" rows="8" placeholder="Créez votre programme avec des heures, descriptions et formatage...">{{ old('program_html', '<div class="program-schedule">
    <div class="schedule-item">
        <div class="time">14:00</div>
        <div class="event">Cérémonie religieuse</div>
                    </div>
    <div class="schedule-item">
        <div class="time">16:00</div>
        <div class="event">Cocktail de bienvenue</div>
                    </div>
    <div class="schedule-item">
        <div class="time">18:00</div>
        <div class="event">Réception et dîner</div>
                </div>
    <div class="schedule-item">
        <div class="time">20:00</div>
        <div class="event">Ouverture du bal</div>
    </div>
    <div class="schedule-item">
        <div class="time">22:00</div>
        <div class="event">Soirée dansante</div>
    </div>
</div>') }}</textarea>
                    <small class="form-text text-muted">Utilisez l'éditeur pour créer un programme personnalisé avec formatage, couleurs et mise en page</small>
                </div>
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

                {{-- 
                ========================================
                SECTION POLICES ET DÉCORATIONS - COMMENTÉE
                ========================================
                
                Cette section est commentée car les valeurs par défaut sont définies
                automatiquement lors de la sauvegarde dans le contrôleur.
                
                Valeurs par défaut appliquées :
                - Police des titres : "Alex Brush" (Élégant)
                - Police du texte : "Cormorant Garamond" (Élégant)
                - Éléments floraux : Activés (true)
                - Effet de superposition : Activé (true)
                - Effet parallaxe : Désactivé (false)
                
                Ces valeurs sont définies dans le contrôleur InvitationController@store
                dans la variable $defaultTheme.
                --}}
                
                {{-- 
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police des titres</label>
                        <select class="form-select" name="theme_heading_font">
                            <option value="Alex Brush" {{ old('theme_heading_font', 'Alex Brush') == 'Alex Brush' ? 'selected' : '' }}>Alex Brush (Élégant)</option>
                            <option value="Dancing Script" {{ old('theme_heading_font', 'Alex Brush') == 'Dancing Script' ? 'selected' : '' }}>Dancing Script (Romantique)</option>
                            <option value="Great Vibes" {{ old('theme_heading_font', 'Alex Brush') == 'Great Vibes' ? 'selected' : '' }}>Great Vibes (Classique)</option>
                            <option value="Montserrat" {{ old('theme_heading_font', 'Alex Brush') == 'Montserrat' ? 'selected' : '' }}>Montserrat (Moderne)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Police du texte</label>
                        <select class="form-select" name="theme_body_font">
                            <option value="Cormorant Garamond" {{ old('theme_body_font', 'Cormorant Garamond') == 'Cormorant Garamond' ? 'selected' : '' }}>Cormorant Garamond (Élégant)</option>
                            <option value="Playfair Display" {{ old('theme_body_font', 'Cormorant Garamond') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Classique)</option>
                            <option value="Crimson Text" {{ old('theme_body_font', 'Cormorant Garamond') == 'Crimson Text' ? 'selected' : '' }}>Crimson Text (Traditionnel)</option>
                            <option value="Open Sans" {{ old('theme_body_font', 'Cormorant Garamond') == 'Open Sans' ? 'selected' : '' }}>Open Sans (Moderne)</option>
                        </select>
                    </div>
                </div>

                <h6 class="mt-4">Décorations</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_floral" value="1" {{ old('theme_floral', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Éléments floraux</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_overlay" value="1" {{ old('theme_overlay', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Effet de superposition</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="theme_parallax" value="1" {{ old('theme_parallax', false) ? 'checked' : '' }}>
                            <label class="form-check-label">Effet parallaxe</label>
                        </div>
                    </div>
                </div>
                --}}
            </div>

             {{-- 
             ========================================
             ONGLET FONCTIONNALITÉS - SIMPLIFIÉ
             ========================================
             
             Cette section est simplifiée car la plupart des valeurs par défaut sont définies
             automatiquement lors de la sauvegarde dans le contrôleur.
             
             Valeurs par défaut appliquées automatiquement :
             
             📖 LIVRE D'OR :
             - Activé par défaut (true)
             - Titre : "Livre d'or"
             - Sous-titre : "Laissez-nous un mot..."
             
             🍷 CHOIX DE BOISSONS :
             - Activé par défaut (true)
             - Titre : "Choisissez vos boissons"
             - Bouton : "Valider mes choix"
             
             🔘 BOUTONS D'ACTION :
             - RSVP : Activé, texte "Confirmer ma présence 💌"
             - Carte : Activé, texte "Voir sur la carte 📍"
             - Téléchargement : Activé, texte "Télécharger l'invitation (PDF)"
             
             Ces valeurs sont définies dans le contrôleur InvitationController@store
             dans les variables $defaultCta et $defaultDrinks.
             --}}

            <!-- Onglet 4 : Fonctionnalités -->
            <div class="tab-pane fade" id="invitation-features-panel" role="tabpanel" aria-labelledby="invitation-features-tab">
                <h5><i class="fas fa-cogs me-2"></i>Fonctionnalités</h5>
                 <p class="text-muted">Activez ou désactivez les fonctionnalités principales de votre invitation</p>

                <div class="row">
                    <div class="col-md-6">
                         <h6>📖 Livre d'or</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="guestbook_enabled" value="1" {{ old('guestbook_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer le livre d'or</label>
                        </div>
                         <small class="text-muted">
                             <i class="fas fa-info-circle me-1"></i>
                             Titre par défaut : "Livre d'or"<br>
                             Sous-titre par défaut : "Laissez-nous un mot..."
                         </small>
                    </div>

                    <div class="col-md-6">
                         <h6>🍷 Choix de boissons</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="drinks_enabled" value="1" {{ old('drinks_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer les choix de boissons</label>
                        </div>
                         <small class="text-muted">
                             <i class="fas fa-info-circle me-1"></i>
                             Titre par défaut : "Choisissez vos boissons"<br>
                             Bouton par défaut : "Valider mes choix"
                         </small>
                        </div>
                        </div>

                 <div class="alert alert-info mt-4">
                     <h6><i class="fas fa-magic me-2"></i>Boutons d'action automatiques</h6>
                     <p class="mb-0">
                         Les boutons suivants sont activés automatiquement avec des textes par défaut :
                     </p>
                     <ul class="mb-0 mt-2">
                         <li><strong>RSVP :</strong> "Confirmer ma présence 💌"</li>
                         <li><strong>Carte :</strong> "Voir sur la carte 📍"</li>
                         <li><strong>Téléchargement :</strong> "Télécharger l'invitation (PDF)"</li>
                     </ul>
                    </div>
                </div>

            <!-- Onglet 5 : Images de Sections -->
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
                            </div>
                            <div class="image-preview" id="hero_image_preview" style="display: none;">
                                <img id="hero_image_preview_img" src="" alt="Aperçu" class="preview-image">
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
                            </div>
                            <div class="image-preview" id="program_background_preview" style="display: none;">
                                <img id="program_background_preview_img" src="" alt="Aperçu" class="preview-image">
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
                            </div>
                            <div class="image-preview" id="guestbook_background_preview" style="display: none;">
                                <img id="guestbook_background_preview_img" src="" alt="Aperçu" class="preview-image">
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
                            </div>
                            <div class="image-preview" id="drinks_background_preview" style="display: none;">
                                <img id="drinks_background_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="drinks_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 
                <!-- Image RSVP - COMMENTÉ -->
                <div class="mb-4">
                    <label class="form-label">💌 Image de fond - RSVP</label>
                    <input type="file" class="form-control" name="rsvp_background_image" accept="image/*" id="rsvp_background_input" onchange="previewImage(this, 'rsvp_background_preview')">
                    <small class="form-text text-muted">Image de fond pour la section RSVP</small>
                    <div id="rsvp_background_preview" class="mt-2" style="display: none;">
                        <img id="rsvp_background_preview_img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                </div>
                </div>
                --}}

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
                            </div>
                            <div class="image-preview" id="footer_background_preview" style="display: none;">
                                <img id="footer_background_preview_img" src="" alt="Aperçu" class="preview-image">
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

                {{-- 
                <!-- Galerie d'images - COMMENTÉ -->
                <div class="mb-4">
                    <label class="form-label">🖼️ Galerie d'images</label>
                    <input type="file" class="form-control" name="gallery[]" multiple accept="image/*" id="gallery_input" onchange="previewMultipleImages(this, 'gallery_preview')">
                    <small class="form-text text-muted">Sélectionnez plusieurs images pour la galerie</small>
                    <div id="gallery_preview" class="mt-2" style="display: none;">
                        <div class="row" id="gallery_preview_images"></div>
                    </div>
                </div>
                --}}

                {{-- 
                <!-- Image OpenGraph - COMMENTÉ -->
                <div class="mb-4">
                    <label class="form-label">📱 Image pour le partage social (OpenGraph)</label>
                    <input type="file" class="form-control" name="og_image" accept="image/*" id="og_image_input" onchange="previewImage(this, 'og_image_preview')">
                    <small class="form-text text-muted">Image qui apparaît lors du partage sur les réseaux sociaux</small>
                    <div id="og_image_preview" class="mt-2" style="display: none;">
                        <img id="og_image_preview_img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
                --}}
            </div>

            <!-- Onglet 6 : Création -->
            <div class="tab-pane fade" id="invitation-review-panel" role="tabpanel" aria-labelledby="invitation-review-tab">
                <h5><i class="fas fa-check-circle me-2"></i>Création de l'invitation</h5>
                <p class="text-muted">Créez votre invitation avec les paramètres configurés</p>

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

                <!-- Champs cachés pour les valeurs par défaut -->
                <input type="hidden" name="content_status" value="sent">
                <input type="hidden" name="meta_title" value="">
                <input type="hidden" name="meta_description" value="">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Créer l'invitation
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

<!-- Styles pour les zones de drag & drop -->
<style>
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
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.gallery-preview {
    margin-top: 1rem;
}

.gallery-thumbnail {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.gallery-thumbnail:hover {
    transform: scale(1.05);
}

/* Animation pour les zones de drag & drop */
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

// ========================================
// GESTION DES IMAGES AVEC DRAG & DROP ET PRÉVISUALISATION
// ========================================

// Initialiser tous les zones de drag & drop
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


// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDrop();
});
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