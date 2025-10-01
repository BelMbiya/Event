@extends('admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/events-drinks.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-glass-cheers text-primary"></i>
            Gestion des Boissons - {{ $event->title }}
        </h1>
        <div>
            <a href="{{ route('drink-choices.index', $event->id) }}" class="btn btn-info me-2">
                <i class="fas fa-list-check"></i> Voir les Choix
            </a>
            <button class="btn btn-success me-2" onclick="downloadPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <button class="btn btn-warning me-2" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistiques Avancées -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Boissons
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_drinks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-glass-cheers fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Non-Alcool
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['non_alcoholic_drinks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tint fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avec Alcool
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['alcoholic_drinks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wine-bottle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Invités avec Choix
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['guests_with_choices'] }}/{{ $stats['total_guests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Boissons de l'événement -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Boissons de l'événement ({{ $eventDrinks->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($eventDrinks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Boisson</th>
                                        <th>Type</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($eventDrinks as $eventDrink)
                                        <tr>
                                            <td>
                                                <strong>{{ $eventDrink->drink->name }}</strong>
                                                @if($eventDrink->drink->description)
                                                    <br><small class="text-muted">{{ $eventDrink->drink->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $eventDrink->drink->alcoholic ? 'danger' : 'success' }}">
                                                    {{ ucfirst($eventDrink->drink->type) }}
                                                    @if($eventDrink->drink->alcoholic)
                                                        (Alcool)
                                                    @else
                                                        (Non-alcool)
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $eventDrink->drink->volume_ml }}ml</strong>
                                                <br><small class="text-muted">{{ ucfirst($eventDrink->drink->unit) }}</small>
                                            </td>
                                            <td>
                                                {{ $eventDrink->limit_per_guest ?? 'Illimitée' }}
                                            </td>
                                            <td>
                                                <form action="{{ route('event-drinks.destroy', [$event->id, $eventDrink->drink->id]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette boisson de l\'événement ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-glass-cheers fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucune boisson ajoutée à cet événement.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Boissons populaires -->
        <div class="col-lg-4">
            @if($popularDrinks->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-star"></i> Boissons Populaires
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($popularDrinks as $popular)
                        @if($popular['drink'])
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $popular['drink']->name }}</div>
                                <small class="text-muted">{{ $popular['count'] }} choix</small>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bolt"></i> Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#bulkAssignModal">
                        <i class="fas fa-layer-group"></i> Assignation en Masse
                    </button>
                    <button type="button" class="btn btn-info btn-block mb-2" data-toggle="modal" data-target="#duplicateModal">
                        <i class="fas fa-copy"></i> Dupliquer d'un Événement
                    </button>
                    <a href="{{ route('drink-choices.index', $event->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-list-check"></i> Voir les Choix des Invités
                    </a>
                </div>
            </div>

        <!-- Ajouter des boissons -->
            <!-- Bouton pour ouvrir le modal de création -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle"></i> Créer une boisson
                    </h6>
                </div>
                <div class="card-body text-center">
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#createDrinkModal">
                        <i class="fas fa-plus-circle"></i> Créer une nouvelle boisson
                    </button>
                    <p class="text-muted mt-2 small">Ajoutez une boisson personnalisée à votre catalogue</p>
                </div>
            </div>

            <!-- Ajouter une boisson existante -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-plus"></i> Ajouter une boisson existante
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('event-drinks.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        
                        <div class="form-group">
                            <label for="drink_id">Sélectionner une boisson *</label>
                            <select name="drink_id" id="drink_id" class="form-control" required>
                                <option value="">-- Choisir une boisson --</option>
                                @foreach($allDrinks as $drink)
                                    @if(!$eventDrinks->contains('drink_id', $drink->id))
                                        <option value="{{ $drink->id }}" data-type="{{ $drink->type }}" data-alcoholic="{{ $drink->alcoholic }}">
                                            {{ $drink->name }} - {{ $drink->volume_ml }}ml ({{ ucfirst($drink->type) }}{{ $drink->alcoholic ? ' - Alcool' : '' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantité (optionnel)</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="Laisser vide pour illimité">
                        </div>

                        <div class="form-group">
                            <label for="price_override">Prix personnalisé (optionnel)</label>
                            <input type="number" name="price_override" id="price_override" class="form-control" min="0" step="50" placeholder="Prix en FC">
                        </div>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Ajouter la boisson
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-bar"></i> Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $eventDrinks->count() }}</div>
                            <div class="text-xs text-gray-600">Boissons</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ $eventDrinks->where('drink.alcoholic', false)->count() }}</div>
                            <div class="text-xs text-gray-600">Non-alcool</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-danger">{{ $eventDrinks->where('drink.alcoholic', true)->count() }}</div>
                            <div class="text-xs text-gray-600">Alcool</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-info">{{ $eventDrinks->where('drink.type', 'soft')->count() }}</div>
                            <div class="text-xs text-gray-600">Sodas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'édition -->
<div class="modal fade" id="editDrinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la boisson</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editDrinkForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_quantity">Quantité</label>
                        <input type="number" name="quantity" id="edit_quantity" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label for="edit_price_override">Prix personnalisé</label>
                        <input type="number" name="price_override" id="edit_price_override" class="form-control" min="0" step="50">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/events-drinks.js') }}"></script>
@endsection

@include('events.drinks-modals')

<!-- Modal de création de boisson -->
<div class="modal fade" id="createDrinkModal" tabindex="-1" role="dialog" aria-labelledby="createDrinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDrinkModalLabel">
                    <i class="fas fa-plus-circle text-primary"></i> Créer une nouvelle boisson
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('drinks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_name">Nom de la boisson *</label>
                                <input type="text" name="name" id="modal_name" class="form-control" required placeholder="Ex: Coca-Cola">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_type">Type *</label>
                                <select name="type" id="modal_type" class="form-control" required>
                                    <option value="">-- Choisir le type --</option>
                                    <option value="water">Eau</option>
                                    <option value="soft">Boisson gazeuse</option>
                                    <option value="juice">Jus</option>
                                    <option value="hot">Boisson chaude</option>
                                    <option value="beer">Bière</option>
                                    <option value="wine">Vin</option>
                                    <option value="spirit">Spiritueux</option>
                                    <option value="cocktail">Cocktail</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_alcoholic">Contient de l'alcool *</label>
                                <select name="alcoholic" id="modal_alcoholic" class="form-control" required>
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_unit">Unité *</label>
                                <select name="unit" id="modal_unit" class="form-control" required>
                                    <option value="">-- Choisir l'unité --</option>
                                    <option value="glass">Verre</option>
                                    <option value="bottle">Bouteille</option>
                                    <option value="can">Canette</option>
                                    <option value="cup">Tasse</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="modal_volume">Volume (ml)</label>
                                <input type="number" name="volume_ml" id="modal_volume" class="form-control" min="1" placeholder="Ex: 330">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer la boisson
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
