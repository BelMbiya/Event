    <!-- Section Préférences de Boissons -->
    @if($content->drinks_enabled && $eventDrinks && $eventDrinks->count() > 0)
    <section class="drinks-section py-5" style="background-image: url('{{ asset('img/invitations/sections/'.$content->drinks_background_image) }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay sombre léger pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center" style="opacity: 1;">
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

                        @php
                            // Vérifier si l'invité a déjà fait ses choix
                            $hasExistingChoices = $guestDrinkChoices && $guestDrinkChoices->count() > 0;
                            $selectedDrinkIds = $hasExistingChoices ? $guestDrinkChoices->pluck('event_drink_id')->toArray() : [];
                        @endphp

                        @if($hasExistingChoices)
                            <!-- Affichage des choix existants (lecture seule) -->
                            <div class="mb-4">
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Vos choix ont été enregistrés !</strong>
                                </div>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    @foreach ($eventDrinks as $drink)
                                        @php
                                            $isSelected = in_array($drink->id, $selectedDrinkIds);
                                        @endphp
                                        <div class="drink-option mb-2">
                                            <span class="btn {{ $isSelected ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-4 py-2 drink-label disabled">
                                                @if($isSelected)
                                                    <i class="fas fa-check me-2"></i>
                                                @endif
                                                {{ $drink->drink->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Formulaire pour faire les choix -->
                            <form action="{{ route('guest_drink_choices.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="guest_id" value="{{ $guest->id ?? '' }}">
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="unique_code" value="{{ $unique_code }}">

                                <div class="mb-4">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @foreach ($eventDrinks as $drink)
                                            <div class="drink-option mb-2">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
