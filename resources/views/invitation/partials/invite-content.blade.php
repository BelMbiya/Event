    <!-- Section Invitation -->
    <section id="invitation-content" class="invitation-section py-5" style="background-image: url('{{ asset('img/invitations/sections/'.$content->program_background_image) }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.1); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase text-muted mb-4 text-center" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_2)
                                {!! $content->intro_2 !!}
                            @else
                                INVITATION
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
                                        <strong>Couple :</strong> {!! $content->couple !!}
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
                        @if(isset($guest))
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
                                    @if(isset($guest->rsvp_status))
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Statut RSVP :</strong>
                                        <span style="color: #000;" class="badge badge-{{ $guest->rsvp_status == 'confirmed' ? 'success' : ($guest->rsvp_status == 'declined' ? 'danger' : 'warning') }}">
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
                                    @if(isset($guest->event_table_id))
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
                                @if(isset($guest))
                                <form action="{{ isset($guest->id) ? route('guests.rsvp', $guest->id) : "#" }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="modern-button">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ $ctaData['rsvp']['label'] ?? 'Confirmer ma présence' }}
                                    </button>
                                </form>
                                @endif

                                <!-- Bouton Voir sur la carte - TOUJOURS visible -->
                                @php
                                    $mapUrl = $content->google_maps_url ?? $event->google_maps_url;
                                    if (!$mapUrl && $content->venue_lat && $content->venue_lng) {
                                        $mapUrl = "https://maps.google.com/maps?q={$content->venue_lat},{$content->venue_lng}";
                                    } elseif (!$mapUrl && $content->venue_name) {
                                        $mapUrl = "https://maps.google.com/maps?q=" . urlencode($content->venue_name . ', ' . ($content->venue_city ?? ''));
                                    } else {
                                        $mapUrl = $mapUrl ?? 'https://maps.google.com';
                                    }
                                @endphp
                                <a href="{{ $mapUrl }}" target="_blank" class="modern-button map-button">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $ctaData['map']['label'] ?? 'Voir sur la carte' }}
                                </a>

                                <!-- Bouton Télécharger PDF - toujours visible -->
                                <a href="{{ route('invitation.download', $invitation->unique_code) }}" class="modern-button download-button">
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
