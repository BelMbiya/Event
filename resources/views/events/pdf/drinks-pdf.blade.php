<html>
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color:#1f2937; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; }
        .small { font-size: 8px; color:#374151; }
        .h1 { font-size: 20px; font-weight: bold; }
        .h2 { font-size: 12px; font-weight: bold; }
        .table { width:100%; border-collapse:collapse; font-size:8px; }
        .table thead th { background:#04647a; color:#fff; padding:6px; font-weight:bold; }
        .table tbody td { border-bottom:1px solid #e5e7eb; padding:6px; font-size:8px; }
        .right { text-align:right; }
        .grid { display:flex; gap:0; justify-content:space-between; }
        .grid .col { width:49%; }
        .stats-grid { display:flex; gap:10px; margin-bottom:20px; }
        .stat-card { background:#f8fafc; padding:8px; border-radius:4px; border-left:4px solid #04647a; flex:1; text-align:center; }
        .stat-number { font-size:14px; font-weight:bold; color:#ff7f00; }
        .stat-label { font-size:7px; color:#6b7280; margin-top:2px; }
        .guest-card { margin-bottom:15px; padding:10px; border:1px solid #e5e7eb; border-radius:4px; background:#f8fafc; }
        .footer { position:fixed; left:0; right:0; bottom:10px; font-size:8px; color:#6b7280; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $logoFile = public_path('img/logotoninvite.png');
        $logoFile = str_replace('\\', '/', $logoFile);
        $logoData = null;
        if (is_file($logoFile)) {
            $logoMime = @mime_content_type($logoFile) ?: 'image/png';
            $logoData = 'data:'.$logoMime.';base64,'.base64_encode(@file_get_contents($logoFile));
        }
    @endphp
    
    <table style="width:100%; border-collapse:collapse; margin-bottom:16px">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0">
                @if($logoData)
                    <img src="{{ $logoData }}" style="height:56px" />
                @endif
                <div style="margin-top:8px; font-weight:bold; font-size:16px; color:#1f2937; background:#f8fafc; padding:8px; border-radius:4px; border-left:4px solid #04647a">Choix de Boissons</div>
            </td>
            <td style="width:50%; vertical-align:top; text-align:right; padding:0">
                <div class="h2">{{ $event->title }}</div>
                <div class="small">{{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('d/m/Y') }} à {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }}</div>
                <div class="small">Lieu: {{ $event->location ?? 'Non défini' }}</div>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-bottom:14px">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0">
                <div class="small" style="margin-bottom:6px">Événement: {{ $event->title }}</div>
                <div class="small" style="margin-bottom:6px">Date: {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</div>
                <div class="small">Lieu: {{ $event->location ?? 'Non défini' }}</div>
            </td>
            <td style="width:50%; vertical-align:top; text-align:center; padding:0">
                <div class="small" style="margin-bottom:6px"><strong>Informations</strong></div>
                <div class="small">{{ $eventDrinks->count() }} Boissons disponibles</div>
                <div class="small">{{ $drinkChoices->count() }} Invités avec choix</div>
            </td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $eventDrinks->count() }}</div>
            <div class="stat-label">Boissons Disponibles</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $drinkChoices->count() }}</div>
            <div class="stat-label">Invités avec Choix</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $eventDrinks->where('drink.alcoholic', false)->count() }}</div>
            <div class="stat-label">Non-Alcool</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $eventDrinks->where('drink.alcoholic', true)->count() }}</div>
            <div class="stat-label">Avec Alcool</div>
        </div>
    </div>
    
    <div class="h2" style="margin-bottom:12px; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Boissons Disponibles</div>
    
    <table class="table">
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
                            <span style="color: #dc3545; font-size:7px;">Oui</span>
                        @else
                            <span style="color: #28a745; font-size:7px;">Non</span>
                        @endif
                    </td>
                    <td>{{ $eventDrink->drink->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($drinkChoices->count() > 0)
        <div class="page-break"></div>
        
        <div class="h2" style="margin-bottom:12px; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Choix des Invités</div>
        
        @foreach($drinkChoices as $guestId => $choices)
            @php
                $guest = $choices->first()->guest;
            @endphp
            
            <div class="guest-card">
                <div class="h2" style="color:#04647a; margin-bottom:8px; font-size:10px;">
                    {{ $guest->first_name }} {{ $guest->last_name }}
                </div>
                
                @if($guest->email)
                    <div class="small" style="margin-bottom:8px;"><strong>Email:</strong> {{ $guest->email }}</div>
                @endif
                
                <div class="h2" style="margin-bottom:8px; font-size:9px; color:#374151;">Choix de boissons:</div>
                <table class="table">
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
        <div style="text-align:center; padding:20px; color:#6b7280; background:#f9fafb; border-radius:4px; margin:20px 0;">
            <p>Aucun choix de boisson enregistré pour le moment.</p>
        </div>
    @endif
    
    <div class="footer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <div>Rapport généré automatiquement</div>
            <div>{{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
        </div>
        <div style="text-align:center; font-size:7px;">
            Système de gestion d'événements - {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
