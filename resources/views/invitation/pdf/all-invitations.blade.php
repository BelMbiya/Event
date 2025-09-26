<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Invitations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            background: linear-gradient(135deg, #e11d48, #f43f5e);
            color: white;
            border-radius: 10px;
            padding: 20px;
        }
        
        .header h1 {
            color: white;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: rgba(255,255,255,0.9);
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
        
        .invitation-card {
            background: white;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        
        .invitation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e11d48;
        }
        
        .invitation-title {
            font-size: 20px;
            font-weight: bold;
            color: #e11d48;
        }
        
        .invitation-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-sent { background-color: #dbeafe; color: #1e40af; }
        .status-opened { background-color: #d1fae5; color: #065f46; }
        .status-responded { background-color: #e0e7ff; color: #3730a3; }
        .status-called { background-color: #fce7f3; color: #be185d; }
        
        .invitation-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .detail-group h4 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-group p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .guest-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .guest-info h5 {
            margin: 0 0 10px 0;
            color: #e11d48;
            font-size: 16px;
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
        
        .no-invitations {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        
        .page-break {
            page-break-before: always;
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
        <h1>Rapport des Invitations</h1>
        <p><strong>Généré le:</strong> {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
        <p><strong>Total d'invitations:</strong> {{ $invitations->count() }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $invitations->count() }}</div>
            <div class="stat-label">Total Invitations</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $invitations->where('status', 'sent')->count() }}</div>
            <div class="stat-label">Envoyées</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $invitations->where('status', 'opened')->count() }}</div>
            <div class="stat-label">Ouvertes</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $invitations->where('status', 'responded')->count() }}</div>
            <div class="stat-label">Répondus</div>
        </div>
    </div>
    
    <h2>Liste des Invitations</h2>
    
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Code Unique</th>
                <th style="width: 20%;">Événement</th>
                <th style="width: 20%;">Invité</th>
                <th style="width: 15%;">RSVP</th>
                <th style="width: 15%;">Envoyé le</th>
                <th style="width: 15%;">Ouvert le</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invitations as $invitation)
                <tr>
                    <td>
                        <strong>{{ $invitation->unique_code }}</strong>
                    </td>
                    <td>
                        {{ $invitation->event->title ?? 'N/A' }}
                    </td>
                    <td>
                        @if($invitation->guest)
                            {{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}
                        @else
                            <em>Non assigné</em>
                        @endif
                    </td>
                    <td>
                        @if($invitation->guest && $invitation->guest->rsvp_status)
                            @switch($invitation->guest->rsvp_status)
                                @case('confirmed')
                                    <span class="status-responded">✅ Confirmé</span>
                                    @break
                                @case('declined')
                                    <span class="status-pending">❌ Décliné</span>
                                    @break
                                @case('pending')
                                    <span class="status-pending">⏳ En attente</span>
                                    @break
                                @default
                                    <span>{{ $invitation->guest->rsvp_status }}</span>
                            @endswitch
                        @else
                            <span class="status-pending">⏳ Non répondu</span>
                        @endif
                    </td>
                    <td>
                        {{ $invitation->sent_at ? \Carbon\Carbon::parse($invitation->sent_at)->locale('fr')->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td>
                        {{ $invitation->opened_at ? \Carbon\Carbon::parse($invitation->opened_at)->locale('fr')->format('d/m/Y H:i') : '-' }}
                    </td>
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
