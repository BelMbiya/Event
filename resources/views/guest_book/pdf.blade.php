<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre d'Or - {{ $event->title }}</title>
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
            margin-top: 5px;
        }
        
        .message-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .author {
            font-weight: bold;
            color: #333;
        }
        
        .date {
            color: #666;
            font-size: 10px;
        }
        
        .status {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .message-content {
            color: #333;
            line-height: 1.5;
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
        <h1>Livre d'Or</h1>
        <p><strong>Événement:</strong> {{ $event->title }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</p>
        <p><strong>Lieu:</strong> {{ $event->location }}</p>
        <p><strong>Généré le:</strong> {{ now()->locale('fr')->translatedFormat('l d F Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $guestBooks->count() }}</div>
            <div class="stat-label">Total Messages</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guestBooks->where('status', 'approved')->count() }}</div>
            <div class="stat-label">Messages Approuvés</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guestBooks->where('status', 'pending')->count() }}</div>
            <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $guestBooks->pluck('guest_id')->unique()->count() }}</div>
            <div class="stat-label">Invités Uniques</div>
        </div>
    </div>
    
    <h2>Messages du Livre d'Or</h2>
    
    @if($guestBooks->count() > 0)
        @foreach($guestBooks as $book)
            <div class="message-card">
                <div class="message-header">
                    <div>
                        <div class="author">
                            {{ $book->guest->first_name ?? 'Anonyme' }} {{ $book->guest->last_name ?? '' }}
                        </div>
                        <div class="date">
                            {{ \Carbon\Carbon::parse($book->created_at)->locale('fr')->translatedFormat('d/m/Y à H:i') }}
                        </div>
                    </div>
                    <div>
                        @if($book->status == 'approved')
                            <span class="status status-approved">✓ Approuvé</span>
                        @elseif($book->status == 'pending')
                            <span class="status status-pending">⏳ En attente</span>
                        @else
                            <span class="status status-rejected">✗ Rejeté</span>
                        @endif
                    </div>
                </div>
                
                <div class="message-content">
                    {{ $book->message }}
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Aucun message dans le livre d'or pour cet événement.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Livre d'Or généré automatiquement le {{ now()->locale('fr')->translatedFormat('d/m/Y à H:i') }}</p>
        <p>Événement: {{ $event->title }} - {{ $event->location }}</p>
    </div>
</body>
</html>
