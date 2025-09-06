<!-- Modal d'assignation en masse -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group text-warning"></i> Assignation en Masse
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('event-drinks.bulk-assign', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Sélectionner les boissons à assigner *</label>
                        <div class="row">
                            @foreach($allDrinks as $drink)
                                @if(!$eventDrinks->contains('drink_id', $drink->id))
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="drink_ids[]" value="{{ $drink->id }}" id="drink_{{ $drink->id }}">
                                        <label class="form-check-label" for="drink_{{ $drink->id }}">
                                            {{ $drink->name }} ({{ ucfirst($drink->type) }})
                                        </label>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bulk_quantity">Quantité par défaut</label>
                                <input type="number" name="default_quantity" id="bulk_quantity" class="form-control" min="1" placeholder="Illimité si vide">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bulk_price">Prix par défaut (FC)</label>
                                <input type="number" name="default_price" id="bulk_price" class="form-control" min="0" step="50" placeholder="Prix par défaut">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-layer-group"></i> Assigner en Masse
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de duplication -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-copy text-info"></i> Dupliquer d'un Événement
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('event-drinks.duplicate', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="source_event_id">Sélectionner l'événement source *</label>
                        <select name="source_event_id" id="source_event_id" class="form-control" required>
                            <option value="">-- Choisir un événement --</option>
                            @foreach(\App\Models\Event::where('id', '!=', $event->id)->get() as $sourceEvent)
                                <option value="{{ $sourceEvent->id }}">
                                    {{ $sourceEvent->title }} ({{ $sourceEvent->event_date }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Toutes les boissons de l'événement sélectionné seront copiées vers cet événement.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-copy"></i> Dupliquer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
