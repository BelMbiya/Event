<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->couple ?? $content->meta_title ?? 'Notre Mariage' }} - Invitation Électronique</title>
    
    <!-- Métadonnées SEO personnalisables -->
    <meta name="description" content="{{ $content->meta_description ?? 'Invitation électronique pour notre mariage' }}">
    <meta name="keywords" content="mariage, invitation, {{ $content->couple ?? 'couple' }}, {{ $content->venue_city ?? 'Kinshasa' }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $content->couple ?? $content->meta_title ?? 'Notre Mariage' }}">
    <meta property="og:description" content="{{ $content->meta_description ?? 'Invitation électronique pour notre mariage' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($content->og_image)
    <meta property="og:image" content="{{ asset('storage/' . $content->og_image) }}">
    @endif
    
    <!-- Ajout de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cormorant+Garamond:wght@300;400;500&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/invitation-3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/invitation-dynamic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print-styles.css') }}">

    <!-- ✅ STYLES DYNAMIQUES BASÉS SUR LES COULEURS DE LA BASE DE DONNÉES -->
    <style>
        :root {
            --color-primary: {{ $themeColors['primary'] ?? '#e11d48' }};
            --color-secondary: {{ $themeColors['secondary'] ?? '#f43f5e' }};
            --color-accent: {{ $themeColors['accent'] ?? '#fb7185' }};
            
            /* Variables RGB pour les effets de focus */
            --color-primary-rgb: {{ hexdec(substr($themeColors['primary'] ?? '#e11d48', 1, 2)) }}, {{ hexdec(substr($themeColors['primary'] ?? '#e11d48', 3, 2)) }}, {{ hexdec(substr($themeColors['primary'] ?? '#e11d48', 5, 2)) }};
            --color-secondary-rgb: {{ hexdec(substr($themeColors['secondary'] ?? '#f43f5e', 1, 2)) }}, {{ hexdec(substr($themeColors['secondary'] ?? '#f43f5e', 3, 2)) }}, {{ hexdec(substr($themeColors['secondary'] ?? '#f43f5e', 5, 2)) }};
        }
        
        /* Décorations conditionnelles */
        @if($themeDecorations['floral'] ?? false)
        .floral-decoration::before {
            content: "🌸";
        }
        @endif
        
        @if($themeDecorations['overlay'] ?? false)
        .hero-section::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }
        @endif
        
        @if($themeDecorations['parallax'] ?? false)
        .hero-section {
            background-attachment: fixed;
        }
        @endif
    </style>
</head>

<body class="digital-invitation {{ (request()->routeIs('invitation.download') || request()->has('pdf')) ? 'pdf-optimized' : '' }}">

    <!-- Éléments flottants décoratifs -->
    <div class="floating-elements">
        <div class="floating-element" style="top: 10%; left: 10%; animation-delay: 0s;">
            <i class="fas fa-heart" style="font-size: 2rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="top: 20%; right: 15%; animation-delay: 2s;">
            <i class="fas fa-star" style="font-size: 1.5rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="bottom: 30%; left: 20%; animation-delay: 4s;">
            <i class="fas fa-heart" style="font-size: 1.8rem; color: rgba(255,255,255,0.3);"></i>
        </div>
        <div class="floating-element" style="bottom: 20%; right: 10%; animation-delay: 1s;">
            <i class="fas fa-star" style="font-size: 2.2rem; color: rgba(255,255,255,0.3);"></i>
        </div>
    </div>


    @include('invitation.partials.hero')

    @include('invitation.partials.invite-content')

    @include('invitation.partials.livredor')

    @include('invitation.partials.program')

    @include('invitation.partials.choixboissons')

    @include('invitation.partials.gallery')
    @include('invitation.partials.footer')

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="{{ asset('js/invitation-3.js') }}"></script>
</body>
</html>
