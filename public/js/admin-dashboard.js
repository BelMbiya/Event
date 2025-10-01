/* ============================================
   JavaScript pour admin/index.blade.php
   Tableau de bord administrateur
   ============================================ */

function refreshDashboard() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualisation...';
    btn.disabled = true;
    
    // Recharger la page pour actualiser les données
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Auto-submit pour le champ de recherche avec délai
let searchTimeout;
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.submit();
                }
            }, 1000);
        });
    }
});

// Fonction pour afficher le modal de chargement
function showCreationModal(eventTitle) {
    const modal = document.getElementById('creationModal');
    const content = document.getElementById('creationContent');
    
    if (modal && content) {
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Création en cours...</span>
                </div>
                <h5>Création d'invitations pour "${eventTitle}"</h5>
                <p class="text-muted">Veuillez patienter, nous créons les invitations pour tous les invités...</p>
            </div>
        `;
        $(modal).modal('show');
    }
}

// Fonction pour masquer le modal de chargement
function hideCreationModal() {
    const modal = document.getElementById('creationModal');
    if (modal) {
        $(modal).modal('hide');
    }
}

// Fonction pour afficher le résultat de la création
function showCreationResult(data, type) {
    hideCreationModal();
    
    const modal = document.getElementById('resultModal');
    const content = document.getElementById('resultContent');
    
    if (modal && content) {
        if (type === 'success') {
            content.innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Création réussie !</h5>
                    <p>${data.message || 'Les invitations ont été créées avec succès.'}</p>
                    ${data.count ? `<p><strong>Nombre d'invitations créées :</strong> ${data.count}</p>` : ''}
                </div>
                <div class="text-center">
                    <a href="/invitations" class="btn btn-primary">
                        <i class="fas fa-list"></i> Voir les Invitations
                    </a>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Erreur</h5>
                    <p>${data.message || 'Une erreur est survenue lors de la création des invitations.'}</p>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            `;
        }
        
        $(modal).modal('show');
    }
}

// Fonction pour créer des invitations pour un événement
function createInvitationsForEvent(eventId, eventTitle) {
    if (!confirm(`Créer des invitations pour tous les invités de "${eventTitle}" ?`)) {
        return;
    }
    
    showCreationModal(eventTitle);
    
    // Envoyer la requête
    fetch('/admin/create-invitations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            event_id: eventId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCreationResult(data, 'success');
        } else {
            showCreationResult(data, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCreationResult({message: 'Une erreur est survenue'}, 'error');
    });
}

// Fonction pour supprimer un événement avec confirmation
function deleteEvent(eventId, eventTitle) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'événement "${eventTitle}" ?\n\nCette action supprimera également toutes les invitations associées et ne peut pas être annulée.`)) {
        // Créer un formulaire temporaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/events/${eventId}`;
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Ajouter la méthode DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}

// Fonction pour exporter les données
function exportData(type) {
    const url = `/admin/export/${type}`;
    window.open(url, '_blank');
}

// Fonction pour afficher les statistiques détaillées
function showDetailedStats(eventId) {
    // Rediriger vers la page de statistiques détaillées
    window.location.href = `/admin/events/${eventId}/stats`;
}
