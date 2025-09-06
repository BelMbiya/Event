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
        
        .page-break {
            page-break-before: always;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Choix de Boissons</h1>
        <p><strong>Événement :</strong> {{ $event->title }}</p>
        <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->format('d/m/Y') }}</p>
        <p><strong>Lieu :</strong> {{ $event->location ?? 'Non défini' }}</p>
    </div>
    
    <!-- Statistiques -->
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $eventDrinks->count() }}</div>
            <div class="stat-label">Boissons Disponibles</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $drinkChoices->count() }}</div>
            <div class="stat-label">Invités avec Choix</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $eventDrinks->where('drink.alcoholic', false)->count() }}</div>
            <div class="stat-label">Non-Alcool</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $eventDrinks->where('drink.alcoholic', true)->count() }}</div>
            <div class="stat-label">Avec Alcool</div>
        </div>
    </div>
    
    <!-- Liste des boissons disponibles -->
    <h2>Boissons Disponibles</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Boisson</th>
                <th style="width: 20%;">Type</th>
                <th style="width: 15%;">Volume</th>
                <th style="width: 15%;">Alcool</th>
                <th style="width: 20%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eventDrinks as $eventDrink)
                <tr>
                    <td><strong>{{ $eventDrink->drink->name }}</strong></td>
                    <td>{{ ucfirst($eventDrink->drink->type) }}</td>
                    <td>{{ $eventDrink->drink->volume_ml }}ml</td>
                    <td>
                        @if($eventDrink->drink->alcoholic)
                            <span style="color: #dc3545;">Oui</span>
                        @else
                            <span style="color: #28a745;">Non</span>
                        @endif
                    </td>
                    <td>{{ $eventDrink->drink->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($drinkChoices->count() > 0)
        <div class="page-break"></div>
        
        <h2>Choix des Invités</h2>
        
        @foreach($drinkChoices as $guestId => $choices)
            @php
                $guest = $choices->first()->guest;
            @endphp
            
            <div style="margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                <h3 style="color: #667eea; margin-bottom: 15px;">
                    {{ $guest->first_name }} {{ $guest->last_name }}
                </h3>
                
                @if($guest->email)
                    <p><strong>Email :</strong> {{ $guest->email }}</p>
                @endif
                
                <h4>Choix de boissons :</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Boisson</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Date de choix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($choices as $choice)
                            <tr>
                                <td>{{ $choice->eventDrink->drink->name }}</td>
                                <td>{{ ucfirst($choice->eventDrink->drink->type) }}</td>
                                <td>{{ $choice->quantity }}</td>
                                <td>{{ \Carbon\Carbon::parse($choice->created_at)->locale('fr')->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i style="font-size: 48px; margin-bottom: 20px;">🍷</i>
            <p>Aucun choix de boisson enregistré pour le moment.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Rapport généré automatiquement le {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p>Système de gestion d'événements - {{ config('app.name') }}</p>
    </div>
</body>
</html>
