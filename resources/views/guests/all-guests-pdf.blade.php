<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Invités</title>
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
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-confirmed {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-declined {
            color: #dc3545;
            font-weight: bold;
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
        <h1>Liste de Tous les Invités</h1>
        <p><strong>Généré le:</strong> {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p><strong>Total d'invités:</strong> {{ $guests->count() }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $guests->count() }}</div>
            <div class="stat-label">Total Invités</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'confirmed')->count() }}</div>
            <div class="stat-label">Confirmés</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'pending')->count() }}</div>
            <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'declined')->count() }}</div>
            <div class="stat-label">Déclinés</div>
        </div>
    </div>
    
    <h2>Liste des Invités</h2>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">Nom</th>
                <th style="width: 15%;">Prénom</th>
                <th style="width: 20%;">Contact</th>
                <th style="width: 20%;">Événement</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 10%;">RSVP</th>
                <th style="width: 5%;">Repas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td>{{ $guest->id }}</td>
                    <td><strong>{{ $guest->last_name }}</strong></td>
                    <td>{{ $guest->first_name }}</td>
                    <td>
                        @if($guest->email)
                            <div>{{ $guest->email }}</div>
                        @endif
                        @if($guest->phone)
                            <div>{{ $guest->phone }}</div>
                        @endif
                        @if(!$guest->email && !$guest->phone)
                            <em>Aucun contact</em>
                        @endif
                    </td>
                    <td>{{ $guest->event->title ?? 'N/A' }}</td>
                    <td>{{ $guest->guestType->name ?? 'Invité' }}</td>
                    <td>
                        @switch($guest->rsvp_status)
                            @case('confirmed')
                                <span class="status-confirmed">✓ Confirmé</span>
                                @break
                            @case('declined')
                                <span class="status-declined">✗ Décliné</span>
                                @break
                            @default
                                <span class="status-pending">⏳ En attente</span>
                        @endswitch
                    </td>
                    <td>{{ $guest->meal_choice ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Rapport généré automatiquement le {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p>Système de gestion d'événements - {{ config('app.name') }}</p>
    </div>
</body>
</html>
