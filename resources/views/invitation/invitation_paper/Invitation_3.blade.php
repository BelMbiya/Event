<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->couple ?? $content->meta_title ?? 'Notre Mariage' }} - Invitation Électronique</title>
    
    <!-- Métadonnées SEO personnalisables -->
    <meta name="description" content="{{ $content->meta_description ?? 'Invitation électronique pour notre mariage' }}">
    <meta name="keywords" content="mariage, invitation, {{ $content->couple ?? 'couple' }}, {{ $content->venue_city ?? 'Kinshasa' }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $content->couple ?? $content->meta_title ?? 'Notre Mariage' }}">
    <meta property="og:description" content="{{ $content->meta_description ?? 'Invitation électronique pour notre mariage' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($content->og_image)
    <meta property="og:image" content="{{ asset('storage/' . $content->og_image) }}">
    @endif
    
    <!-- Ajout de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cormorant+Garamond:wght@300;400;500&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .romantic-font {
            font-family: 'Alex Brush', cursive;
        }

        .elegant-font {
            font-family: 'Cormorant Garamond', serif;
        }

        .modern-font {
            font-family: 'Poppins', sans-serif;
        }

        .animate-fade-in {
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        /* Styles modernes pour l'invitation électronique - Personnalisables via content.theme */
        .digital-invitation {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(135deg, {{ $theme['colors']['primary'] ?? '#667eea' }} 0%, {{ $theme['colors']['secondary'] ?? '#764ba2' }} 100%);
            @else
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            @endif
            min-height: 100vh;
        }

        .invitation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .gradient-text {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? '#667eea' }}, {{ $theme['colors']['secondary'] ?? '#764ba2' }});
            @else
                background: linear-gradient(45deg, #667eea, #764ba2);
            @endif
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modern-button {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? '#667eea' }}, {{ $theme['colors']['secondary'] ?? '#764ba2' }});
            @else
                background: linear-gradient(45deg, #667eea, #764ba2);
            @endif
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
        }

        /* Timeline Styles */
        .timeline-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #667eea, #764ba2);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin: 40px 0;
            width: 100%;
        }

        .timeline-marker {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            border: 4px solid white;
        }

        .timeline-content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            width: 45%;
            margin-top: -30px;
        }

        .timeline-left .timeline-content {
            margin-left: 0;
            margin-right: auto;
        }

        .timeline-right .timeline-content {
            margin-left: auto;
            margin-right: 0;
        }

        .timeline-time {
            font-size: 1.2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .timeline-event {
            font-size: 1rem;
            color: #333;
            line-height: 1.6;
            font-family: 'Cormorant Garamond', serif;
        }

        .timeline-content::before {
            content: '';
            position: absolute;
            top: 30px;
            width: 0;
            height: 0;
            border: 15px solid transparent;
        }

        .timeline-left .timeline-content::before {
            right: -30px;
            border-left-color: white;
        }

        .timeline-right .timeline-content::before {
            left: -30px;
            border-right-color: white;
        }

        @media (max-width: 768px) {
            .timeline::before {
                left: 30px;
            }

            .timeline-marker {
                left: 30px;
            }

            .timeline-content {
                width: calc(100% - 80px);
                margin-left: 80px !important;
                margin-right: 0 !important;
            }

            .timeline-content::before {
                left: -15px !important;
                right: auto !important;
                border-right-color: white !important;
                border-left-color: transparent !important;
            }
        }
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .modern-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Styles pour la section des boissons */
        .drinks-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 3rem 0;
        }
        
        .drink-label {
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .drink-label:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-check:checked + .btn {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }

        .section-visible {
            animation: fadeInUp 1s ease forwards;
        }
    </style>
</head>

<body class="digital-invitation">
    <!-- Éléments flottants décoratifs -->
    <div class="floating-elements">
        <div class="floating-element" style="top: 10%; left: 10%; animation-delay: 0s;">
            <i class="fas fa-heart" style="font-size: 2rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="top: 20%; right: 15%; animation-delay: 2s;">
            <i class="fas fa-star" style="font-size: 1.5rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="bottom: 30%; left: 20%; animation-delay: 4s;">
            <i class="fas fa-heart" style="font-size: 1.8rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="bottom: 20%; right: 10%; animation-delay: 1s;">
            <i class="fas fa-star" style="font-size: 2.2rem; color: rgba(255,255,255,0.3);"></i>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center" 
             @if($content->hero_image_path && file_exists(public_path('storage/' . $content->hero_image_path)))
             style="background-image: url('{{ asset('storage/' . $content->hero_image_path) }}'); background-size: cover; background-position: center;"
             @elseif($content->hero_image_path)
             style="background-image: url('{{ $content->hero_image_path }}'); background-size: cover; background-position: center;"
             @endif>
        @if($content->hero_image_path)
        <!-- Overlay pour la lisibilité -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        @endif
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase tracking-wide text-muted mb-4" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_1)
                                {{ $content->intro_1 }}
                            @else
                                💍 Le grand jour arrive 💍
                            @endif
                        </p>

                        <!-- Message personnalisé pour l'invité -->
                        @if($guest)
                        <div class="guest-welcome mb-4">
                            <p class="elegant-font text-dark" style="font-size: 1.1rem; font-style: italic;">
                                Cher(e) <strong>{{ $guest->first_name }} {{ $guest->last_name }}</strong>,
                            </p>
                            <p class="modern-font text-muted" style="font-size: 0.95rem;">
                                Nous avons le plaisir de vous inviter à partager ce moment spécial avec nous.
                            </p>
                        </div>
                        @endif

                        <!-- Noms du couple -->
                        <h1 class="romantic-font gradient-text mb-4" style="font-size: 4rem; line-height: 1.1;">
                            {{ $content->couple ?? 'Notre Mariage' }}
                        </h1>

                        <!-- Date de l'événement -->
                        @if($content->event_datetime)
                        <p class="modern-font text-dark mb-5" style="font-size: 1.3rem; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('l d F Y') }}
                        </p>
                        @elseif($event->event_date)
                        <p class="modern-font text-dark mb-5" style="font-size: 1.3rem; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                        </p>
                        @endif

                        <!-- Bouton d'ouverture -->
                        <button class="modern-button" onclick="scrollToContent()">
                            <i class="fas fa-envelope me-2"></i>
                            Ouvrir l'invitation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Invitation -->
    <section id="invitation-content" class="py-5" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase text-muted mb-4 text-center" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_2)
                                {{ $content->intro_2 }}
                            @else
                                💌 Invitation 💌
                            @endif
                        </p>

                        <!-- Texte principal -->
                        <div class="text-center mb-5">
                            @if($content->body_html)
                                <div class="elegant-font text-dark" style="font-size: 1.2rem; line-height: 1.8;">
                                    {!! $content->body_html !!}
                                </div>
                            @elseif($content->Content)
                                <div class="elegant-font text-dark" style="font-size: 1.2rem; line-height: 1.8;">
                                    {!! $content->Content !!}
                                </div>
                            @else
                                <p class="elegant-font text-dark mb-4" style="font-size: 1.2rem; line-height: 1.8;">
                                    C'est avec une immense joie et beaucoup d'amour que nous vous invitons
                                    à célébrer notre union sacrée.
                                </p>
                                <p class="elegant-font text-dark mb-4" style="font-size: 1.2rem; line-height: 1.8;">
                                    Rejoignez-nous pour partager ce moment unique de bonheur, d'émotion et de fête,
                                    entourés de ceux qui nous sont chers.
                                </p>
                            @endif
                            
                            <!-- Informations supplémentaires du contenu -->
                            @if($content->couple && $content->couple !== 'Notre Mariage')
                                <div class="couple-details mt-4 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px;">
                                    <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                        <strong>Couple :</strong> {{ $content->couple }}
                                    </p>
                                    @if($content->epoux || $content->epouse)
                                        <div class="row">
                                            @if($content->epoux)
                                            <div class="col-md-6">
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                                    <strong>Époux :</strong> {{ $content->epoux }}
                                                </p>
                                                @if($content->Famille_epoux)
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.8rem;">
                                                    <em>Famille de {{ $content->epoux }}</em>
                                                </p>
                                                @endif
                                            </div>
                                            @endif
                                            @if($content->epouse)
                                            <div class="col-md-6">
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                                    <strong>Épouse :</strong> {{ $content->epouse }}
                                                </p>
                                                @if($content->Famille_epouse)
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.8rem;">
                                                    <em>Famille de {{ $content->epouse }}</em>
                                                </p>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Informations de l'événement -->
                        <div class="row text-center mb-5">
                            <div class="col-md-6 mb-3">
                                <h4 class="modern-font gradient-text mb-2">📅 Date & Heure</h4>
                                @if($content->event_datetime)
                                    <p class="elegant-font text-dark">
                                        {{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('l d F Y à H:i') }}
                                    </p>
                                @elseif($event->event_date)
                                    <p class="elegant-font text-dark">
                                        {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                                    </p>
                                @endif
                                
                                @if($content->timezone)
                                    <p class="modern-font text-muted small">
                                        Fuseau horaire: {{ $content->timezone }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="modern-font gradient-text mb-2">📍 Lieu</h4>
                                @if($content->venue_name)
                                    <p class="elegant-font text-dark">{{ $content->venue_name }}</p>
                                    @if($content->venue_address_line1)
                                        <p class="modern-font text-muted small">{{ $content->venue_address_line1 }}</p>
                                    @endif
                                    @if($content->venue_address_line2)
                                        <p class="modern-font text-muted small">{{ $content->venue_address_line2 }}</p>
                                    @endif
                                    @if($content->venue_city)
                                        <p class="modern-font text-muted small">
                                            {{ $content->venue_city }}
                                            @if($content->venue_region), {{ $content->venue_region }}@endif
                                            @if($content->venue_country), {{ $content->venue_country }}@endif
                                        </p>
                                    @endif
                                @elseif($event->location)
                                    <p class="elegant-font text-dark">{{ $event->location }}</p>
                                @else
                                    <p class="elegant-font text-dark">Lieu à confirmer</p>
                                @endif
                            </div>
                        </div>

                        <!-- Informations spécifiques à l'invité -->
                        @if($guest)
                        <div class="guest-info-section mb-4 p-4" style="background: rgba(102, 126, 234, 0.1); border-radius: 15px; border: 1px solid rgba(102, 126, 234, 0.2);">
                            <h4 class="modern-font gradient-text mb-3 text-center">
                                <i class="fas fa-user-circle me-2"></i>Vos Informations
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Nom :</strong> {{ $guest->first_name }} {{ $guest->last_name }}
                                    </p>
                                    @if($guest->email)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Email :</strong> {{ $guest->email }}
                                    </p>
                                    @endif
                                    @if($guest->phone)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Téléphone :</strong> {{ $guest->phone }}
                                    </p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($guest->rsvp_status)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Statut RSVP :</strong> 
                                        <span class="badge badge-{{ $guest->rsvp_status == 'confirmed' ? 'success' : ($guest->rsvp_status == 'declined' ? 'danger' : 'warning') }}">
                                            @switch($guest->rsvp_status)
                                                @case('confirmed')
                                                    ✅ Confirmé
                                                    @break
                                                @case('declined')
                                                    ❌ Décliné
                                                    @break
                                                @case('pending')
                                                    ⏳ En attente
                                                    @break
                                                @default
                                                    {{ $guest->rsvp_status }}
                                            @endswitch
                                        </span>
                                    </p>
                                    @endif
                                    @if($guest->meal_choice)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Choix de repas :</strong> {{ $guest->meal_choice }}
                                    </p>
                                    @endif
                                    @if($guest->event_table_id)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Table :</strong> {{ $guest->eventTable->name ?? 'Table ' . $guest->event_table_id }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Boutons d'action personnalisables -->
                        <div class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                @php
                                    $ctaData = [];
                                    if ($content->cta) {
                                        if (is_string($content->cta)) {
                                            $ctaData = json_decode($content->cta, true) ?: [];
                                        } elseif (is_array($content->cta)) {
                                            $ctaData = $content->cta;
                                        }
                                    }
                                @endphp
                                
                                <!-- Bouton Confirmer présence - toujours visible si invité -->
                                @if($guest)
                                <form action="{{ route('guests.rsvp', $guest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="modern-button">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ $ctaData['rsvp']['label'] ?? 'Confirmer ma présence' }}
                                    </button>
                                </form>
                                @endif

                                <!-- Bouton Voir sur la carte - toujours visible si URL disponible -->
                                @if($content->google_maps_url || $event->google_maps_url)
                                <a href="{{ $content->google_maps_url ?? $event->google_maps_url }}" target="_blank" class="modern-button">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $ctaData['map']['label'] ?? 'Voir sur la carte' }}
                                </a>
                                @endif

                                <!-- Bouton Télécharger PDF - toujours visible -->
                                <a href="{{ route('invitation.download', $invitation->unique_code) }}" class="modern-button">
                                    <i class="fas fa-download me-2"></i>
                                    {{ $ctaData['download']['label'] ?? 'Télécharger PDF' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Livre d'Or -->
    @if($content->guestbook_enabled)
    <section class="py-5" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center">
                        <h2 class="modern-font gradient-text mb-4" style="font-size: 2.5rem;">
                            {{ $content->guestbook_title ?? 'Livre d\'or' }}
                        </h2>
                        <p class="elegant-font text-dark mb-5" style="font-size: 1.1rem;">
                            {{ $content->guestbook_subtitle ?? 'Laissez-nous un mot, une pensée ou une bénédiction pour marquer ce moment unique.' }}
                        </p>

                        <!-- Formulaire d'ajout -->
                        <div class="text-start bg-light rounded-3 p-4">
                            <h3 class="modern-font text-dark mb-3">
                                @if($guest)
                                    Écrire un message - {{ $guest->first_name }} {{ $guest->last_name }}
                                @else
                                    Écrire un message
                                @endif
                            </h3>
                            <form action="{{ route('book.store', ['unique_code' => $unique_code]) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-dark">Votre message</label>
                                    <textarea rows="4" name="message" class="form-control"
                                        placeholder="Écrivez votre message ici..."></textarea>
                                </div>
                                <button type="submit" class="modern-button">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Envoyer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Programme/Schedule -->
    @if($content->schedule)
    <section class="py-5" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center">
                        <h2 class="modern-font gradient-text mb-5" style="font-size: 2.5rem;">
                            <i class="fas fa-calendar-alt me-3"></i>Programme de la Journée
                        </h2>
                        
                        @php
                            $schedule = [];
                            if ($content->schedule) {
                                if (is_string($content->schedule)) {
                                    $schedule = json_decode($content->schedule, true) ?: [];
                                } elseif (is_array($content->schedule)) {
                                    $schedule = $content->schedule;
                                }
                            }
                        @endphp
                        
                        @if($schedule && is_array($schedule) && count($schedule) > 0)
                            <div class="timeline-container">
                                <div class="timeline">
                                    @foreach($schedule as $index => $item)
                                        @php
                                            $time = '';
                                            $event = '';
                                            
                                            if (is_array($item) && isset($item['time']) && isset($item['event'])) {
                                                $time = $item['time'] ?? '';
                                                $event = $item['event'] ?? '';
                                            } elseif (is_string($item)) {
                                                $scheduleKeys = array_keys($schedule);
                                                $time = $scheduleKeys[$index] ?? '';
                                                $event = $item;
                                            }
                                        @endphp
                                        
                                        @if($time && $event)
                                            <div class="timeline-item {{ $index % 2 == 0 ? 'timeline-left' : 'timeline-right' }}">
                                                <div class="timeline-marker">
                                                    <div class="timeline-icon">
                                                        @if($event && (strpos(strtolower($event), 'cérémonie') !== false || strpos(strtolower($event), 'mariage') !== false))
                                                            <i class="fas fa-heart"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'cocktail') !== false || strpos(strtolower($event), 'apéritif') !== false))
                                                            <i class="fas fa-glass-cheers"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'dîner') !== false || strpos(strtolower($event), 'repas') !== false))
                                                            <i class="fas fa-utensils"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'danse') !== false || strpos(strtolower($event), 'soirée') !== false))
                                                            <i class="fas fa-music"></i>
                                                        @else
                                                            <i class="fas fa-clock"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="timeline-time">{{ $time }}</div>
                                                    <div class="timeline-event">{{ $event }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="no-schedule">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-plus text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h4 class="text-muted">Programme à venir</h4>
                                    <p class="text-muted">Le programme détaillé sera bientôt disponible</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Préférences de Boissons -->
    @if($content->drinks_enabled && $eventDrinks && $eventDrinks->count() > 0)
    <section class="drinks-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center">
                        @php
                            $drinksData = [];
                            if ($content->drinks) {
                                if (is_string($content->drinks)) {
                                    $drinksData = json_decode($content->drinks, true) ?: [];
                                } elseif (is_array($content->drinks)) {
                                    $drinksData = $content->drinks;
                                }
                            }
                        @endphp
                        <h4 class="romantic-font gradient-text mb-4" style="font-size: 2.5rem;">
                            {{ $drinksData['title'] ?? 'Choisissez vos boissons' }}
                        </h4>
                        
                        <form action="{{ route('guest_drink_choices.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="guest_id" value="{{ $guest->id ?? '' }}">
                            <input type="hidden" name="unique_code" value="{{ $unique_code }}">

                            <div class="mb-4">
                                <div class="d-flex flex-wrap justify-content-center">
                                    @foreach ($eventDrinks as $drink)
                                        <div class="drink-option">
                                            <!-- Checkbox masqué -->
                                            <input type="checkbox" 
                                                   class="btn-check" 
                                                   name="drinks[]" 
                                                   value="{{ $drink->id }}" 
                                                   id="drink{{ $drink->id }}" 
                                                   autocomplete="off">

                                            <!-- Le label devient le bouton stylisé -->
                                            <label class="btn btn-outline-primary rounded-pill px-4 py-2 drink-label" 
                                                   for="drink{{ $drink->id }}">
                                                {{ $drink->drink->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="modern-button">
                                <i class="fas fa-check me-2"></i>
                                {{ $drinksData['submit_label'] ?? 'Valider mes choix' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Galerie -->
    @php
        $gallery = [];
        if ($content->gallery) {
            if (is_string($content->gallery)) {
                $gallery = json_decode($content->gallery, true) ?: [];
            } elseif (is_array($content->gallery)) {
                $gallery = $content->gallery;
            }
        }
    @endphp
    @if($gallery && is_array($gallery))
    <section class="py-5" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center">
                        <h2 class="modern-font gradient-text mb-4" style="font-size: 2.5rem;">Galerie</h2>
                        <div class="row">
                            @php
                                // $gallery est déjà défini plus haut
                            @endphp
                            @foreach($gallery as $image)
                                <div class="col-md-4 mb-3">
                                    <div class="image-frame">
                                        @if(file_exists(public_path('storage/' . $image)))
                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $content->hero_image_alt ?? 'Image de la galerie' }}" 
                                                 class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: cover;">
                                        @else
                                            <img src="{{ $image }}" alt="{{ $content->hero_image_alt ?? 'Image de la galerie' }}" 
                                                 class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div class="placeholder-image d-none" style="width: 100%; height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="py-5" style="background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px);">
        <div class="container text-center">
            <p class="romantic-font text-white mb-2" style="font-size: 2rem;">{{ $content->couple ?? 'Notre Mariage' }}</p>
            @if($content->event_datetime)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('d F Y') }}</p>
            @elseif($event->event_date)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('d F Y') }}</p>
            @endif
        </div>
    </footer>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function scrollToContent() {
            document.getElementById('invitation-content').scrollIntoView({
                behavior: 'smooth'
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('section-visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('section:not(:first-child)').forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>