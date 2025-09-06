<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix de Boissons - {{ $event->title }}</title>
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
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .drink-badge {
            background-color: #667eea;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            margin: 1px;
            display: inline-block;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Choix de Boissons</h1>
        <p><strong>Événement:</strong> {{ $event->title }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</p>
        <p><strong>Généré le:</strong> {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $guests->count() }}</div>
            <div class="stat-label">Total Invités</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('has_drink_choices', true)->count() }}</div>
            <div class="stat-label">Choix Effectués</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('has_drink_choices', false)->count() }}</div>
            <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $eventDrinks->count() }}</div>
            <div class="stat-label">Boissons Disponibles</div>
        </div>
    </div>
    
    <h2>Liste des Invités et leurs Choix</h2>
    
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Nom</th>
                <th style="width: 15%;">Contact</th>
                <th style="width: 35%;">Choix de Boissons</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 20%;">Type d'Invitation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td>
                        <strong>{{ $guest->first_name }} {{ $guest->last_name }}</strong>
                    </td>
                    <td>
                        @if($guest->email)
                            <div>{{ $guest->email }}</div>
                        @endif
                        @if($guest->phone)
                            <div>{{ $guest->phone }}</div>
                        @endif
                    </td>
                    <td>
                        @if($guest->drinkChoices->count() > 0)
                            @foreach($guest->drinkChoices as $choice)
                                <span class="drink-badge">
                                    {{ $choice->eventDrink->drink->name }}
                                    @if($choice->eventDrink->price)
                                        ({{ $choice->eventDrink->price }}€)
                                    @endif
                                </span>
                            @endforeach
                            <br><small><strong>Total:</strong> {{ $guest->drinkChoices->count() }} boisson(s)</small>
                        @else
                            <em style="color: #999;">Aucun choix</em>
                        @endif
                    </td>
                    <td>
                        @if($guest->has_drink_choices)
                            <span class="status-completed">✓ Complété</span>
                        @else
                            <span class="status-pending">⏳ En attente</span>
                        @endif
                    </td>
                    <td>
                        {{ $guest->guestType->name ?? 'Invité' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Résumé des Choix</h2>
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $guests->sum(function($guest) { return $guest->drinkChoices->count(); }) }}</div>
            <div class="stat-label">Total des Choix</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($guests->sum(function($guest) { 
                return $guest->drinkChoices->sum(function($choice) { 
                    return $choice->eventDrink->price ?? 0; 
                }); 
            }), 0) }}€</div>
            <div class="stat-label">Prix Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('has_drink_choices', true)->count() }}</div>
            <div class="stat-label">Invités avec Choix</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('has_drink_choices', false)->count() }}</div>
            <div class="stat-label">Invités sans Choix</div>
        </div>
    </div>
    
    
    <div class="footer">
        <p>Rapport généré automatiquement le {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p>Système de gestion d'événements - {{ config('app.name') }}</p>
    </div>
</body>
</html>
