    <!-- Hero Section -->
    <section class="hero-section relative min-h-screen flex items-center justify-center"
             style="background-image: url('{{ asset('img/invitations/hero/'.$content->hero_image_path) }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;"
             data-parallax="scroll">
        <!-- Overlay pour la lisibilité - opacité réduite -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.05); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center" style="opacity: 0.5!important;">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase tracking-wide text-muted mb-4" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_1)
                                {!! $content->intro_1 !!}
                            @else
                                💍 Le grand jour arrive 💍
                            @endif
                        </p>

                        <!-- Message personnalisé pour l'invité -->
                        @if($guest)
                        <div class="guest-welcome mb-4">
                            <p class="elegant-font text-dark" style="font-size: 1.1rem; font-style: italic;">
                                Cher(e) <strong>{{ $guest->first_name }}</strong>,
                            </p>
                            <p class="modern-font text-muted" style="font-size: 0.95rem;">
                                Nous avons le plaisir de vous inviter à partager ce moment spécial avec nous.
                            </p>
                        </div>
                        @endif

                        <!-- Noms du couple -->
                        <h1 class="romantic-font gradient-text mb-4" style="font-size: 4rem; line-height: 1.1;">
                            {!! $content->couple ?? 'Notre Mariage' !!}
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

                        <!-- Bouton d'ouverture supprimé -->
                    </div>
                </div>
            </div>
        </div>
    </section>
