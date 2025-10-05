document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de l\'édition d\'invitation...');
    
    // Initialiser Summernote
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Gérer l'affichage des images existantes
    handleExistingImages();
    
    // Initialiser le drag & drop
    initializeDragDrop();
});

function handleExistingImages() {
    console.log('🖼️ Gestion des images existantes...');
    
    // Vérifier chaque zone de drag & drop pour les images existantes
    document.querySelectorAll('.drag-drop-zone').forEach(zone => {
        const target = zone.dataset.target;
        const preview = zone.querySelector('.image-preview');
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && preview.style.display === 'block') {
            // Masquer le contenu de drag & drop si une image existe
            if (content) {
                content.style.display = 'none';
            }
            console.log(`  ✅ Image existante affichée pour: ${target}`);
        }
    });
}

function initializeDragDrop() {
    console.log('\n🖱️ Initialisation du drag & drop...');
    
    document.querySelectorAll('.drag-drop-zone').forEach((zone, index) => {
        const input = zone.querySelector('input[type="file"]');
        const target = zone.dataset.target;
        const isMultiple = zone.dataset.multiple === 'true';
        
        console.log(`\n📦 Zone ${index + 1}: ${target} (multiple: ${isMultiple})`);
        
        if (!input) {
            console.error(`❌ Input non trouvé pour: ${target}`);
            return;
        }
        
        // SOLUTION 1: Clic sur toute la zone (sauf boutons)
        zone.addEventListener('click', function(e) {
            // Ignorer si clic sur bouton de suppression
            if (e.target.closest('.remove-image')) {
                console.log('⏭️ Clic sur bouton suppression ignoré');
                return;
            }
            
            // Ignorer si clic sur bouton de sélection
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
            // Vérifier si on quitte vraiment la zone
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
            console.log(`Fichiers reçus: ${files.length}`);
            
            if (files.length > 0) {
                // Vérifier que ce sont des images
                const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
                console.log(`Images valides: ${imageFiles.length}`);
                
                if (imageFiles.length === 0) {
                    alert('❌ Veuillez glisser uniquement des fichiers images (JPEG, PNG, JPG, GIF, WebP).');
                    return;
                }
                
                // Vérifier la taille des fichiers (2MB max)
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
                
                console.log(`Fichiers assignés à l'input: ${input.files.length}`);
                handleFileSelect(input, target);
            }
        });
        
        // Changement de fichier (sélection classique)
        input.addEventListener('change', function(e) {
            console.log(`📂 Fichier sélectionné pour ${target}: ${e.target.files.length} fichier(s)`);
            
            if (e.target.files.length === 0) {
                return;
            }
            
            // Valider les fichiers sélectionnés
            const files = Array.from(e.target.files);
            const validFiles = files.filter(f => f.type.startsWith('image/'));
            
            if (validFiles.length !== files.length) {
                alert('❌ Seuls les fichiers images sont autorisés (JPEG, PNG, JPG, GIF, WebP).');
                e.target.value = '';
                return;
            }
            
            // Vérifier la taille des fichiers (2MB max)
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
    
    // Gestion des boutons de suppression
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
        console.log(`Image chargée pour ${target}`);
        
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
            console.log(`  Preview: ${preview ? '✅' : '❌'}`);
            console.log(`  PreviewImg: ${previewImg ? '✅' : '❌'}`);
            console.log(`  Content: ${content ? '✅' : '❌'}`);
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
    
    if (!preview || !previewImages) {
        console.error('❌ Éléments de galerie non trouvés');
        return;
    }
    
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
    console.log(`Suppression de l'image pour: ${target}`);
    
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
        const preview = zone.querySelector('.image-preview');
        
        console.log(`Zone ${i + 1}: ${target}`);
        console.log(`  Input: ${input ? '✅' : '❌'}`);
        console.log(`  Texte cliquable: ${clickText ? '✅' : '❌'}`);
        console.log(`  Bouton parcourir: ${fileBtn ? '✅' : '❌'}`);
        console.log(`  Bouton suppression: ${removeBtn ? '✅' : '❌'}`);
        console.log(`  Prévisualisation: ${preview ? '✅' : '❌'}\n`);
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