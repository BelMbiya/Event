<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation - {{ $guest->first_name }} {{ $guest->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .info-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .info-section h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-declined {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .drink-choices {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .drink-badge {
            background-color: #667eea;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invitation Personnalisée</h1>
        <p><strong>Invitation pour:</strong> {{ $guest->first_name }} {{ $guest->last_name }}</p>
        <p><strong>Généré le:</strong> {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
    </div>
    
    <div class="info-section">
        <h3>Informations Personnelles</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Nom complet:</span>
                    <span class="info-value">{{ $guest->first_name }} {{ $guest->last_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Type d'invité:</span>
                    <span class="info-value">{{ $guest->guestType->name ?? 'Invité' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $guest->email ?? 'Non renseigné' }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Téléphone:</span>
                    <span class="info-value">{{ $guest->phone ?? 'Non renseigné' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut RSVP:</span>
                    <span class="info-value">
                        @switch($guest->rsvp_status)
                            @case('confirmed')
                                <span class="status-badge status-confirmed">Confirmé</span>
                                @break
                            @case('declined')
                                <span class="status-badge status-declined">Décliné</span>
                                @break
                            @default
                                <span class="status-badge status-pending">En attente</span>
                        @endswitch
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Choix de repas:</span>
                    <span class="info-value">{{ $guest->meal_choice ?? 'Non spécifié' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    @if($guest->event)
    <div class="info-section">
        <h3>Informations de l'Événement</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Titre de l'événement:</span>
                    <span class="info-value">{{ $guest->event->title }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($guest->event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Lieu:</span>
                    <span class="info-value">{{ $guest->event->location ?? 'Non défini' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Description:</span>
                    <span class="info-value">{{ $guest->event->description ?? 'Non disponible' }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($guest->drinkChoices && $guest->drinkChoices->count() > 0)
    <div class="info-section">
        <h3>Choix de Boissons</h3>
        <div class="drink-choices">
            @foreach($guest->drinkChoices as $choice)
                <span class="drink-badge">{{ $choice->eventDrink->drink->name }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="info-section">
        <h3>Informations Techniques</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">ID Invité:</span>
                    <span class="info-value">{{ $guest->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">URL Unique:</span>
                    <span class="info-value">{{ $guest->unique_url ?? 'Non générée' }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Date de création:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($guest->created_at)->locale('fr')->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dernière mise à jour:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($guest->updated_at)->locale('fr')->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>Invitation générée automatiquement le {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p>Système de gestion d'événements - {{ config('app.name') }}</p>
    </div>
</body>
</html>
