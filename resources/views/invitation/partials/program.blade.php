    <!-- Section Programme/Schedule -->
    @if($content->program_html || $content->schedule)
    <section class="schedule-section py-5" style="background-image: url('{{ $programBgUrl }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.2); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center" style="opacity: 1;">
                        <div class="schedule-header mb-5">
                            <h2 class="modern-font gradient-text mb-3" style="font-size: 2.8rem; font-weight: 700;">
                                <i class="fas fa-calendar-alt me-3" style="color: #e11d48;"></i>Programme de la Journée
                        </h2>
                            <div class="schedule-subtitle">
                                <p class="elegant-font text-muted" style="font-size: 1.2rem; font-style: italic;">
                                    Découvrez le déroulement de cette journée exceptionnelle
                                </p>
                                <div class="schedule-divider">
                                    <div class="divider-line"></div>
                                    <i class="fas fa-heart" style="color: #e11d48; font-size: 1.2rem;"></i>
                                    <div class="divider-line"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Programme HTML personnalisé ou Schedule -->
                        <div class="program-content">
                            @if($content->program_html)
                                {!! $content->program_html !!}
                            @elseif($content->schedule)
                                @php
                                    $schedule = is_string($content->schedule) ? json_decode($content->schedule, true) : $content->schedule;
                                @endphp
                                @if($schedule && is_array($schedule))
                                    <div class="timeline-container">
                                        @foreach($schedule as $index => $item)
                                            @php
                                                $time = $item['time'] ?? '';
                                                $eventItem = $item['event'] ?? '';
                                                $location = $item['location'] ?? '';
                                            @endphp
                                            @if($time && $eventItem)
                                                <div class="timeline-item {{ $loop->index % 2 == 0 ? 'timeline-left' : 'timeline-right' }}">
                                                    <div class="timeline-marker">
                                                        <div class="timeline-icon">
                                                            @if($eventItem && (strpos(strtolower($eventItem), 'cérémonie') !== false || strpos(strtolower($eventItem), 'mariage') !== false))
                                                                <i class="fas fa-heart"></i>
                                                            @elseif($eventItem && (strpos(strtolower($eventItem), 'cocktail') !== false || strpos(strtolower($eventItem), 'apéritif') !== false))
                                                                <i class="fas fa-glass-cheers"></i>
                                                            @elseif($eventItem && (strpos(strtolower($eventItem), 'dîner') !== false || strpos(strtolower($eventItem), 'repas') !== false))
                                                                <i class="fas fa-utensils"></i>
                                                            @elseif($eventItem && (strpos(strtolower($eventItem), 'danse') !== false || strpos(strtolower($eventItem), 'soirée') !== false))
                                                                <i class="fas fa-music"></i>
                                                            @else
                                                                <i class="fas fa-clock"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-time">{{ $time }}</div>
                                                        <div class="timeline-event">{{ $eventItem }}</div>
                                                        @if($location)
                                                            <div class="timeline-location">
                                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $location }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="schedule-text">
                                        <p class="elegant-font" style="font-size: 1.1rem; line-height: 1.8; color: #666;">
                                            {!! $content->schedule !!}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
