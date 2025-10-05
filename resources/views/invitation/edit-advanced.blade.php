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
        <input type="hidden" name="status" value="{{ $content->status ?? 'published' }}">
        <input type="hidden" name="content_status" value="published">
        <input type="hidden" name="venue_name" value="{{ $event->location }}">
        <input type="hidden" name="venue_address_line1" value="{{ $event->address ?? '' }}">
        <input type="hidden" name="venue_city" value="{{ $event->city ?? '' }}">
        <input type="hidden" name="venue_country" value="{{ $event->country ?? 'RDC' }}">
        <input type="hidden" name="locale" value="fr">
        <input type="hidden" name="slug" value="{{ Str::slug($event->title) }}">
        <input type="hidden" name="unique_code" value="{{ $invitation->unique_code }}">
        <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">

        <!-- Champs manquants avec valeurs par défaut -->
        <input type="hidden" name="venue_address_line2" value="{{ $content->venue_address_line2 ?? '' }}">
        <input type="hidden" name="venue_region" value="{{ $content->venue_region ?? '' }}">
        <input type="hidden" name="google_maps_url" value="{{ $content->google_maps_url ?? '' }}">
        <input type="hidden" name="venue_lat" value="{{ $content->venue_lat ?? '' }}">
        <input type="hidden" name="venue_lng" value="{{ $content->venue_lng ?? '' }}">

        <!-- Champs de contenu avec valeurs par défaut -->
        <input type="hidden" name="intro_2" value="💌 Invitation 💌">
        <input type="hidden" name="hero_image_alt" value="{{ $content->hero_image_alt ?? '' }}">

        <!-- Champs de fonctionnalités avec valeurs par défaut -->
        <input type="hidden" name="guestbook_title" value="{{ $content->guestbook_title ?? 'Livre d\'or' }}">
        <input type="hidden" name="guestbook_subtitle" value="{{ $content->guestbook_subtitle ?? 'Laissez-nous un message' }}">
        <input type="hidden" name="drinks_title" value="{{ $content->drinks_title ?? 'Choix des boissons' }}">
        <input type="hidden" name="drinks_submit_label" value="{{ $content->drinks_submit_label ?? 'Confirmer mes choix' }}">
        <input type="hidden" name="cta_rsvp_label" value="{{ $content->cta_rsvp_label ?? 'Répondre à l\'invitation' }}">
        <input type="hidden" name="cta_map_label" value="{{ $content->cta_map_label ?? 'Voir sur la carte' }}">
        <input type="hidden" name="cta_download_label" value="{{ $content->cta_download_label ?? 'Télécharger l\'invitation' }}">

        <!-- Champs de thème avec valeurs par défaut -->
        @php
            // Extraire les couleurs du thème JSON
            $theme = json_decode($content->theme ?? '{}', true);
            $colors = $theme['colors'] ?? [];
            $fonts = $theme['fonts'] ?? [];
            $decorations = $theme['decorations'] ?? [];

            $primaryColor = $colors['primary'] ?? '#e11d48';
            $secondaryColor = $colors['secondary'] ?? '#f43f5e';
            $accentColor = $colors['accent'] ?? '#fb7185';
            $headingFont = $fonts['headings'] ?? 'Playfair Display';
            $bodyFont = $fonts['body'] ?? 'Inter';
        @endphp

        <!-- Les couleurs sont maintenant gérées par les champs visibles dans l'onglet Design & Thème -->
        <input type="hidden" name="theme_heading_font" value="{{ $headingFont }}">
        <input type="hidden" name="theme_body_font" value="{{ $bodyFont }}">

        <!-- Champs meta avec valeurs par défaut -->
        <input type="hidden" name="meta_title" value="{{ $content->meta_title ?? $event->title . ' - Invitation' }}">
        <input type="hidden" name="meta_description" value="{{ $content->meta_description ?? 'Invitation pour l\'événement de ' . $event->title }}">

        <!-- Champs de planning dynamiques avec valeurs par défaut -->
        @for($i = 0; $i < 5; $i++)
            <input type="hidden" name="schedule_time_{{ $i }}" value="{{ $content->{'schedule_time_' . $i} ?? '' }}">
            <input type="hidden" name="schedule_event_{{ $i }}" value="{{ $content->{'schedule_event_' . $i} ?? '' }}">
        @endfor

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

                <!-- Message principal (body_html) -->
                <div class="mb-4">
                    <label class="form-label">Message Principal <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote" name="body_html" rows="4" placeholder="Message principal de l'invitation...">{{ old('body_html', $content->body_html ?? '') }}</textarea>
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
                        <input type="color" class="form-control form-control-color" name="theme_primary_color" value="{{ old('theme_primary_color', $primaryColor) }}">
                        <small class="form-text text-muted">Couleur dominante de l'invitation</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur secondaire</label>
                        <input type="color" class="form-control form-control-color" name="theme_secondary_color" value="{{ old('theme_secondary_color', $secondaryColor) }}">
                        <small class="form-text text-muted">Couleur d'accent</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Couleur d'accent</label>
                        <input type="color" class="form-control form-control-color" name="theme_accent_color" value="{{ old('theme_accent_color', $accentColor) }}">
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
                                <img id="hero_image_preview_img" src="{{ $content->hero_image_path ? asset('img/invitations/hero/'.$content->hero_image_path) : '' }}" alt="Aperçu" class="preview-image">
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
                                <img id="program_background_preview_img" src="{{ $content->program_background_image ? asset('img/invitations/sections/' . $content->program_background_image) : '' }}" alt="Aperçu" class="preview-image">
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
                                <img id="guestbook_background_preview_img" src="{{ $content->guestbook_background_image ? asset('img/invitations/sections/' . $content->guestbook_background_image) : '' }}" alt="Aperçu" class="preview-image">
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
                                <img id="drinks_background_preview_img" src="{{ $content->drinks_background_image ? asset('img/invitations/sections/' . $content->drinks_background_image) : '' }}" alt="Aperçu" class="preview-image">
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
                                <img id="footer_background_preview_img" src="{{ $content->footer_background_image ? asset('img/invitations/sections/' . $content->footer_background_image) : '' }}" alt="Aperçu" class="preview-image">
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

<link rel="stylesheet" href="{{ asset('css/invitation-edit.css') }}">

<script src="{{ asset('js/invitation-edit.js') }}"></script>
@endsection
