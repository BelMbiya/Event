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
        
        console.log(`\n📦 Zone ${index + 1}: ${target} (multiple: ${isMultiple})`);
        
        // Vérifier que l'input existe
        if (!input) {
            console.error(`❌ Input non trouvé pour la zone: ${target}`);
            return;
        }
        
        // SOLUTION 1: Clic sur toute la zone (sauf boutons)
        zone.addEventListener('click', function(e) {
            // Ignorer si clic sur bouton de suppression
            if (e.target.closest('.remove-image')) {
                console.log('⏭️ Clic sur bouton suppression ignoré');
                return;
            }
            
            // Ignorer si clic sur bouton de sélection (il a son propre handler)
            if (e.target.closest('.file-select-btn')) {
                console.log('⏭️ Clic sur bouton sélection (handler séparé)');
                return;
            }
            
            console.log(`🖱️ Clic sur zone détecté pour: ${target}`);
            console.log(`📂 Ouverture du sélecteur de fichiers...`);
            input.click();
        });
        
        // SOLUTION 2: Clic sur le texte "cliquez pour sélectionner"
        const clickText = zone.querySelector('.click-to-upload');
        if (clickText) {
            clickText.style.cursor = 'pointer';
            clickText.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log(`📝 Clic sur texte cliquable: ${target}`);
                input.click();
            });
        }
        
        // SOLUTION 3: Bouton "Parcourir les fichiers"
        const fileBtn = zone.querySelector('.file-select-btn');
        if (fileBtn) {
            fileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log(`📁 Clic sur bouton parcourir: ${target}`);
                input.click();
            });
        }
        
        // Drag & Drop - Dragover
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
        });
        
        // Drag & Drop - Dragenter
        zone.addEventListener('dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
        });
        
        // Drag & Drop - Dragleave
        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!zone.contains(e.relatedTarget)) {
                zone.classList.remove('drag-over');
            }
        });
        
        // Drag & Drop - Drop
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('drag-over');
            
            console.log(`📥 Drop détecté sur: ${target}`);
            const files = e.dataTransfer.files;
            
            if (files.length > 0) {
                const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
                
                if (imageFiles.length === 0) {
                    alert('❌ Veuillez glisser uniquement des fichiers images (JPEG, PNG, JPG, GIF, WebP).');
                    return;
                }
                
                // Vérifier la taille (2MB max)
                const oversized = imageFiles.filter(f => f.size > 2 * 1024 * 1024);
                if (oversized.length > 0) {
                    alert(`❌ Fichier(s) trop volumineux (max 2MB):\n${oversized.map(f => f.name).join('\n')}`);
                    return;
                }
                
                // Assigner les fichiers à l'input
                const dt = new DataTransfer();
                if (isMultiple) {
                    imageFiles.forEach(f => dt.items.add(f));
                } else {
                    dt.items.add(imageFiles[0]);
                }
                input.files = dt.files;
                
                // Déclencher la prévisualisation
                handleFileSelect(input, target);
            }
        });
        
        // Changement de fichier (sélection classique)
        input.addEventListener('change', function(e) {
            console.log(`📂 Fichier sélectionné pour ${target}: ${e.target.files.length} fichier(s)`);
            
            const files = Array.from(e.target.files);
            const validFiles = files.filter(f => f.type.startsWith('image/'));
            
            if (validFiles.length !== files.length) {
                alert('❌ Seuls les fichiers images sont autorisés (JPEG, PNG, JPG, GIF, WebP).');
                e.target.value = '';
                return;
            }
            
            // Vérifier la taille
            const oversized = validFiles.filter(f => f.size > 2 * 1024 * 1024);
            if (oversized.length > 0) {
                alert(`❌ Fichier(s) trop volumineux (max 2MB):\n${oversized.map(f => f.name).join('\n')}`);
                e.target.value = '';
                return;
            }
            
            handleFileSelect(input, target);
        });
        
        console.log(`  ✅ Zone configurée avec succès`);
    });
    
    // Boutons de suppression
    document.querySelectorAll('.remove-image').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const target = btn.dataset.target;
            console.log(`🗑️ Suppression pour: ${target}`);
            removeImage(target);
        });
    });
    
    console.log('\n✅ Drag & drop initialisé avec succès!\n');
}

// Gérer la sélection de fichier
function handleFileSelect(input, target) {
    const files = input.files;
    if (files.length > 0) {
        console.log(`🖼️ Prévisualisation pour ${target}: ${files[0].name}`);
        handleSingleImagePreview(files[0], target);
    }
}

// Prévisualiser une image unique
function handleSingleImagePreview(file, target) {
    if (!file) {
        console.log('❌ Aucun fichier à prévisualiser');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const zone = document.querySelector(`[data-target="${target}"]`);
        if (!zone) {
            console.error(`❌ Zone non trouvée: ${target}`);
            return;
        }
        
        const preview = zone.querySelector('.image-preview');
        const previewImg = preview ? preview.querySelector('img') : null;
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && previewImg && content) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            content.style.display = 'none';
            console.log(`✅ Prévisualisation affichée pour ${target}`);
        } else {
            console.error(`❌ Éléments de prévisualisation manquants pour ${target}`);
        }
    };
    
    reader.onerror = function() {
        console.error(`❌ Erreur lors du chargement de l'image pour ${target}`);
    };
    
    reader.readAsDataURL(file);
}

// Supprimer une image
function removeImage(target) {
    const zone = document.querySelector(`[data-target="${target}"]`);
    if (!zone) {
        console.error(`❌ Zone non trouvée: ${target}`);
        return;
    }
    
    const input = zone.querySelector('input[type="file"]');
    const preview = zone.querySelector('.image-preview');
    const content = zone.querySelector('.drag-drop-content');
    
    if (input) {
        input.value = '';
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
    
    if (content) {
        content.style.display = 'block';
    }
    
    console.log(`✅ Image supprimée pour: ${target}`);
}

// ========================================
// GESTION DU FORMULAIRE
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOM Ready - Recherche du formulaire...');
    
    const form = document.getElementById('invitationForm');
    if (form) {
        console.log('✅ Formulaire trouvé');
        
        $(form).off('submit').on('submit', function(e) {
            console.log('🚀 Soumission du formulaire');
            
            const submitBtn = $(form).find('button[type="submit"]');
            if (submitBtn.length > 0) {
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Création en cours...');
                
                setTimeout(() => {
                    if (submitBtn.prop('disabled')) {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                }, 30000);
            }
        });
    }
});

// Fonction pour créer automatiquement des invitations
function createInvitationsForAllGuests(eventId, eventTitle) {
    if (!confirm(`🔍 Vérifier et créer les invitations manquantes pour "${eventTitle}" ?\n\n✅ Créera seulement pour les invités sans invitation\nℹ️ Ignorera ceux qui en ont déjà une`)) {
        return;
    }
    
    showAutoCreationModal(eventTitle);
    
    fetch(`/events/${eventId}/create-invitations-for-all-guests`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showAutoCreationResult(data, data.success ? 'success' : 'error');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAutoCreationResult({
            message: 'Une erreur inattendue s\'est produite.'
        }, 'error');
    });
}

function showAutoCreationModal(eventTitle) {
    const content = document.getElementById('autoCreationContent');
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <h5>Création automatique en cours...</h5>
            <p class="text-muted">Événement : <strong>${eventTitle}</strong></p>
        </div>
    `;
    $('#autoCreationModal').modal('show');
}

function showAutoCreationResult(data, type) {
    const content = document.getElementById('autoCreationContent');
    const title = document.querySelector('#autoCreationModalLabel');
    
    if (type === 'success') {
        title.innerHTML = '<i class="fas fa-check-circle text-success"></i> Succès !';
        title.className = 'modal-title text-success';
        
        let html = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle me-2"></i>Opération terminée !</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
        
        if (data.count > 0) {
            html += `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Détails :</h6>
                    <ul class="mb-0">
                        <li><strong>${data.count}</strong> invitation(s) créée(s)</li>
                        <li><strong>${data.existing_count}</strong> existante(s) (ignorée(s))</li>
                    </ul>
                </div>
            `;
        }
        content.innerHTML = html;
    } else {
        title.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Erreur';
        title.className = 'modal-title text-danger';
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Erreur</h6>
                <p class="mb-0">${data.message}</p>
            </div>
        `;
    }
}

// Fonction de débogage
function debugDragDropZones() {
    console.log('\n🔍 === DÉBOGAGE DES ZONES ===');
    const zones = document.querySelectorAll('.drag-drop-zone');
    console.log(`📊 Nombre de zones: ${zones.length}\n`);
    
    zones.forEach((zone, i) => {
        const target = zone.dataset.target;
        const input = zone.querySelector('input[type="file"]');
        const clickText = zone.querySelector('.click-to-upload');
        const fileBtn = zone.querySelector('.file-select-btn');
        const removeBtn = zone.querySelector('.remove-image');
        
        console.log(`Zone ${i + 1}: ${target}`);
        console.log(`  Input: ${input ? '✅' : '❌'}`);
        console.log(`  Texte cliquable: ${clickText ? '✅' : '❌'}`);
        console.log(`  Bouton parcourir: ${fileBtn ? '✅' : '❌'}`);
        console.log(`  Bouton suppression: ${removeBtn ? '✅' : '❌'}\n`);
    });
    console.log('=== FIN DU DÉBOGAGE ===\n');
}

// TEST: Fonction pour tester manuellement les clics
function testClickUpload(target) {
    console.log(`\n🧪 TEST MANUEL pour: ${target}`);
    const zone = document.querySelector(`[data-target="${target}"]`);
    if (!zone) {
        console.error(`❌ Zone non trouvée: ${target}`);
        return;
    }
    
    const input = zone.querySelector('input[type="file"]');
    if (!input) {
        console.error(`❌ Input non trouvé`);
        return;
    }
    
    console.log(`✅ Zone trouvée, tentative de clic sur input...`);
    input.click();
    console.log(`✅ Click() exécuté\n`);
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('\n🚀 === INITIALISATION DU SYSTÈME ===\n');
    
    debugDragDropZones();
    initializeDragDrop();
    
    setTimeout(() => {
        console.log('\n🔍 Vérification post-initialisation...');
        debugDragDropZones();
        
        console.log('💡 Pour tester manuellement, tapez dans la console:');
        console.log('   testClickUpload("hero_image_path")');
        console.log('   testClickUpload("program_background_image")');
        console.log('   etc.\n');
    }, 500);
});