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
        .drink-badge { background:#078ba0; color:#fff; padding:2px 4px; border-radius:2px; font-size:6px; margin:1px; display:inline-block; }
        .status-completed { color:#10b981; font-weight:bold; font-size:7px; }
        .status-pending { color:#ff7f00; font-weight:bold; font-size:7px; }
        .footer { position:fixed; left:0; right:0; bottom:10px; font-size:8px; color:#6b7280; }
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
                <div class="small">Généré le: {{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-bottom:14px">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0">
                <div class="small" style="margin-bottom:6px">Événement: {{ $event->title }}</div>
                <div class="small" style="margin-bottom:6px">Lieu: {{ $event->location }}</div>
                <div class="small">Date: {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</div>
            </td>
            <td style="width:50%; vertical-align:top; text-align:center; padding:0">
                <div class="small" style="margin-bottom:6px"><strong>Informations</strong></div>
                <div class="small">{{ $guests->count() }} Invités</div>
                <div class="small">{{ $eventDrinks->count() }} Boissons disponibles</div>
            </td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $guests->count() }}</div>
            <div class="stat-label">Total Invités</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('has_drink_choices', true)->count() }}</div>
            <div class="stat-label">Choix Effectués</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('has_drink_choices', false)->count() }}</div>
            <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $eventDrinks->count() }}</div>
            <div class="stat-label">Boissons Disponibles</div>
        </div>
    </div>
    
    <div class="h2" style="margin-bottom:12px; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Liste des Invités et leurs Choix</div>
    
    <table class="table">
        <thead>
            <tr>
                <th style="width: 20%;">Nom</th>
                <th style="width: 15%;">Contact</th>
                <th style="width: 35%;">Choix de Boissons</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 20%;">Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td><strong>{{ $guest->first_name }} {{ $guest->last_name }}</strong></td>
                    <td>
                        @if($guest->email)<div>{{ $guest->email }}</div>@endif
                        @if($guest->phone)<div>{{ $guest->phone }}</div>@endif
                    </td>
                    <td>
                        @if($guest->drinkChoices->count() > 0)
                            @foreach($guest->drinkChoices as $choice)
                                <span class="drink-badge">
                                    {{ $choice->eventDrink->drink->name }}
                                    @if($choice->eventDrink->price)({{ $choice->eventDrink->price }}€)@endif
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
                    <td>{{ $guest->guestType->name ?? 'Invité' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="h2" style="margin:20px 0 12px 0; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Résumé des Choix</div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $guests->sum(function($guest) { return $guest->drinkChoices->count(); }) }}</div>
            <div class="stat-label">Total des Choix</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ number_format($guests->sum(function($guest) { 
                return $guest->drinkChoices->sum(function($choice) { 
                    return $choice->eventDrink->price ?? 0; 
                }); 
            }), 0) }}€</div>
            <div class="stat-label">Prix Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('has_drink_choices', true)->count() }}</div>
            <div class="stat-label">Invités avec Choix</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('has_drink_choices', false)->count() }}</div>
            <div class="stat-label">Invités sans Choix</div>
        </div>
    </div>
    
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
