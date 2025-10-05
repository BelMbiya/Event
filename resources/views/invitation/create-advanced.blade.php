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

<!-- Messages de succès et d'erreur -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Erreurs de validation :</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
<div class="container-fluid">
    <h2 class="mb-4">🎨 Créer une Invitation Personnalisée - {{ $event->title }}</h2>


    <!-- Note sur les champs obligatoires -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires pour la validation et l'enregistrement.
    </div>


    <form action="{{ route('invitation.store') }}" method="POST" enctype="multipart/form-data" id="invitationForm">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        
        <!-- Champs cachés pour les informations importantes de l'événement -->
        <input type="hidden" name="couple" value="{{ old('couple', $event->title) }}">
        <input type="hidden" name="event_datetime" value="{{ old('event_datetime', $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i') : '') }}">
        <input type="hidden" name="timezone" value="{{ old('timezone', 'Africa/Kinshasa') }}">
        <input type="hidden" name="guest_id" value="{{ old('guest_id', '') }}">
        <input type="hidden" name="status" value="{{ old('status', 'sent') }}">
        <input type="hidden" name="content_status" value="{{ old('content_status', 'published') }}">
        <input type="hidden" name="venue_name" value="{{ old('venue_name', $event->location) }}">
        <input type="hidden" name="venue_address_line1" value="{{ old('venue_address_line1', $event->address ?? '') }}">
        <input type="hidden" name="venue_city" value="{{ old('venue_city', $event->city ?? '') }}">
        <input type="hidden" name="venue_region" value="{{ old('venue_region', $event->region ?? '') }}">
        <input type="hidden" name="venue_country" value="{{ old('venue_country', $event->country ?? 'République Démocratique du Congo') }}">
        <input type="hidden" name="google_maps_url" value="{{ old('google_maps_url', $event->google_maps_url ?? '') }}">

        <!-- Information sur les invités -->
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Invitations automatiques :</strong> Une invitation personnalisée sera créée automatiquement pour tous les invités de cet événement.
            <br>
            <strong>Invitations trouvées :</strong> {{ $guests->count() }} invité(s) pour "{{ $event->title }}"
        </div>

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
                    <input type="text" class="form-control @error('intro_1') is-invalid @enderror" name="intro_1" value="{{ old('intro_1', '💍 Le grand jour arrive 💍') }}" placeholder="💍 Le grand jour arrive 💍">
                    <small class="form-text text-muted">Texte qui apparaît en haut de l'invitation</small>
                    @error('intro_1')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sous-titre de l'invitation</label>
                    <input type="text" class="form-control @error('intro_2') is-invalid @enderror" name="intro_2" value="{{ old('intro_2', '💌 Invitation 💌') }}" placeholder="💌 Invitation 💌">
                    <small class="form-text text-muted">Texte qui apparaît avant le contenu principal</small>
                    @error('intro_2')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Message Principal <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote @error('body_html') is-invalid @enderror" name="body_html" rows="6" placeholder="C'est avec une immense joie...">{{ old('body_html') }}</textarea>
                    <small class="form-text text-muted">Le message principal de votre invitation</small>
                    @error('body_html')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <h6 class="mt-4">📅 Programme de l'événement</h6>
                <p class="text-muted">Définissez le déroulement de votre événement avec un éditeur riche</p>
                
                <div class="mb-3">
                    <label class="form-label">Programme détaillé</label>
                    <textarea class="form-control summernote @error('program_html') is-invalid @enderror" name="program_html" rows="8" placeholder="Créez votre programme avec des heures, descriptions et formatage...">{{ old('program_html', '<div class="program-schedule">
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
                    @error('program_html')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Onglet 3 : Design & Thème -->
            <div class="tab-pane fade" id="invitation-design-panel" role="tabpanel" aria-labelledby="invitation-design-tab">
                <h5><i class="fas fa-palette me-2"></i>Design & Thème</h5>
                <p class="text-muted">Personnalisez les couleurs et le style de votre invitation</p>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur principale</label>
                        <input type="color" class="form-control form-control-color @error('theme_primary_color') is-invalid @enderror" name="theme_primary_color" value="{{ old('theme_primary_color', '#e11d48') }}">
                        <small class="form-text text-muted">Couleur dominante de l'invitation</small>
                        @error('theme_primary_color')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur secondaire</label>
                        <input type="color" class="form-control form-control-color @error('theme_secondary_color') is-invalid @enderror" name="theme_secondary_color" value="{{ old('theme_secondary_color', '#f43f5e') }}">
                        <small class="form-text text-muted">Couleur d'accent</small>
                        @error('theme_secondary_color')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur d'accent</label>
                        <input type="color" class="form-control form-control-color @error('theme_accent_color') is-invalid @enderror" name="theme_accent_color" value="{{ old('theme_accent_color', '#fb7185') }}">
                        <small class="form-text text-muted">Couleur pour les détails</small>
                        @error('theme_accent_color')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
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
                            <option value="Inter" {{ old('theme_heading_font', 'Inter') == 'Inter' ? 'selected' : '' }}>Inter (Moderne)</option>
                            <option value="Alex Brush" {{ old('theme_heading_font', 'Inter') == 'Alex Brush' ? 'selected' : '' }}>Alex Brush (Élégant)</option>
                            <option value="Dancing Script" {{ old('theme_heading_font', 'Inter') == 'Dancing Script' ? 'selected' : '' }}>Dancing Script (Romantique)</option>
                            <option value="Great Vibes" {{ old('theme_heading_font', 'Inter') == 'Great Vibes' ? 'selected' : '' }}>Great Vibes (Classique)</option>
                            <option value="Montserrat" {{ old('theme_heading_font', 'Inter') == 'Montserrat' ? 'selected' : '' }}>Montserrat (Moderne)</option>
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
                            <input class="form-check-input @error('guestbook_enabled') is-invalid @enderror" type="checkbox" name="guestbook_enabled" value="1" {{ old('guestbook_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer le livre d'or</label>
                            @error('guestbook_enabled')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
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
                            <input class="form-check-input @error('drinks_enabled') is-invalid @enderror" type="checkbox" name="drinks_enabled" value="1" {{ old('drinks_enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Activer les choix de boissons</label>
                            @error('drinks_enabled')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
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
                        <div class="drag-drop-zone @error('hero_image_path') is-invalid @enderror" data-target="hero_image_path">
                            <input type="file" class="form-control d-none @error('hero_image_path') is-invalid @enderror" name="hero_image_path" accept="image/*" id="hero_image_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">
                                    <strong>Option 1 :</strong> Glissez-déposez votre image ici<br>
                                    <strong>Option 2 :</strong> <span class="text-primary click-to-upload">Cliquez pour sélectionner</span>
                                </p>
                                <small class="text-muted">Image qui apparaît en arrière-plan de l'en-tête (max 2MB)</small>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm file-select-btn" data-target="hero_image_path">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>
                            </div>
                            <div class="image-preview" id="hero_image_preview" style="display: none;">
                                <img id="hero_image_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="hero_image_path">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @error('hero_image_path')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Image Programme -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">📅 Image de fond - Programme</label>
                        <div class="drag-drop-zone @error('program_background_image') is-invalid @enderror" data-target="program_background_image">
                            <input type="file" class="form-control d-none @error('program_background_image') is-invalid @enderror" name="program_background_image" accept="image/*" id="program_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">
                                    <strong>Option 1 :</strong> Glissez-déposez votre image ici<br>
                                    <strong>Option 2 :</strong> <span class="text-primary click-to-upload">Cliquez pour sélectionner</span>
                                </p>
                                <small class="text-muted">Image de fond pour la section programme (max 2MB)</small>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm file-select-btn" data-target="program_background_image">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>
                            </div>
                            <div class="image-preview" id="program_background_preview" style="display: none;">
                                <img id="program_background_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="program_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @error('program_background_image')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Image Livre d'or -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">📖 Image de fond - Livre d'or</label>
                        <div class="drag-drop-zone @error('guestbook_background_image') is-invalid @enderror" data-target="guestbook_background_image">
                            <input type="file" class="form-control d-none @error('guestbook_background_image') is-invalid @enderror" name="guestbook_background_image" accept="image/*" id="guestbook_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">
                                    <strong>Option 1 :</strong> Glissez-déposez votre image ici<br>
                                    <strong>Option 2 :</strong> <span class="text-primary click-to-upload">Cliquez pour sélectionner</span>
                                </p>
                                <small class="text-muted">Image de fond pour la section livre d'or (max 2MB)</small>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm file-select-btn" data-target="guestbook_background_image">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>
                            </div>
                            <div class="image-preview" id="guestbook_background_preview" style="display: none;">
                                <img id="guestbook_background_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="guestbook_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @error('guestbook_background_image')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Image Choix de boissons -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">🍷 Image de fond - Choix de boissons</label>
                        <div class="drag-drop-zone @error('drinks_background_image') is-invalid @enderror" data-target="drinks_background_image">
                            <input type="file" class="form-control d-none @error('drinks_background_image') is-invalid @enderror" name="drinks_background_image" accept="image/*" id="drinks_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">
                                    <strong>Option 1 :</strong> Glissez-déposez votre image ici<br>
                                    <strong>Option 2 :</strong> <span class="text-primary click-to-upload">Cliquez pour sélectionner</span>
                                </p>
                                <small class="text-muted">Image de fond pour la section choix de boissons (max 2MB)</small>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm file-select-btn" data-target="drinks_background_image">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>
                            </div>
                            <div class="image-preview" id="drinks_background_preview" style="display: none;">
                                <img id="drinks_background_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="drinks_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @error('drinks_background_image')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
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
                        <div class="drag-drop-zone @error('footer_background_image') is-invalid @enderror" data-target="footer_background_image">
                            <input type="file" class="form-control d-none @error('footer_background_image') is-invalid @enderror" name="footer_background_image" accept="image/*" id="footer_background_input">
                            <div class="drag-drop-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">
                                    <strong>Option 1 :</strong> Glissez-déposez votre image ici<br>
                                    <strong>Option 2 :</strong> <span class="text-primary click-to-upload">Cliquez pour sélectionner</span>
                                </p>
                                <small class="text-muted">Image de fond pour le pied de page (max 2MB)</small>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm file-select-btn" data-target="footer_background_image">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>
                            </div>
                            <div class="image-preview" id="footer_background_preview" style="display: none;">
                                <img id="footer_background_preview_img" src="" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-sm btn-danger remove-image" data-target="footer_background_image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @error('footer_background_image')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
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
                <input type="hidden" name="content_status" value="{{ old('content_status', 'published') }}">
                <input type="hidden" name="meta_title" value="{{ old('meta_title', '') }}">
                <input type="hidden" name="meta_description" value="{{ old('meta_description', '') }}">

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

<!-- CSS personnalisé -->
<link rel="stylesheet" href="{{ asset('css/invitation-create-advanced.css') }}">

<!-- JavaScript personnalisé -->
<script src="{{ asset('js/invitation-create-advanced.js') }}"></script>

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