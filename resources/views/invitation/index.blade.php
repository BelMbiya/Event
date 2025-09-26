{{--
========================================
INVITATION INDEX VIEW - LISTE DES INVITATIONS
========================================

Cette vue affiche la liste complète des invitations avec :
- Filtres avancés (événement, statut, dates, invité)
- Tableau avec actions (voir, éditer, supprimer, dupliquer)
- Export PDF des invitations filtrées
- Statistiques et métriques d'engagement
- Gestion des statuts et publication

UTILISATION : Dashboard principal pour la gestion des invitations
--}}
@extends('admin')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des invitations</h1>
    <div>
        <button class="btn btn-success me-2" onclick="downloadFilteredPDF()">
            <i class="fas fa-download me-2"></i>
            Télécharger PDF
        </button>
        <button class="btn btn-info me-2" onclick="showDynamicLinksModal()">
            <i class="fas fa-link me-2"></i>
            Liens Dynamiques
        </button>
        <a href="{{ route("dashboard") }}" class="btn btn-primary">Créer une nouvelle invitation</a>
    </div>
</div>

<!-- ✅ RECHERCHE SIMPLIFIÉE -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search"></i> Recherche
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('invitation.index') }}" id="searchForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="search">Rechercher partout</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher par nom d'invité, événement, statut, etc...">
                        <small class="form-text text-muted">
                            Recherche dans les noms d'invités, événements, statuts et contenus
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="{{ route('invitation.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @if(request('search'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Recherche : "<strong>{{ request('search') }}</strong>" - 
                        {{ $invitations->count() }} invitation(s) trouvée(s)
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>


    <!-- Ici tu peux mettre tes cards, tables, etc. -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i>
                    Liste des invitations
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    @if(!request('event_id'))
                                    <th>Événement</th>
                                    @endif
                                    <th>Invité</th>
                                    <th>Code</th>
                                    <th>URL</th>
                                    <th>RSVP</th>
                                    <th>Envoyé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemple de ligne d'invitation -->
                                @foreach ($invitations as $invitation)
                                <tr data-event-id="{{ $invitation->event_id }}">
                                    @if(!request('event_id'))
                                    <td>
                                        @if($invitation->event)
                                            {{ $invitation->event->title }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        @if($invitation->guest)
                                            {{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}
                                        @else
                                            <span class="text-info">
                                                <i class="fas fa-users"></i> Invitation générale
                                                <br><small class="text-muted">(Liens dynamiques ci-dessous)</small>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->guest)
                                            <code>{{ $invitation->unique_code }}</code>
                                        @else
                                            <code>{{ $invitation->unique_code }}</code>
                                            <br><small class="text-muted">Générale</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->guest)
                                            <a href="{{ route('invitation.show.dynamic', $invitation->guest->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> Voir
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-info" onclick="showDynamicLinksForEvent({{ $invitation->event_id }})">
                                                <i class="fas fa-link"></i> Liens
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->guest && $invitation->guest->rsvp_status)
                                            <span class="badge 
                                                @if($invitation->guest->rsvp_status == 'confirmed') badge-success
                                                @elseif($invitation->guest->rsvp_status == 'declined') badge-danger
                                                @elseif($invitation->guest->rsvp_status == 'pending') badge-warning
                                                @else badge-secondary
                                                @endif">
                                                @if($invitation->guest->rsvp_status == 'confirmed')
                                                    <i class="fas fa-check"></i> Confirmé
                                                @elseif($invitation->guest->rsvp_status == 'declined')
                                                    <i class="fas fa-times"></i> Décliné
                                                @elseif($invitation->guest->rsvp_status == 'pending')
                                                    <i class="fas fa-clock"></i> En attente
                                                @else
                                                    {{ ucfirst($invitation->guest->rsvp_status) }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-question"></i> Non défini
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->sent_at)
                                            {{ \Carbon\Carbon::parse($invitation->sent_at)->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- <a href="{{ route('invitation.show', $invitation->unique_code) }}" class="btn btn-success btn-sm" title="Voir l'invitation">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invitation.content.edit', $invitation->id) }}" class="btn btn-warning btn-sm" title="Éditer le contenu">
                                                <i class="fas fa-palette"></i>
                                            </a>
                                            <a href="{{ route('content.create', $invitation->id) }}" class="btn btn-primary btn-sm" title="Éditer l'invitation">
                                                <i class="fas fa-edit"></i>
                                            </a> -->
                                            <a href="{{ route('invitation.download', $invitation->unique_code) }}" class="btn btn-info btn-sm" title="Télécharger PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteInvitation({{ $invitation->id }})" title="Supprimer l'invitation">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Formulaire de suppression caché -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- ✅ MODAL POUR LES LIENS DYNAMIQUES -->
<div class="modal fade" id="dynamicLinksModal" tabindex="-1" aria-labelledby="dynamicLinksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dynamicLinksModalLabel">
                    <i class="fas fa-link me-2"></i>Liens d'Invitation Dynamiques
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Invitations Dynamiques :</strong> Une seule invitation par événement qui s'adapte automatiquement selon chaque invité.
                </div>
                
                <div id="dynamicLinksContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2">Chargement des liens...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="copyAllLinks()">
                    <i class="fas fa-copy me-2"></i>Copier tous les liens
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function downloadFilteredPDF() {
    // Récupérer les paramètres de filtrage actuels
    const form = document.getElementById('searchForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    // Ajouter tous les paramètres de filtrage
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Construire l'URL de téléchargement avec les filtres
    const downloadUrl = `/invitations/download-all-pdf?${params.toString()}`;
    window.open(downloadUrl, '_blank');
}

function deleteInvitation(invitationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette invitation ? Cette action est irréversible.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/invitation/${invitationId}`;
        form.submit();
    }
}

// ✅ NOUVELLE FONCTION : Afficher les liens dynamiques
function showDynamicLinksModal() {
    const modal = new bootstrap.Modal(document.getElementById('dynamicLinksModal'));
    modal.show();
    
    // Charger les liens dynamiques
    loadDynamicLinks();
}

function loadDynamicLinks() {
    const content = document.getElementById('dynamicLinksContent');
    
    // Récupérer tous les événements uniques des invitations affichées
    const events = [...new Set(Array.from(document.querySelectorAll('[data-event-id]')).map(el => el.dataset.eventId))];
    
    if (events.length === 0) {
        content.innerHTML = '<div class="alert alert-warning">Aucun événement trouvé pour générer les liens.</div>';
        return;
    }
    
    let allLinks = '';
    
    events.forEach(eventId => {
        fetch(`/invitation/${eventId}/generate-links`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allLinks += generateEventLinksHTML(data);
                    content.innerHTML = allLinks;
                } else {
                    content.innerHTML = `<div class="alert alert-danger">Erreur: ${data.message}</div>`;
                }
            })
            .catch(error => {
                content.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement: ${error.message}</div>`;
            });
    });
}

function generateEventLinksHTML(data) {
    let html = `
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-calendar me-2"></i>${data.event.title}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
    `;
    
    data.links.forEach(link => {
        html += `
            <div class="col-md-6 mb-3">
                <div class="card border">
                    <div class="card-body p-3">
                        <h6 class="card-title">${link.guest_name}</h6>
                        <p class="card-text small text-muted">${link.guest_email}</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm" value="${link.dynamic_url}" readonly>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard('${link.dynamic_url}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <a href="${link.whatsapp_url}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp me-1"></i>WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
                </div>
            </div>
        </div>
    `;
    
    return html;
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Afficher une notification de succès
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Lien copié !
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(() => toast.remove(), 3000);
    });
}

function copyAllLinks() {
    const links = Array.from(document.querySelectorAll('input[readonly]')).map(input => input.value);
    const allLinksText = links.join('\n');
    
    navigator.clipboard.writeText(allLinksText).then(() => {
        alert('Tous les liens ont été copiés !');
    });
}

// ✅ NOUVELLE FONCTION : Afficher les liens dynamiques pour un événement spécifique
function showDynamicLinksForEvent(eventId) {
    const modal = new bootstrap.Modal(document.getElementById('dynamicLinksModal'));
    modal.show();
    
    // Charger les liens pour cet événement spécifique
    loadDynamicLinksForEvent(eventId);
}

// ✅ NOUVELLE FONCTION : Charger les liens dynamiques pour un événement spécifique
function loadDynamicLinksForEvent(eventId) {
    const content = document.getElementById('dynamicLinksContent');
    
    fetch(`/invitation/${eventId}/generate-links`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = generateEventLinksHTML(data);
            } else {
                content.innerHTML = `<div class="alert alert-danger">Erreur: ${data.message}</div>`;
            }
        })
        .catch(error => {
            content.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement: ${error.message}</div>`;
        });
}

</script>
@endsection