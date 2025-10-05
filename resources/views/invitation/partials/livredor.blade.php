    <!-- Section Livre d'Or -->
    @if($content->guestbook_enabled)
    <section class="guestbook-section py-5" style="background-image: url('{{ $guestbookBgUrl ?? "" }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.2); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center" style="opacity: 1;">
                        <h2 class="modern-font gradient-text mb-4" style="font-size: 2.5rem;">
                            {!! $content->guestbook_title ?? 'Livre d\'or' !!}
                        </h2>
                        <p class="elegant-font text-dark mb-5" style="font-size: 1.1rem;">
                            {!! $content->guestbook_subtitle ?? 'Laissez-nous un mot, une pensée ou une bénédiction pour marquer ce moment unique.' !!}
                        </p>

                        @php
                            // Vérifier si l'invité a déjà envoyé un message
                            if (isset($guestBookMessages))
                                {
                                    $hasExistingMessage = $guestBookMessages && $guestBookMessages->count() > 0;
                            $existingMessage = $hasExistingMessage ? $guestBookMessages->first() : null;
                                }
                        @endphp

                        @if(isset($hasExistingMessage))
                            <!-- Affichage du message existant (lecture seule) -->
                            <div class="text-start bg-light rounded-3 p-4">
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Votre message a été envoyé !</strong>
                                </div>
                                <h3 class="modern-font text-dark mb-3">
                                    <i class="fas fa-heart me-2" style="color: #e11d48;"></i>
                                    Votre message
                                </h3>
                                <div class="bg-white rounded-3 p-4 border">
                                    <p class="text-dark mb-3" style="font-size: 1.1rem; line-height: 1.6;">
                                        "{{ $existingMessage->message }}"
                                    </p>
                                    <div class="text-muted small">
                                        <i class="fas fa-clock me-1"></i>
                                        Envoyé le {{ \Carbon\Carbon::parse($existingMessage->created_at)->locale('fr')->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Formulaire pour envoyer un message -->
                            <div class="text-start bg-light rounded-3 p-4">
                                <h3 class="modern-font text-dark mb-3">
                                    @if($guest)
                                        Écrire un message - {{ $guest->first_name }} {{ $guest->last_name }}
                                    @else
                                        Écrire un message
                                    @endif
                                </h3>
                                <form action="{{ isset($guest->id) ? route('book.store.dynamic', $guest->id) : "#"}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                                    <div class="mb-3">
                                        <label class="form-label text-dark">Votre message</label>
                                        <textarea rows="4" name="message" class="form-control" required
                                            placeholder="Écrivez votre message ici..."></textarea>
                                    </div>
                                    <button type="submit" class="modern-button">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Envoyer
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
