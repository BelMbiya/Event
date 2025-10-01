/* ============================================
   JavaScript pour invitation/create-advanced.blade.php
   Gestion du drag & drop, prévisualisation d'images et formulaires
   ============================================ */

// Initialiser Summernote
$(document).ready(function() {
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Rédigez votre message d\'invitation...',
        lang: 'fr-FR'
    });
});

// ========================================
// GESTION DES IMAGES AVEC DRAG & DROP ET PRÉVISUALISATION
// ========================================

// Initialiser tous les zones de drag & drop
function initializeDragDrop() {
    console.log('🖱️ Initialisation du drag & drop...');
    
    document.querySelectorAll('.drag-drop-zone').forEach((zone, index) => {
        const input = zone.querySelector('input[type="file"]');
        const target = zone.dataset.target;
        const isMultiple = zone.dataset.multiple === 'true';
        
        console.log(`Zone ${index + 1}: ${target} (multiple: ${isMultiple})`);
        
        // Gestion du clic pour ouvrir le sélecteur de fichiers
        zone.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log(`Clic sur zone: ${target}`);
            
            // Vérifier si on clique sur le bouton de suppression
            if (e.target.closest('.remove-image')) {
                console.log('Clic sur bouton de suppression, ignoré');
                return;
            }
            
            // Ouvrir le sélecteur de fichiers
            console.log(`Ouverture du sélecteur de fichiers pour: ${target}`);
            input.click();
        });
        
        // Gestion du drag & drop - dragover
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
            console.log(`Drag over: ${target}`);
        });
        
        // Gestion du drag & drop - dragenter
        zone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
            console.log(`Drag enter: ${target}`);
        });
        
        // Gestion du drag & drop - dragleave
        zone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            // Vérifier si on quitte vraiment la zone
            if (!zone.contains(e.relatedTarget)) {
                zone.classList.remove('drag-over');
                console.log(`Drag leave: ${target}`);
            }
        });
        
        // Gestion du drag & drop - drop
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('drag-over');
            
            console.log(`Drop sur: ${target}`);
            const files = e.dataTransfer.files;
            console.log(`Fichiers reçus: ${files.length}`);
            
            if (files.length > 0) {
                // Vérifier que ce sont des images
                const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
                console.log(`Images valides: ${imageFiles.length}`);
                
                if (imageFiles.length > 0) {
                    if (isMultiple) {
                        // Créer un DataTransfer pour les fichiers multiples
                        const dt = new DataTransfer();
                        imageFiles.forEach(file => dt.items.add(file));
                        input.files = dt.files;
                    } else {
                        // Prendre seulement le premier fichier
                        const dt = new DataTransfer();
                        dt.items.add(imageFiles[0]);
                        input.files = dt.files;
                    }
                    
                    console.log(`Fichiers assignés à l'input: ${input.files.length}`);
                    handleFileSelect(input, target);
                } else {
                    alert('Veuillez glisser uniquement des fichiers images.');
                }
            }
        });
        
        // Gestion de la sélection de fichier classique
        input.addEventListener('change', (e) => {
            console.log(`Sélection classique: ${target}, fichiers: ${e.target.files.length}`);
            handleFileSelect(input, target);
        });
    });
    
    // Gestion des boutons de suppression
    document.querySelectorAll('.remove-image').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const target = btn.dataset.target;
            console.log(`Suppression: ${target}`);
            removeImage(target);
        });
    });
    
    console.log('✅ Drag & drop initialisé');
}

// Gérer la sélection de fichier
function handleFileSelect(input, target) {
    const files = input.files;
    handleSingleImagePreview(files[0], target);
}

// Prévisualiser une image unique
function handleSingleImagePreview(file, target) {
    console.log(`Prévisualisation: ${target}, fichier: ${file ? file.name : 'null'}`);
    
    if (!file) {
        console.log('❌ Aucun fichier à prévisualiser');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        console.log(`Image chargée pour ${target}`);
        
        const zone = document.querySelector(`[data-target="${target}"]`);
        if (!zone) {
            console.error(`❌ Zone non trouvée: ${target}`);
            return;
        }
        
        const preview = zone.querySelector('.image-preview');
        const previewImg = preview.querySelector('img');
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && previewImg && content) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            content.style.display = 'none';
            console.log(`✅ Prévisualisation affichée pour ${target}`);
        } else {
            console.error(`❌ Éléments de prévisualisation non trouvés pour ${target}`);
        }
    };
    
    reader.onerror = function() {
        console.error(`❌ Erreur lors du chargement de l'image pour ${target}`);
    };
    
    reader.readAsDataURL(file);
}

// Prévisualiser plusieurs images (galerie)
function handleGalleryPreview(files) {
    const preview = document.getElementById('gallery_preview');
    const previewImages = document.getElementById('gallery_preview_images');
    
    previewImages.innerHTML = '';
    
    if (files.length > 0) {
        preview.style.display = 'block';
        
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" alt="Aperçu ${index + 1}" class="img-thumbnail gallery-thumbnail">
                        <small class="text-muted d-block text-center mt-1">Image ${index + 1}</small>
                    </div>
                `;
                previewImages.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.style.display = 'none';
    }
}

// Supprimer une image
function removeImage(target) {
    const zone = document.querySelector(`[data-target="${target}"]`);
    const input = zone.querySelector('input[type="file"]');
    const preview = zone.querySelector('.image-preview');
    const content = zone.querySelector('.drag-drop-content');
    
    input.value = '';
    preview.style.display = 'none';
    content.style.display = 'block';
}

// ========================================
// GESTION DU FORMULAIRE AVEC AJAX
// ========================================

// Gestion du formulaire - Validation côté serveur uniquement
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOM Ready - Recherche du formulaire...');
    
    const form = document.getElementById('invitationForm');
    if (form) {
        console.log('✅ Formulaire trouvé:', form);
        console.log('📋 Action du formulaire:', form.action);
        
        // Supprimer tous les gestionnaires d'événements existants pour éviter les doublons
        $(form).off('submit');
        
        // Attacher le gestionnaire d'événement
        $(form).on('submit', function(e) {
            console.log('🚀 Soumission du formulaire détectée');
            console.log('📋 Action du formulaire:', form.action);
            console.log('📋 Méthode du formulaire:', form.method);
            
            const submitBtn = $(form).find('button[type="submit"]');
            if (submitBtn.length > 0) {
                const originalText = submitBtn.html();
                console.log('📝 Texte original du bouton:', originalText);
                
                // Désactiver le bouton et afficher le loading
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Création en cours...');
                
                console.log('⏳ Spinner activé, soumission au serveur...');
                console.log('🔄 Le formulaire va être soumis normalement au serveur');
                
                // Sécurité : réactiver le bouton après 30 secondes
                setTimeout(() => {
                    if (submitBtn.prop('disabled')) {
                        console.warn('⚠️ Timeout - Réactivation du bouton après 30s');
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                }, 30000);
            } else {
                console.error('❌ Bouton de soumission non trouvé !');
            }
            
            // Ne pas prévenir la soumission - laisser le serveur gérer
            // Pas de e.preventDefault()
            console.log('✅ Soumission autorisée - pas de preventDefault()');
        });
        
        // Test alternatif : gestionnaire sur le bouton directement
        const submitBtn = $(form).find('button[type="submit"]');
        if (submitBtn.length > 0) {
            console.log('🔘 Gestionnaire alternatif sur le bouton attaché');
            submitBtn.off('click').on('click', function(e) {
                console.log('🖱️ Clic direct sur le bouton détecté');
                // Laisser le clic se propager normalement pour soumettre le formulaire
            });
        }
    } else {
        console.error('❌ Formulaire avec ID "invitationForm" non trouvé !');
    }
});

// Fonction showCreationResult supprimée - Plus nécessaire avec la validation côté serveur

// Fonction pour créer automatiquement des invitations pour tous les invités
function createInvitationsForAllGuests(eventId, eventTitle) {
    if (!confirm(`🔍 Vérifier et créer les invitations manquantes pour l'événement "${eventTitle}" ?\n\n✅ Créera seulement pour les invités qui n'ont pas encore d'invitation\nℹ️ Ignorera ceux qui en ont déjà une`)) {
        return;
    }
    
    // Afficher le modal de chargement
    showAutoCreationModal(eventTitle);
    
    // Envoyer la requête AJAX
    fetch(`/events/${eventId}/create-invitations-for-all-guests`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAutoCreationResult(data, 'success');
        } else {
            showAutoCreationResult(data, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAutoCreationResult({
            message: 'Une erreur inattendue s\'est produite. Veuillez réessayer.'
        }, 'error');
    });
}

// Fonction pour afficher le modal de chargement pour la création automatique
function showAutoCreationModal(eventTitle) {
    const modal = document.getElementById('autoCreationModal');
    const content = document.getElementById('autoCreationContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <h5>Création automatique des invitations en cours...</h5>
            <p class="text-muted">Vérification et création des invitations pour l'événement : <strong>${eventTitle}</strong></p>
        </div>
    `;
    
    $('#autoCreationModal').modal('show');
}

// Fonction pour afficher le résultat de la création automatique
function showAutoCreationResult(data, type) {
    const modal = document.getElementById('autoCreationModal');
    const content = document.getElementById('autoCreationContent');
    const title = document.querySelector('#autoCreationModalLabel');
    
    if (type === 'success') {
        title.innerHTML = '<i class="fas fa-check-circle text-success"></i> Création Automatique Réussie !';
        title.className = 'modal-title text-success';
        
        let resultHtml = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle me-2"></i>Opération terminée avec succès !</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
        
        if (data.count > 0) {
            resultHtml += `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Détails de la création :</h6>
                    <ul class="mb-0">
                        <li><strong>${data.count}</strong> nouvelle(s) invitation(s) créée(s)</li>
                        <li><strong>${data.existing_count}</strong> invitation(s) existante(s) (ignorée(s))</li>
                    </ul>
                </div>
            `;
        }
        
        content.innerHTML = resultHtml;
    } else {
        title.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Erreur';
        title.className = 'modal-title text-danger';
        
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la création automatique</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
    }
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDrop();
});
