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
        .stat-number { font-size:14px; font-weight:bold; color:#04647a; }
        .stat-label { font-size:7px; color:#6b7280; margin-top:2px; }
        .message-card { border:1px solid #e5e7eb; border-radius:4px; padding:8px; margin-bottom:10px; background:#fff; }
        .message-header { border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:6px; }
        .author { font-weight:bold; color:#1f2937; font-size:9px; }
        .date { color:#6b7280; font-size:7px; }
        .message-content { color:#374151; font-size:8px; line-height:1.4; }
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
                <div style="margin-top:8px; font-weight:bold; font-size:16px; color:#1f2937; background:#f8fafc; padding:8px; border-radius:4px; border-left:4px solid #04647a">Livre d'Or</div>
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
                <div class="small">{{ $guestBooks->count() }} Messages</div>
                <div class="small">{{ $guestBooks->pluck('guest_id')->unique()->count() }} Auteurs uniques</div>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-bottom:14px">
        <tr>
            <td style="width:25%; vertical-align:top; padding:0">
                <div style="border:1px solid #078ba0; border-radius:4px; padding:6px; background:#f0f9ff; text-align:center;">
                    <div style="font-size:16px; font-weight:bold; color:#ff7f00; margin-bottom:4px;">{{ $guestBooks->count() }}</div>
                    <div style="font-size:7px; color:#374151; font-weight:bold;">Total Messages</div>
                </div>
            </td>
            <td style="width:25%; vertical-align:top; padding:0">
                <div style="border:1px solid #078ba0; border-radius:4px; padding:6px; background:#f0f9ff; text-align:center;">
                    <div style="font-size:16px; font-weight:bold; color:#ff7f00; margin-bottom:4px;">{{ $guestBooks->where('visibility', 'public')->count() }}</div>
                    <div style="font-size:7px; color:#374151; font-weight:bold;">Messages Publics</div>
                </div>
            </td>
            <td style="width:25%; vertical-align:top; padding:0">
                <div style="border:1px solid #078ba0; border-radius:4px; padding:6px; background:#f0f9ff; text-align:center;">
                    <div style="font-size:16px; font-weight:bold; color:#ff7f00; margin-bottom:4px;">{{ $guestBooks->where('visibility', 'private')->count() }}</div>
                    <div style="font-size:7px; color:#374151; font-weight:bold;">Messages Privés</div>
                </div>
            </td>
            <td style="width:25%; vertical-align:top; padding:0">
                <div style="border:1px solid #078ba0; border-radius:4px; padding:6px; background:#f0f9ff; text-align:center;">
                    <div style="font-size:16px; font-weight:bold; color:#ff7f00; margin-bottom:4px;">{{ $guestBooks->pluck('guest_id')->unique()->count() }}</div>
                    <div style="font-size:7px; color:#374151; font-weight:bold;">Auteurs Uniques</div>
                </div>
            </td>
        </tr>
    </table>
    
    <div class="h2" style="margin-bottom:12px; color:#04647a; border-bottom:2px solid #04647a; padding-bottom:4px">Messages du Livre d'Or</div>
    
    @if($guestBooks->count() > 0)
        @foreach($guestBooks as $book)
            <div class="message-card">
                <div class="message-header">
                    <div class="author">{{ $book->first_name ?? 'Anonyme' }} {{ $book->last_name ?? '' }}</div>
                    <div class="date">{{ \Carbon\Carbon::parse($book->created_at)->locale('fr')->translatedFormat('d/m/Y à H:i') }}</div>
                </div>
                <div class="message-content">{{ $book->message }}</div>
            </div>
        @endforeach
    @else
        <div style="text-align:center; padding:20px; color:#6b7280; background:#f9fafb; border-radius:4px; margin:20px 0;">
            <p>Aucun message dans le livre d'or pour cet événement.</p>
        </div>
    @endif
    
    <div class="footer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            </div>
        </div>
        <div style="text-align:center; font-size:7px;">
            {{ $event->title }} - {{ $event->location }}
            <div>Livre d'Or généré automatiquement</div>
            <div>{{ now()->locale('fr')->translatedFormat('d/m/Y H:i') }}</div>
            Toninvite|copyright © {{ now()->year }} | Tous droits réservés
        </div>
    </div>
</body>
</html>
