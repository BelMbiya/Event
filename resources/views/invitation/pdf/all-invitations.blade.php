<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Invitations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #e11d48, #f43f5e);
            color: white;
            border-radius: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
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
        .no-invitations {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Toutes les Invitations</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    @if($invitations->count() > 0)
        @foreach($invitations as $invitation)
            <div class="invitation-card">
                <div class="invitation-header">
                    <div class="invitation-title">
                        {{ $invitation->content->couple ?? 'Invitation sans titre' }}
                    </div>
                    <div class="invitation-status status-{{ $invitation->status }}">
                        {{ ucfirst($invitation->status) }}
                    </div>
                </div>

                <div class="invitation-details">
                    <div class="detail-group">
                        <h4>📅 Événement</h4>
                        <p><strong>Nom:</strong> {{ $invitation->event->name ?? 'Non défini' }}</p>
                        <p><strong>Date:</strong> {{ $invitation->content->event_datetime ? \Carbon\Carbon::parse($invitation->content->event_datetime)->format('d/m/Y à H:i') : 'Non définie' }}</p>
                        <p><strong>Lieu:</strong> {{ $invitation->content->venue_name ?? 'Non défini' }}</p>
                        <p><strong>Ville:</strong> {{ $invitation->content->venue_city ?? 'Non définie' }}</p>
                    </div>

                    <div class="detail-group">
                        <h4>🔗 Informations</h4>
                        <p><strong>Code unique:</strong> {{ $invitation->unique_code }}</p>
                        <p><strong>URL:</strong> {{ $invitation->invitation_url ?? 'Non générée' }}</p>
                        <p><strong>Créée le:</strong> {{ $invitation->created_at->format('d/m/Y à H:i') }}</p>
                        <p><strong>Statut contenu:</strong> {{ $invitation->content->status ?? 'Non défini' }}</p>
                    </div>
                </div>

                @if($invitation->guest)
                    <div class="guest-info">
                        <h5>👤 Invité concerné</h5>
                        <p><strong>Nom:</strong> {{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}</p>
                        <p><strong>Email:</strong> {{ $invitation->guest->email ?? 'Non fourni' }}</p>
                        <p><strong>Téléphone:</strong> {{ $invitation->guest->phone ?? 'Non fourni' }}</p>
                    </div>
                @else
                    <div class="guest-info">
                        <h5>👥 Invitation générale</h5>
                        <p>Cette invitation n'est pas assignée à un invité spécifique.</p>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="no-invitations">
            <h3>Aucune invitation trouvée</h3>
            <p>Il n'y a actuellement aucune invitation dans le système.</p>
        </div>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion d'événements</p>
        <p>Total: {{ $invitations->count() }} invitation(s)</p>
    </div>
</body>
</html>
