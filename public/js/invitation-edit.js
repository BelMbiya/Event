document.addEventListener('DOMContentLoaded', function() {
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

    // Initialiser le drag & drop
    initializeDragDrop();
    
    // Gérer l'affichage des images existantes
    handleExistingImages();
});

function handleExistingImages() {
    console.log('🖼️ Gestion des images existantes...');
    
    // Vérifier chaque zone de drag & drop pour les images existantes
    document.querySelectorAll('.drag-drop-zone').forEach(zone => {
        const target = zone.dataset.target;
        const preview = document.getElementById(`${target}_preview`);
        const content = zone.querySelector('.drag-drop-content');
        
        if (preview && preview.style.display === 'block') {
            // Masquer le contenu de drag & drop si une image existe
            if (content) {
                content.style.display = 'none';
            }
            console.log(`Image existante affichée pour: ${target}`);
        }
    });
}

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
