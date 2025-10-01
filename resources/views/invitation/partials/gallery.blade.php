    <!-- Section Galerie -->
    @php
        // SOLUTION DÉFINITIVE POUR LA GALERIE
        $gallery = [];
        if ($content->gallery) {
            if (is_string($content->gallery)) {
                $gallery = json_decode($content->gallery, true) ?: [];
            } elseif (is_array($content->gallery)) {
                $gallery = $content->gallery;
            }
        }
    @endphp
    @if($gallery && is_array($gallery))
    <section class="gallery-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center" style="opacity: 1;">
                        <h2 class="modern-font gradient-text mb-4" style="font-size: 2.5rem;">Galerie</h2>
                        <div class="row">
                            @php
                                // $gallery est déjà défini plus haut
                            @endphp
                            @foreach($gallery as $index => $image)
                                <div class="col-md-4 mb-3">
                                    <div class="image-frame">
                                        @php
                                            // SOLUTION DÉFINITIVE POUR LES IMAGES DE GALERIE
                                            $imageUrl = null;
                                            if($image) {
                                                $cleanPath = trim($image);
                                                
                                                // 1. Essayer le chemin direct avec storage/
                                                $directPath = 'storage/' . $cleanPath;
                                                if(file_exists(public_path($directPath))) {
                                                    $imageUrl = 'http://localhost:8000/' . $directPath;
                                                }
                                                
                                                // 2. Si pas trouvé, essayer avec le nom de fichier seulement
                                                if(!$imageUrl) {
                                                    $filenamePath = 'storage/invitations/gallery/' . basename($cleanPath);
                                                    if(file_exists(public_path($filenamePath))) {
                                                        $imageUrl = 'http://localhost:8000/' . $filenamePath;
                                                    }
                                                }
                                                
                                                // 3. Si pas trouvé, essayer le chemin original
                                                if(!$imageUrl) {
                                                    if(file_exists(public_path($cleanPath))) {
                                                        $imageUrl = 'http://localhost:8000/' . $cleanPath;
                                                    }
                                                }
                                                
                                                // 4. Fallback final même si le fichier n'existe pas
                                                if(!$imageUrl) {
                                                    $imageUrl = 'http://localhost:8000/storage/' . $cleanPath;
                                                }
                                            }
                                        @endphp
                                        
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $content->hero_image_alt ?? 'Image de la galerie' }}" 
                                                 class="img-fluid rounded" 
                                                 style="width: 100%; height: 200px; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        
                                        <div class="placeholder-image {{ $imageUrl ? 'd-none' : 'd-flex' }}" 
                                             style="width: 100%; height: 200px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 2px dashed #dee2e6;">
                                            <div class="text-center">
                                                <i class="fas fa-image text-muted mb-2" style="font-size: 2rem;"></i>
                                                <p class="text-muted small mb-0">Image non disponible</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
