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
        .status-pending { color:#ff7f00; font-weight:bold; font-size:7px; }
        .status-confirmed { color:#10b981; font-weight:bold; font-size:7px; }
        .status-declined { color:#ef4444; font-weight:bold; font-size:7px; }
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
                <div style="margin-top:8px; font-weight:bold; font-size:16px; color:#1f2937; background:#f8fafc; padding:8px; border-radius:4px; border-left:4px solid #04647a">Liste de Tous les Invités</div>
            </td>
            <td style="width:50%; vertical-align:top; text-align:right; padding:0">
                <div class="h2">Total: {{ $guests->count() }} Invités</div>
                <div class="small">Généré le: {{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
                <div class="small">{{ $guests->pluck('event_id')->unique()->count() }} Événements</div>
            </td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $guests->count() }}</div>
            <div class="stat-label">Total Invités</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'confirmed')->count() }}</div>
            <div class="stat-label">Confirmés</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'pending')->count() }}</div>
            <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('rsvp_status', 'declined')->count() }}</div>
            <div class="stat-label">Déclinés</div>
        </div>
    </div>
    
    <div class="h2" style="margin-bottom:12px; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Liste des Invités</div>
    
    <table class="table">
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
                        @if($guest->email)<div>{{ $guest->email }}</div>@endif
                        @if($guest->phone)<div>{{ $guest->phone }}</div>@endif
                        @if(!$guest->email && !$guest->phone)<em>Aucun contact</em>@endif
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
