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
        .info-section { margin-bottom:15px; padding:10px; border:1px solid #e5e7eb; border-radius:4px; background:#f8fafc; }
        .info-section h3 { color:#ff7f00; margin-bottom:8px; font-size:12px; }
        .info-grid { display:flex; gap:20px; }
        .info-grid .col { flex:1; }
        .info-item { margin-bottom:6px; }
        .info-label { font-weight:bold; color:#374151; font-size:8px; }
        .info-value { color:#1f2937; font-size:8px; }
        .status-badge { padding:2px 6px; border-radius:2px; font-size:7px; font-weight:bold; }
        .status-confirmed { background:#d1fae5; color:#065f46; }
        .status-pending { background:#fff7ed; color:#ea580c; }
        .status-declined { background:#fee2e2; color:#991b1b; }
        .drink-badge { background:#078ba0; color:#fff; padding:2px 4px; border-radius:2px; font-size:6px; margin:1px; display:inline-block; }
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
                <div style="margin-top:8px; font-weight:bold; font-size:16px; color:#1f2937; background:#f8fafc; padding:8px; border-radius:4px; border-left:4px solid #04647a">Invitation Personnalisée</div>
            </td>
            <td style="width:50%; vertical-align:top; text-align:right; padding:0">
                <div class="h2">{{ $guest->first_name }} {{ $guest->last_name }}</div>
                <div class="small">Type: {{ $guest->guestType->name ?? 'Invité' }}</div>
                <div class="small">Généré le: {{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
    
    <div class="info-section">
        <h3>Informations Personnelles</h3>
        <div class="info-grid">
            <div class="col">
                <div class="info-item">
                    <div class="info-label">Nom complet:</div>
                    <div class="info-value">{{ $guest->first_name }} {{ $guest->last_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $guest->email ?? 'Non renseigné' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Choix de repas:</div>
                    <div class="info-value">{{ $guest->meal_choice ?? 'Non spécifié' }}</div>
                </div>
            </div>
            <div class="col">
                <div class="info-item">
                    <div class="info-label">Téléphone:</div>
                    <div class="info-value">{{ $guest->phone ?? 'Non renseigné' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Statut RSVP:</div>
                    <div class="info-value">
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
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">ID Invité:</div>
                    <div class="info-value">{{ $guest->id }}</div>
                </div>
            </div>
        </div>
    </div>
    
    @if($guest->event)
    <div class="info-section">
        <h3>Informations de l'Événement</h3>
        <div class="info-grid">
            <div class="col">
                <div class="info-item">
                    <div class="info-label">Titre de l'événement:</div>
                    <div class="info-value">{{ $guest->event->title }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($guest->event->event_date)->locale('fr')->translatedFormat('l d F Y') }}</div>
                </div>
            </div>
            <div class="col">
                <div class="info-item">
                    <div class="info-label">Lieu:</div>
                    <div class="info-value">{{ $guest->event->location ?? 'Non défini' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Description:</div>
                    <div class="info-value">{{ $guest->event->description ?? 'Non disponible' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($guest->drinkChoices && $guest->drinkChoices->count() > 0)
    <div class="info-section">
        <h3>Choix de Boissons</h3>
        <div>
            @foreach($guest->drinkChoices as $choice)
                <span class="drink-badge">{{ $choice->eventDrink->drink->name }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="footer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <div>Invitation générée automatiquement</div>
            <div>{{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
        </div>
        <div style="text-align:center; font-size:7px;">
            Système de gestion d'événements - {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
