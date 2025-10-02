/* ============================================
   JavaScript pour Invitation_3.blade.php
   Invitation électronique personnalisée
   ============================================ */

function scrollToContent() {
    document.getElementById('invitation-content').scrollIntoView({
        behavior: 'smooth'
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Animation d'apparition de la carte de contenu hero au défilement
    const heroCard = document.querySelector('.hero-section .invitation-card');
    const heroSection = document.querySelector('.hero-section');
    
    // S'assurer que la carte est visible au chargement
    if (heroCard) {
        heroCard.style.opacity = '1';
    }
    
    // Debug complet de l'image hero côté client
    if (heroSection) {
        console.log('=== DEBUG IMAGE HERO CLIENT ===');
        const currentBg = heroSection.style.backgroundImage;
        const computedBg = window.getComputedStyle(heroSection).backgroundImage;
        console.log('Hero background style:', currentBg);
        console.log('Hero background computed:', computedBg);
        console.log('Hero section element:', heroSection);
        
        // Fonction pour tester si une image se charge
        function testImageLoad(url) {
            return new Promise((resolve) => {
                console.log('Testing image load for:', url);
                const img = new Image();
                img.onload = () => {
                    console.log('✅ Image loaded successfully:', url);
                    resolve(true);
                };
                img.onerror = (error) => {
                    console.log('❌ Image failed to load:', url, error);
                    resolve(false);
                };
                img.src = url;
            });
        }
        
        // Fonction pour forcer l'image par défaut
        function forceDefaultImage() {
            const defaultImage = '/img/couplelamika.jpeg';
            console.log('🔄 Forcing default image:', defaultImage);
            heroSection.style.backgroundImage = `url('${defaultImage}')`;
            heroSection.setAttribute('data-image-src', defaultImage);
        }
        
        // Tester l'image actuelle
        if (currentBg && currentBg !== 'none') {
            const urlMatch = currentBg.match(/url\(['"]?(.*?)['"]?\)/);
            if (urlMatch && urlMatch[1]) {
                const imageUrl = urlMatch[1];
                console.log('Found image URL in style:', imageUrl);
                testImageLoad(imageUrl).then(isLoaded => {
                    if (!isLoaded) {
                        console.log('❌ Image hero actuelle ne se charge pas, utilisation du fallback');
                        forceDefaultImage();
                    } else {
                        console.log('✅ Image hero chargée avec succès');
                    }
                });
            } else {
                console.log('❌ No valid URL found in background style');
                forceDefaultImage();
            }
        } else {
            console.log('❌ No background image set, forcing default');
            forceDefaultImage();
        }
        
        // Vérifier aussi l'attribut data-image-src
        const dataImageSrc = heroSection.getAttribute('data-image-src');
        if (dataImageSrc) {
            console.log('Data image src:', dataImageSrc);
            testImageLoad(dataImageSrc).then(isLoaded => {
                if (!isLoaded) {
                    console.log('❌ Data image src failed, using fallback');
                    forceDefaultImage();
                }
            });
        }
        
        console.log('=== FIN DEBUG IMAGE HERO CLIENT ===');
    }
    
    if (heroCard && heroSection) {
        // Animation d'apparition de la carte au chargement
        setTimeout(() => {
            heroCard.style.opacity = '1';
            heroCard.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Effet parallaxe pour la section hero
    if (heroSection) {
        let ticking = false;
        
        function updateParallax() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            if (scrolled < window.innerHeight) {
                heroSection.style.transform = `translateY(${rate}px)`;
            }
            
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }
        
        window.addEventListener('scroll', requestTick);
    }
    
    // Animation des sections
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('section-visible');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('section:not(:first-child)').forEach(section => {
        observer.observe(section);
    });
});
