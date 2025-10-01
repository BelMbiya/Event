/**
 * JavaScript pour la gestion des invitations
 * Fonctionnalités : téléchargement PDF, suppression, liens dynamiques
 */

// ✅ FONCTION : Télécharger le PDF avec les filtres appliqués
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

// ✅ FONCTION : Supprimer une invitation
function deleteInvitation(invitationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette invitation ? Cette action est irréversible.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/invitation/${invitationId}`;
        form.submit();
    }
}

// ✅ FONCTION : Afficher les liens dynamiques
function showDynamicLinksModal() {
    const modal = new bootstrap.Modal(document.getElementById('dynamicLinksModal'));
    modal.show();
    
    // Charger les liens dynamiques
    loadDynamicLinks();
}

// ✅ FONCTION : Charger tous les liens dynamiques
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

// ✅ FONCTION : Générer le HTML pour les liens d'un événement
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

// ✅ FONCTION : Copier un texte dans le presse-papiers
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

// ✅ FONCTION : Copier tous les liens
function copyAllLinks() {
    const links = Array.from(document.querySelectorAll('input[readonly]')).map(input => input.value);
    const allLinksText = links.join('\n');
    
    navigator.clipboard.writeText(allLinksText).then(() => {
        alert('Tous les liens ont été copiés !');
    });
}

// ✅ FONCTION : Afficher les liens dynamiques pour un événement spécifique
function showDynamicLinksForEvent(eventId) {
    const modal = new bootstrap.Modal(document.getElementById('dynamicLinksModal'));
    modal.show();
    
    // Charger les liens pour cet événement spécifique
    loadDynamicLinksForEvent(eventId);
}

// ✅ FONCTION : Charger les liens dynamiques pour un événement spécifique
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

// ✅ INITIALISATION : Quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap si disponibles
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialiser les toasts Bootstrap si disponibles
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl);
        });
    }
});
