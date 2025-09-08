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

    <style>
        /* Palette pilotée par la base de données (content.theme) */
        :root {
            @php
                $themeVars = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $themeVars = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $themeVars = $content->theme;
                    }
                }
                $primary = $themeVars['colors']['primary'] ?? '#e11d48';     // rose
                $secondary = $themeVars['colors']['secondary'] ?? '#f59e0b';  // doré
                $textColor = $themeVars['colors']['text'] ?? '#6b7280';
                $paper = '#ffffff';
                $blush = '#ffe9ef';
            @endphp
            --color-primary: {{ $primary }};
            --color-secondary: {{ $secondary }};
            --color-text: {{ $textColor }};
            --color-paper: {{ $paper }};
            --color-blush: {{ $blush }};
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .romantic-font {
            font-family: 'Alex Brush', cursive;
        }

        .elegant-font {
            font-family: 'Cormorant Garamond', serif;
        }

        .modern-font {
            font-family: 'Poppins', sans-serif;
        }

        .animate-fade-in {
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        /* Fond général doux comme sur le mockup (rose clair) */
        .digital-invitation {
            background:
                radial-gradient(1200px 600px at 50% -300px, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 60%),
                radial-gradient(800px 400px at 20% 20%, rgba(255,182,193,0.15) 0%, rgba(255,182,193,0) 60%),
                radial-gradient(600px 300px at 80% 80%, rgba(255,218,185,0.12) 0%, rgba(255,218,185,0) 60%),
                linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%);
            min-height: 100vh;
        }

        /* Carte type papier aquarelle, cadre smartphone */
        .invitation-card {
            background:
                radial-gradient(800px 400px at 20% 0%, rgba(225,29,72,0.12) 0%, rgba(225,29,72,0) 60%),
                radial-gradient(700px 500px at 90% 100%, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0) 60%),
                var(--color-paper);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.15);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        /* Fleurs décoratives (CSS pur pour éviter les erreurs PDF) */
        .invitation-card::before,
        .invitation-card::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            pointer-events: none;
            opacity: 0.3;
            border-radius: 50%;
        }
        .invitation-card::before {
            top: -50px;
            left: -50px;
            background: radial-gradient(circle, rgba(225,29,72,0.2) 0%, rgba(225,29,72,0.1) 30%, transparent 70%);
            transform: rotate(45deg);
        }
        .invitation-card::after {
            right: -50px;
            bottom: -50px;
            background: radial-gradient(circle, rgba(245,158,11,0.2) 0%, rgba(245,158,11,0.1) 30%, transparent 70%);
            transform: rotate(-45deg);
        }

        /* Désactivation des icônes flottants pour un rendu plus épuré */
        .floating-elements { display: none; }
        .floating-element { display: none; }

        /* Arrière-plans contextuels par section basés sur la BDD */
        @php
            $eventType = $event->eventType ?? null;
            $eventTypeName = $eventType ? strtolower($eventType->name ?? '') : '';
            $isWedding = strpos($eventTypeName, 'mariage') !== false || strpos($eventTypeName, 'wedding') !== false;
            $isBirthday = strpos($eventTypeName, 'anniversaire') !== false || strpos($eventTypeName, 'birthday') !== false;
            $isCorporate = strpos($eventTypeName, 'entreprise') !== false || strpos($eventTypeName, 'corporate') !== false;
            
            // Couleurs contextuelles basées sur le type d'événement
            $contextColors = [
                'wedding' => ['primary' => '#e11d48', 'secondary' => '#f59e0b', 'accent' => '#fbbf24'],
                'birthday' => ['primary' => '#3b82f6', 'secondary' => '#8b5cf6', 'accent' => '#a78bfa'],
                'corporate' => ['primary' => '#1f2937', 'secondary' => '#374151', 'accent' => '#6b7280'],
                'default' => ['primary' => $primary, 'secondary' => $secondary, 'accent' => '#fbbf24']
            ];
            
            $currentContext = $isWedding ? 'wedding' : ($isBirthday ? 'birthday' : ($isCorporate ? 'corporate' : 'default'));
            $context = $contextColors[$currentContext];
            @endphp

        /* Section Hero avec arrière-plan contextuel - seulement si pas d'image */
        .hero-section:not([style*="background-image"]),
        .hero-section.no-hero-image {
            background: 
                radial-gradient(1200px 600px at 50% -300px, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 60%),
                radial-gradient(800px 400px at 20% 20%, rgba(255,182,193,0.15) 0%, rgba(255,182,193,0) 60%),
                radial-gradient(600px 300px at 80% 80%, rgba(255,218,185,0.12) 0%, rgba(255,218,185,0) 60%),
                linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%);
            position: relative;
        }

        /* Motifs décoratifs pour les sections sans image */
        .hero-section.no-hero-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e11d48' fill-opacity='0.03'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
            pointer-events: none;
        }

        .hero-section.no-hero-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(225, 29, 72, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(225, 29, 72, 0.04) 0%, transparent 50%);
            z-index: 2;
            pointer-events: none;
        }

        /* Overlay contextuel pour les images de fond */
        .hero-section[style*="background-image"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(800px 400px at 20% 20%, rgba(255,182,193,0.2) 0%, rgba(255,182,193,0) 60%),
                radial-gradient(600px 300px at 80% 80%, rgba(255,218,185,0.15) 0%, rgba(255,218,185,0) 60%),
                transparent;
            pointer-events: none;
            z-index: 1;
        }

        /* Section Invitation avec pattern contextuel */
        .invitation-section {
            background: 
                @if($isWedding)
                    linear-gradient(135deg, rgba(225,29,72,0.05) 0%, rgba(245,158,11,0.03) 100%),
                @elseif($isBirthday)
                    linear-gradient(135deg, rgba(59,130,246,0.05) 0%, rgba(139,92,246,0.03) 100%),
                @elseif($isCorporate)
                    linear-gradient(135deg, rgba(31,41,55,0.05) 0%, rgba(55,65,81,0.03) 100%),
                @else
                    linear-gradient(135deg, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.05) 0%, rgba({{ hexdec(substr($secondary,1,2)) }},{{ hexdec(substr($secondary,3,2)) }},{{ hexdec(substr($secondary,5,2)) }},0.03) 100%),
                @endif
                rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Section Livre d'Or avec thème romantique */
        .guestbook-section {
            background: 
                @if($isWedding)
                    radial-gradient(600px 400px at 30% 20%, rgba(225,29,72,0.08) 0%, rgba(225,29,72,0) 60%),
                    radial-gradient(500px 300px at 70% 80%, rgba(245,158,11,0.06) 0%, rgba(245,158,11,0) 60%),
                @else
                    radial-gradient(600px 400px at 30% 20%, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.08) 0%, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0) 60%),
                @endif
                rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Section Programme avec timeline contextuelle */
        .schedule-section {
            background: 
                @if($isWedding)
                    linear-gradient(45deg, rgba(225,29,72,0.04) 0%, rgba(245,158,11,0.02) 50%, rgba(225,29,72,0.04) 100%),
                @elseif($isBirthday)
                    linear-gradient(45deg, rgba(59,130,246,0.04) 0%, rgba(139,92,246,0.02) 50%, rgba(59,130,246,0.04) 100%),
                @elseif($isCorporate)
                    linear-gradient(45deg, rgba(31,41,55,0.04) 0%, rgba(55,65,81,0.02) 50%, rgba(31,41,55,0.04) 100%),
                @else
                    linear-gradient(45deg, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.04) 0%, rgba({{ hexdec(substr($secondary,1,2)) }},{{ hexdec(substr($secondary,3,2)) }},{{ hexdec(substr($secondary,5,2)) }},0.02) 50%, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.04) 100%),
                @endif
                rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Section Boissons avec ambiance festive */
        .drinks-section {
            background: 
                @if($isWedding)
                    radial-gradient(800px 500px at 50% 0%, rgba(245,158,11,0.08) 0%, rgba(245,158,11,0) 60%),
                    radial-gradient(600px 400px at 0% 100%, rgba(225,29,72,0.06) 0%, rgba(225,29,72,0) 60%),
                @elseif($isBirthday)
                    radial-gradient(800px 500px at 50% 0%, rgba(139,92,246,0.08) 0%, rgba(139,92,246,0) 60%),
                    radial-gradient(600px 400px at 0% 100%, rgba(59,130,246,0.06) 0%, rgba(59,130,246,0) 60%),
                @else
                    radial-gradient(800px 500px at 50% 0%, rgba({{ hexdec(substr($secondary,1,2)) }},{{ hexdec(substr($secondary,3,2)) }},{{ hexdec(substr($secondary,5,2)) }},0.08) 0%, rgba({{ hexdec(substr($secondary,1,2)) }},{{ hexdec(substr($secondary,3,2)) }},{{ hexdec(substr($secondary,5,2)) }},0) 60%),
                @endif
                linear-gradient(180deg, rgba(255,255,255,0.5), rgba(255,255,255,0.2));
            backdrop-filter: blur(8px);
        }

        /* Section Galerie avec effet focus */
        .gallery-section {
            background: 
                @if($isWedding)
                    radial-gradient(1000px 600px at 50% 50%, rgba(225,29,72,0.06) 0%, rgba(225,29,72,0) 70%),
                @elseif($isBirthday)
                    radial-gradient(1000px 600px at 50% 50%, rgba(59,130,246,0.06) 0%, rgba(59,130,246,0) 70%),
                @elseif($isCorporate)
                    radial-gradient(1000px 600px at 50% 50%, rgba(31,41,55,0.06) 0%, rgba(31,41,55,0) 70%),
                @else
                    radial-gradient(1000px 600px at 50% 50%, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.06) 0%, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0) 70%),
                @endif
                rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Footer avec dégradé contextuel */
        .footer-section {
            background: 
                @if($isWedding)
                    linear-gradient(135deg, rgba(225,29,72,0.15) 0%, rgba(245,158,11,0.1) 100%),
                @elseif($isBirthday)
                    linear-gradient(135deg, rgba(59,130,246,0.15) 0%, rgba(139,92,246,0.1) 100%),
                @elseif($isCorporate)
                    linear-gradient(135deg, rgba(31,41,55,0.15) 0%, rgba(55,65,81,0.1) 100%),
                @else
                    linear-gradient(135deg, rgba({{ hexdec(substr($primary,1,2)) }},{{ hexdec(substr($primary,3,2)) }},{{ hexdec(substr($primary,5,2)) }},0.15) 0%, rgba({{ hexdec(substr($secondary,1,2)) }},{{ hexdec(substr($secondary,3,2)) }},{{ hexdec(substr($secondary,5,2)) }},0.1) 100%),
                @endif
                rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .gradient-text {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }}, {{ $theme['colors']['secondary'] ?? 'var(--color-secondary)' }});
            @else
                background: linear-gradient(45deg, var(--color-primary), var(--color-secondary));
            @endif
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modern-button {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }}, {{ $theme['colors']['secondary'] ?? 'var(--color-secondary)' }});
            @else
                background: linear-gradient(45deg, var(--color-primary), var(--color-secondary));
            @endif
            border: none;
            border-radius: 999px;
            padding: 12px 26px;
            color: white;
            font-weight: 600;
            letter-spacing: .3px;
        }

        /* Timeline Styles Compact - Style Tableau */
        .timeline-container {
            position: relative;
            max-width: 700px;
            margin: 0 auto;
            padding: 1rem 0;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, 
                rgba(225,29,72,0.8) 0%, 
                rgba(245,158,11,0.8) 50%, 
                rgba(225,29,72,0.8) 100%);
            transform: translateX(-50%);
            border-radius: 2px;
            box-shadow: 0 0 15px rgba(225,29,72,0.3);
        }

        .timeline-item {
            position: relative;
            margin: 25px 0;
            width: 100%;
            opacity: 0;
            animation: slideInUp 0.6s ease forwards;
        }

        .timeline-item:nth-child(1) { animation-delay: 0.1s; }
        .timeline-item:nth-child(2) { animation-delay: 0.15s; }
        .timeline-item:nth-child(3) { animation-delay: 0.2s; }
        .timeline-item:nth-child(4) { animation-delay: 0.25s; }
        .timeline-item:nth-child(5) { animation-delay: 0.3s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .timeline-marker {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
        }

        .timeline-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, 
                rgba(225,29,72,0.9) 0%, 
                rgba(245,158,11,0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(225,29,72,0.4);
            border: 3px solid white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .timeline-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }

        .timeline-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(225,29,72,0.6);
        }

        .timeline-icon:hover::before {
            animation: shine 0.6s ease;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .timeline-content {
            background: linear-gradient(135deg, 
                rgba(255,255,255,0.95) 0%, 
                rgba(255,248,248,0.95) 100%);
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            position: relative;
            width: 45%;
            margin-top: -25px;
            border: 1px solid rgba(225,29,72,0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .timeline-content:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .timeline-left .timeline-content {
            margin-left: 0;
            margin-right: auto;
        }

        .timeline-right .timeline-content {
            margin-left: auto;
            margin-right: 0;
        }

        .timeline-time {
            font-size: 1rem;
            font-weight: 700;
            background: linear-gradient(45deg, #e11d48, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Poppins', sans-serif;
            min-width: 80px;
            text-align: center;
            padding: 6px 12px;
            border-radius: 20px;
            background-color: rgba(225,29,72,0.1);
            flex-shrink: 0;
        }

        .timeline-event {
            font-size: 0.95rem;
            color: #333;
            line-height: 1.4;
            font-family: 'Cormorant Garamond', serif;
            font-weight: 500;
            flex: 1;
        }

        .timeline-content::before {
            content: '';
            position: absolute;
            top: 25px;
            width: 0;
            height: 0;
            border: 12px solid transparent;
        }

        .timeline-left .timeline-content::before {
            right: -24px;
            border-left-color: rgba(255,255,255,0.95);
        }

        .timeline-right .timeline-content::before {
            left: -24px;
            border-right-color: rgba(255,255,255,0.95);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .timeline::before {
                left: 30px;
            }

            .timeline-marker {
                left: 30px;
            }

            .timeline-icon {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }

            .timeline-content {
                width: calc(100% - 80px);
                margin-left: 80px !important;
                margin-right: 0 !important;
                padding: 12px 15px;
                flex-direction: column;
                gap: 8px;
            }

            .timeline-content::before {
                left: -12px !important;
                right: auto !important;
                border-right-color: rgba(255,255,255,0.95) !important;
                border-left-color: transparent !important;
            }

            .timeline-time {
                font-size: 0.9rem;
                min-width: auto;
                width: 100%;
            }

            .timeline-event {
                font-size: 0.9rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .timeline-container {
                padding: 0.5rem 0;
            }

            .timeline-item {
                margin: 20px 0;
            }

            .timeline-icon {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }

            .timeline-content {
                width: calc(100% - 70px);
                margin-left: 70px !important;
                padding: 10px 12px;
            }
        }
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .modern-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Styles pour la section des boissons */
        .drinks-section {
            background: linear-gradient(180deg, rgba(255,255,255,0.5), rgba(255,255,255,0.2));
            backdrop-filter: blur(8px);
            padding: 3rem 0;
        }
        
        .drink-label {
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .drink-label:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Styles des pilles de boissons adaptés au thème */
        .drink-label {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                border-color: {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }} !important;
                color: {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }} !important;
            @else
                border-color: var(--color-primary) !important;
                color: var(--color-primary) !important;
            @endif
            background-color: transparent !important;
            transition: all 0.3s ease !important;
            font-weight: 500 !important;
            letter-spacing: 0.5px !important;
        }

        .drink-label:hover {
            @if($theme && isset($theme['colors']))
                background-color: {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }} !important;
                color: white !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 12px rgba({{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 1, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 3, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 5, 2)) }}, 0.3) !important;
            @else
                background-color: var(--color-primary) !important;
                color: white !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3) !important;
            @endif
        }

        .btn-check:checked + .drink-label {
            @if($theme && isset($theme['colors']))
                background-color: {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }} !important;
                border-color: {{ $theme['colors']['primary'] ?? 'var(--color-primary)' }} !important;
                color: white !important;
                box-shadow: 0 4px 15px rgba({{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 1, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 3, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 5, 2)) }}, 0.4) !important;
            @else
                background-color: var(--color-primary) !important;
                border-color: var(--color-primary) !important;
                color: white !important;
                box-shadow: 0 4px 15px rgba(225, 29, 72, 0.4) !important;
            @endif
            transform: translateY(-2px) !important;
        }

        /* Animation pour les pilles sélectionnées */
        .btn-check:checked + .drink-label::before {
            content: '✓ ';
            font-weight: bold;
            margin-right: 4px;
        }

        .section-visible {
            animation: fadeInUp 1s ease forwards;
        }

        /* Amélioration de l'affichage des images */
        .image-frame {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .image-frame:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .image-frame img {
            transition: transform 0.3s ease;
        }

        .image-frame:hover img {
            transform: scale(1.05);
        }

        /* Z-index pour les overlays */
        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        .hero-section .absolute {
            z-index: 1;
        }

        /* Amélioration des pilles de boissons */
        .drink-option {
            margin: 0.5rem;
            position: relative;
        }

        .drink-option .btn-check {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .drink-option .drink-label {
            cursor: pointer;
            user-select: none;
            min-width: 120px;
            text-align: center;
        }

        /* Animation de sélection */
        .drink-option .btn-check:checked + .drink-label {
            animation: pulse 0.6s ease-in-out;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Style spécial pour le bouton de carte adapté au thème */
        .map-button {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? '#e11d48' }}, {{ $theme['colors']['secondary'] ?? '#f59e0b' }}) !important;
            @else
                background: linear-gradient(45deg, #e11d48, #f59e0b) !important;
            @endif
            border: none !important;
            position: relative;
            overflow: hidden;
        }

        .map-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .map-button:hover::before {
            left: 100%;
        }

        .map-button:hover {
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['primary'] ?? '#e11d48' }}, {{ $theme['colors']['secondary'] ?? '#f59e0b' }}) !important;
                box-shadow: 0 8px 20px rgba({{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 1, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 3, 2)) }}, {{ hexdec(substr($theme['colors']['primary'] ?? '#e11d48', 5, 2)) }}, 0.4) !important;
            @else
                background: linear-gradient(45deg, #e11d48, #f59e0b) !important;
                box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4) !important;
            @endif
            transform: translateY(-3px) !important;
        }

        /* Style pour le bouton de téléchargement */
        .download-button {
            @php
                $theme = null;
                if ($content->theme) {
                    if (is_string($content->theme)) {
                        $theme = json_decode($content->theme, true);
                    } elseif (is_array($content->theme)) {
                        $theme = $content->theme;
                    }
                }
            @endphp
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['secondary'] ?? '#f59e0b' }}, {{ $theme['colors']['accent'] ?? '#fbbf24' }}) !important;
            @else
                background: linear-gradient(45deg, #f59e0b, #fbbf24) !important;
            @endif
        }

        .download-button:hover {
            @if($theme && isset($theme['colors']))
                background: linear-gradient(45deg, {{ $theme['colors']['secondary'] ?? '#f59e0b' }}, {{ $theme['colors']['accent'] ?? '#fbbf24' }}) !important;
                box-shadow: 0 8px 20px rgba({{ hexdec(substr($theme['colors']['secondary'] ?? '#f59e0b', 1, 2)) }}, {{ hexdec(substr($theme['colors']['secondary'] ?? '#f59e0b', 3, 2)) }}, {{ hexdec(substr($theme['colors']['secondary'] ?? '#f59e0b', 5, 2)) }}, 0.4) !important;
            @else
                background: linear-gradient(45deg, #f59e0b, #fbbf24) !important;
                box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4) !important;
            @endif
            transform: translateY(-3px) !important;
        }

        /* Styles pour le header de la section programme */
        .schedule-header {
            position: relative;
            padding: 2rem 0;
        }

        .schedule-subtitle {
            margin-top: 1rem;
        }

        .schedule-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 1rem;
        }

        .divider-line {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #e11d48, transparent);
            border-radius: 1px;
        }

        /* Styles PDF optimisés - CSS pur pour correspondre exactement à l'invitation */
        .pdf-optimized {
            background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
            color: #333 !important;
            font-family: 'Cormorant Garamond', serif !important;
            line-height: 1.6 !important;
        }

        .pdf-optimized * {
            box-sizing: border-box !important;
        }

        .pdf-optimized .container {
            max-width: 1200px !important;
            margin: 0 auto !important;
            padding: 0 20px !important;
        }

        .pdf-optimized .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin: 0 -15px !important;
        }

        .pdf-optimized .col-lg-10 {
            flex: 0 0 83.333333% !important;
            max-width: 83.333333% !important;
            padding: 0 15px !important;
        }

        .pdf-optimized .col-md-4 {
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
            padding: 0 15px !important;
        }

        .pdf-optimized .col-md-6 {
            flex: 0 0 50% !important;
            max-width: 50% !important;
            padding: 0 15px !important;
        }

        .pdf-optimized .mb-3 { margin-bottom: 1rem !important; }
        .pdf-optimized .mb-4 { margin-bottom: 1.5rem !important; }
        .pdf-optimized .mb-5 { margin-bottom: 3rem !important; }
        .pdf-optimized .py-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
        .pdf-optimized .text-center { text-align: center !important; }
        .pdf-optimized .text-start { text-align: left !important; }
        .pdf-optimized .justify-content-center { justify-content: center !important; }

        .pdf-optimized .hero-section {
            background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
            background-image: none !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
        }

        .pdf-optimized .hero-section::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
            background: radial-gradient(1200px 600px at 50% -300px, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 60%),
                        radial-gradient(800px 400px at 20% 20%, rgba(255,182,193,0.15) 0%, rgba(255,182,193,0) 60%),
                        radial-gradient(600px 300px at 80% 80%, rgba(255,218,185,0.12) 0%, rgba(255,218,185,0) 60%) !important;
            z-index: 1 !important;
        }

        .pdf-optimized .invitation-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 3rem !important;
            position: relative !important;
            z-index: 2 !important;
        }

        .pdf-optimized .gradient-text {
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 50%, #fb7185 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            color: #e11d48 !important;
            font-family: 'Alex Brush', cursive !important;
        }

        .pdf-optimized .modern-font {
            font-family: 'Alex Brush', cursive !important;
            font-weight: 400 !important;
        }

        .pdf-optimized .elegant-font {
            font-family: 'Cormorant Garamond', serif !important;
            font-weight: 400 !important;
        }

        .pdf-optimized .timeline-container {
            max-width: 700px !important;
            margin: 0 auto !important;
            padding: 1rem 0 !important;
            position: relative !important;
        }

        .pdf-optimized .timeline::before {
            content: '' !important;
            position: absolute !important;
            left: 50% !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 4px !important;
            background: linear-gradient(180deg, #e11d48 0%, #f43f5e 50%, #fb7185 100%) !important;
            transform: translateX(-50%) !important;
            border-radius: 2px !important;
            box-shadow: 0 0 10px rgba(225, 29, 72, 0.3) !important;
        }

        .pdf-optimized .timeline-item {
            margin: 25px 0 !important;
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .pdf-optimized .timeline-icon {
            width: 50px !important;
            height: 50px !important;
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            border: 3px solid white !important;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.3) !important;
            position: relative !important;
            z-index: 2 !important;
        }

        .pdf-optimized .timeline-content {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            padding: 15px 20px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            width: 45% !important;
            margin-top: -25px !important;
            display: flex !important;
            align-items: center !important;
            gap: 15px !important;
            position: relative !important;
        }

        .pdf-optimized .timeline-content::before {
            content: '' !important;
            position: absolute !important;
            top: 25px !important;
            width: 0 !important;
            height: 0 !important;
            border: 12px solid transparent !important;
        }

        .pdf-optimized .timeline-item:nth-child(odd) .timeline-content {
            margin-left: auto !important;
            margin-right: 0 !important;
        }

        .pdf-optimized .timeline-item:nth-child(odd) .timeline-content::before {
            right: -24px !important;
            border-left-color: rgba(255, 255, 255, 0.9) !important;
        }

        .pdf-optimized .timeline-item:nth-child(even) .timeline-content {
            margin-left: 0 !important;
            margin-right: auto !important;
        }

        .pdf-optimized .timeline-item:nth-child(even) .timeline-content::before {
            left: -24px !important;
            border-right-color: rgba(255, 255, 255, 0.9) !important;
        }

        .pdf-optimized .timeline-time {
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%) !important;
            color: white !important;
            padding: 6px 12px !important;
            border-radius: 20px !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            min-width: 80px !important;
            text-align: center !important;
            white-space: nowrap !important;
        }

        .pdf-optimized .timeline-event {
            color: #333 !important;
            font-size: 0.95rem !important;
            line-height: 1.4 !important;
            font-weight: 500 !important;
        }

        .pdf-optimized .guestbook-section,
        .pdf-optimized .drinks-section {
            background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
            background-image: none !important;
            position: relative !important;
        }

        .pdf-optimized .schedule-section {
            background: linear-gradient(135deg, #fff0f3 0%, #ffe9ef 50%, #fff0f3 100%) !important;
            position: relative !important;
        }

        .pdf-optimized .gallery-section {
            background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
            position: relative !important;
        }

        /* Styles pour les titres et sous-titres */
        .pdf-optimized h1 {
            font-size: 3.5rem !important;
            font-weight: 400 !important;
            margin-bottom: 1rem !important;
            line-height: 1.2 !important;
        }

        .pdf-optimized h2 {
            font-size: 2.5rem !important;
            font-weight: 400 !important;
            margin-bottom: 1.5rem !important;
            line-height: 1.3 !important;
        }

        .pdf-optimized h3 {
            font-size: 1.8rem !important;
            font-weight: 500 !important;
            margin-bottom: 1rem !important;
            line-height: 1.4 !important;
        }

        .pdf-optimized p {
            font-size: 1.1rem !important;
            line-height: 1.6 !important;
            margin-bottom: 1rem !important;
        }

        /* Styles pour les informations importantes */
        .pdf-optimized .event-info {
            background: rgba(255, 255, 255, 0.8) !important;
            border-radius: 15px !important;
            padding: 2rem !important;
            margin: 2rem 0 !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .pdf-optimized .event-details {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .pdf-optimized .event-detail-item {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
            padding: 0.5rem 0 !important;
        }

        .pdf-optimized .event-detail-icon {
            width: 40px !important;
            height: 40px !important;
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1rem !important;
            flex-shrink: 0 !important;
        }

        .pdf-optimized .event-detail-text {
            flex: 1 !important;
        }

        .pdf-optimized .event-detail-label {
            font-weight: 600 !important;
            color: #e11d48 !important;
            font-size: 1rem !important;
        }

        .pdf-optimized .event-detail-value {
            color: #333 !important;
            font-size: 1.1rem !important;
            margin-top: 0.25rem !important;
        }

        .pdf-optimized .guestbook-section::before,
        .pdf-optimized .drinks-section::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
            background: rgba(0, 0, 0, 0.1) !important;
            z-index: 1 !important;
        }

        .pdf-optimized .container {
            position: relative !important;
            z-index: 2 !important;
        }

        /* Styles pour les sections spécifiques du PDF */
        .pdf-optimized .schedule-header h2 {
            font-size: 2.8rem !important;
            font-weight: 700 !important;
            margin-bottom: 1rem !important;
            text-align: center !important;
        }

        .pdf-optimized .schedule-subtitle p {
            font-size: 1.2rem !important;
            font-style: italic !important;
            text-align: center !important;
            margin-bottom: 1rem !important;
            color: #666 !important;
        }

        .pdf-optimized .schedule-divider {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 2rem 0 !important;
        }

        .pdf-optimized .divider-line {
            width: 100px !important;
            height: 2px !important;
            background: linear-gradient(90deg, transparent, #e11d48, transparent) !important;
            border-radius: 1px !important;
        }

        .pdf-optimized .schedule-divider i {
            color: #e11d48 !important;
            font-size: 1.2rem !important;
            margin: 0 1rem !important;
        }

        .pdf-optimized .drink-pill {
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%) !important;
            color: white !important;
            padding: 8px 16px !important;
            border-radius: 25px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            margin: 4px !important;
            display: inline-block !important;
        }

        .pdf-optimized .gallery-item {
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            margin: 10px !important;
        }

        .pdf-optimized .gallery-item img {
            width: 100% !important;
            height: 200px !important;
            object-fit: cover !important;
        }

        /* Styles pour les images et éléments visuels */
        .pdf-optimized img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 8px !important;
        }

        .pdf-optimized .image-frame {
            position: relative !important;
            overflow: hidden !important;
            border-radius: 15px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            transition: transform 0.3s ease !important;
        }

        .pdf-optimized .placeholder-image {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border: 2px dashed #dee2e6 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 200px !important;
        }

        /* Styles pour les badges et statuts */
        .pdf-optimized .badge {
            display: inline-block !important;
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            text-align: center !important;
        }

        .pdf-optimized .badge-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            color: white !important;
        }

        .pdf-optimized .badge-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%) !important;
            color: white !important;
        }

        .pdf-optimized .badge-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
            color: #333 !important;
        }

        /* Styles pour les formulaires et inputs */
        .pdf-optimized .form-control {
            width: 100% !important;
            padding: 0.75rem 1rem !important;
            border: 1px solid #ddd !important;
            border-radius: 8px !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            background: white !important;
        }

        .pdf-optimized .form-label {
            font-weight: 600 !important;
            color: #333 !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }

        /* Styles pour les sections avec background spécial */
        .pdf-optimized .bg-light {
            background: rgba(248, 249, 250, 0.8) !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        /* Amélioration des espacements */
        .pdf-optimized .d-flex {
            display: flex !important;
        }

        .pdf-optimized .flex-wrap {
            flex-wrap: wrap !important;
        }

        .pdf-optimized .gap-3 {
            gap: 1rem !important;
        }

        .pdf-optimized .me-2 {
            margin-right: 0.5rem !important;
        }

        .pdf-optimized .me-3 {
            margin-right: 1rem !important;
        }

        .pdf-optimized .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .pdf-optimized .mb-3 {
            margin-bottom: 1rem !important;
        }

        .pdf-optimized .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .pdf-optimized .mb-5 {
            margin-bottom: 3rem !important;
        }

        .pdf-optimized .text-white {
            color: #333 !important;
        }

        .pdf-optimized .text-white-50 {
            color: #666 !important;
        }

        .pdf-optimized .bg-opacity-20 {
            background: rgba(0, 0, 0, 0.1) !important;
        }

        .pdf-optimized .bg-opacity-40 {
            background: rgba(0, 0, 0, 0.2) !important;
        }

        /* Masquer tous les boutons dans le PDF */
        .pdf-optimized .modern-button,
        .pdf-optimized .btn,
        .pdf-optimized button,
        .pdf-optimized a[href*="download"],
        .pdf-optimized a[href*="rsvp"],
        .pdf-optimized a[href*="map"],
        .pdf-optimized .cta-buttons,
        .pdf-optimized .action-buttons {
            display: none !important;
        }

        /* Masquer les éléments flottants */
        .pdf-optimized .floating-elements,
        .pdf-optimized .floating-element {
            display: none !important;
        }

        /* Effet parallaxe pour la section hero */
        .hero-section {
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            background-position: center center !important;
            /* Fallback image par défaut */
            background-image: url('https://cdn0.mariages.net/article-real-wedding/678/3_2/1920/jpg/3928114.webp') !important;
        }

        /* Animation de parallaxe au défilement */
        @media (prefers-reduced-motion: no-preference) {
            .hero-section {
                transition: transform 0.1s ease-out;
            }
        }

        /* Animation d'apparition pour la carte de contenu */
        .hero-section .invitation-card {
            opacity: 0 !important;
            transition: opacity 0.8s ease-in-out !important;
            background: rgba(255, 255, 255, 0.75) !important;
        }

        .hero-section .invitation-card.show {
            opacity: 1 !important;
        }

        /* Ombre de soulèvement pour le titre */
        .hero-section .invitation-card h1 {
            font-size: 4.5rem !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-weight: 400;
            line-height: 1.1;
        }

        /* Version PDF-safe des décorations - Simplifiée pour correspondre au style .pdf-optimized */
        @media print {
            /* Appliquer le style PDF optimisé par défaut */
            body {
                background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
                color: #333 !important;
                font-family: 'Cormorant Garamond', serif !important;
            }

            /* Masquer tous les boutons et éléments interactifs */
            .modern-button,
            .btn,
            button,
            a[href*="download"],
            a[href*="rsvp"],
            a[href*="map"],
            .cta-buttons,
            .action-buttons,
            .floating-elements,
            .floating-element {
                display: none !important;
            }

            /* Assurer la lisibilité */
            .text-white {
                color: #333 !important;
            }

            .text-white-50 {
                color: #666 !important;
            }

            /* Simplifier les backgrounds pour l'impression */
            .hero-section,
            .guestbook-section,
            .drinks-section,
            .schedule-section,
            .gallery-section {
                background: linear-gradient(135deg, #ffe9ef 0%, #fff0f3 50%, #ffe9ef 100%) !important;
                background-image: none !important;
            }

            /* Masquer les overlays complexes */
            .hero-section::before,
            .guestbook-section::before,
            .drinks-section::before {
                display: none !important;
            }

            /* Assurer la visibilité du texte gradient */
            .gradient-text {
                color: #e11d48 !important;
                background: none !important;
                -webkit-background-clip: unset !important;
                -webkit-text-fill-color: unset !important;
            }
        }
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

    <!-- Hero Section -->
    @php
        // SOLUTION DÉFINITIVE POUR LES IMAGES HERO
        $heroImageUrl = null;
        
        if($content->hero_image_path) {
            $cleanPath = trim($content->hero_image_path);
            
            // 1. Essayer le chemin direct avec storage/
            $directPath = 'storage/' . $cleanPath;
            if(file_exists(public_path($directPath))) {
                $heroImageUrl = 'http://localhost:8000/' . $directPath;
            }
            
            // 2. Si pas trouvé, essayer avec le nom de fichier seulement
            if(!$heroImageUrl) {
                $filenamePath = 'storage/invitations/hero/' . basename($cleanPath);
                if(file_exists(public_path($filenamePath))) {
                    $heroImageUrl = 'http://localhost:8000/' . $filenamePath;
                }
            }
            
            // 3. Si pas trouvé, essayer le chemin original
            if(!$heroImageUrl) {
                if(file_exists(public_path($cleanPath))) {
                    $heroImageUrl = 'http://localhost:8000/' . $cleanPath;
                }
            }
            
            // 4. Fallback final même si le fichier n'existe pas
            if(!$heroImageUrl) {
                $heroImageUrl = 'http://localhost:8000/storage/' . $cleanPath;
            }
        }
        
        // Image par défaut si rien n'est trouvé
        $finalHeroImageUrl = $heroImageUrl ?: 'https://cdn0.mariages.net/article-real-wedding/678/3_2/1920/jpg/3928114.webp';
    @endphp
    <section class="hero-section relative min-h-screen flex items-center justify-center" 
             style="background-image: url('{{ $finalHeroImageUrl }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;"
             data-parallax="scroll" data-image-src="{{ $finalHeroImageUrl }}">
        <!-- Overlay pour la lisibilité - opacité réduite -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.1); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center" style="opacity: 0;">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase tracking-wide text-muted mb-4" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_1)
                                {{ $content->intro_1 }}
                            @else
                                💍 Le grand jour arrive 💍
                            @endif
                        </p>

                        <!-- Message personnalisé pour l'invité -->
                        @if($guest)
                        <div class="guest-welcome mb-4">
                            <p class="elegant-font text-dark" style="font-size: 1.1rem; font-style: italic;">
                                Cher(e) <strong>{{ $guest->first_name }} {{ $guest->last_name }}</strong>,
                            </p>
                            <p class="modern-font text-muted" style="font-size: 0.95rem;">
                                Nous avons le plaisir de vous inviter à partager ce moment spécial avec nous.
                            </p>
                        </div>
                        @endif

                        <!-- Noms du couple -->
                        <h1 class="romantic-font gradient-text mb-4" style="font-size: 4rem; line-height: 1.1;">
                            {{ $content->couple ?? 'Notre Mariage' }}
                        </h1>

                        <!-- Date de l'événement -->
                        @if($content->event_datetime)
                        <p class="modern-font text-dark mb-5" style="font-size: 1.3rem; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('l d F Y') }}
                        </p>
                        @elseif($event->event_date)
                        <p class="modern-font text-dark mb-5" style="font-size: 1.3rem; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                        </p>
                        @endif

                        <!-- Bouton d'ouverture -->
                        <button class="modern-button" onclick="scrollToContent()">
                            <i class="fas fa-envelope me-2"></i>
                            Ouvrir l'invitation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Invitation -->
    <section id="invitation-content" class="invitation-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5">
                        <!-- Petit texte décoratif -->
                        <p class="modern-font text-uppercase text-muted mb-4 text-center" style="letter-spacing: 0.3em; font-size: 0.9rem;">
                            @if($content->intro_2)
                                {{ $content->intro_2 }}
                            @else
                                💌 Invitation 💌
                            @endif
                        </p>

                        <!-- Texte principal -->
                        <div class="text-center mb-5">
                            @if($content->body_html)
                                <div class="elegant-font text-dark" style="font-size: 1.2rem; line-height: 1.8;">
                                    {!! $content->body_html !!}
                                </div>
                            @elseif($content->Content)
                                <div class="elegant-font text-dark" style="font-size: 1.2rem; line-height: 1.8;">
                                    {!! $content->Content !!}
                                </div>
                            @else
                                <p class="elegant-font text-dark mb-4" style="font-size: 1.2rem; line-height: 1.8;">
                                    C'est avec une immense joie et beaucoup d'amour que nous vous invitons
                                    à célébrer notre union sacrée.
                                </p>
                                <p class="elegant-font text-dark mb-4" style="font-size: 1.2rem; line-height: 1.8;">
                                    Rejoignez-nous pour partager ce moment unique de bonheur, d'émotion et de fête,
                                    entourés de ceux qui nous sont chers.
                                </p>
                            @endif
                            
                            <!-- Informations supplémentaires du contenu -->
                            @if($content->couple && $content->couple !== 'Notre Mariage')
                                <div class="couple-details mt-4 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px;">
                                    <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                        <strong>Couple :</strong> {{ $content->couple }}
                                    </p>
                                    @if($content->epoux || $content->epouse)
                                        <div class="row">
                                            @if($content->epoux)
                                            <div class="col-md-6">
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                                    <strong>Époux :</strong> {{ $content->epoux }}
                                                </p>
                                                @if($content->Famille_epoux)
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.8rem;">
                                                    <em>Famille de {{ $content->epoux }}</em>
                                                </p>
                                                @endif
                                            </div>
                                            @endif
                                            @if($content->epouse)
                                            <div class="col-md-6">
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.9rem;">
                                                    <strong>Épouse :</strong> {{ $content->epouse }}
                                                </p>
                                                @if($content->Famille_epouse)
                                                <p class="modern-font text-muted mb-1" style="font-size: 0.8rem;">
                                                    <em>Famille de {{ $content->epouse }}</em>
                                                </p>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Informations de l'événement -->
                        <div class="row text-center mb-5">
                            <div class="col-md-6 mb-3">
                                <h4 class="modern-font gradient-text mb-2">📅 Date & Heure</h4>
                                @if($content->event_datetime)
                                    <p class="elegant-font text-dark">
                                        {{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('l d F Y à H:i') }}
                                    </p>
                                @elseif($event->event_date)
                                    <p class="elegant-font text-dark">
                                        {{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('l d F Y') }}
                                    </p>
                                @endif
                                
                                @if($content->timezone)
                                    <p class="modern-font text-muted small">
                                        Fuseau horaire: {{ $content->timezone }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="modern-font gradient-text mb-2">📍 Lieu</h4>
                                @if($content->venue_name)
                                    <p class="elegant-font text-dark">{{ $content->venue_name }}</p>
                                    @if($content->venue_address_line1)
                                        <p class="modern-font text-muted small">{{ $content->venue_address_line1 }}</p>
                                    @endif
                                    @if($content->venue_address_line2)
                                        <p class="modern-font text-muted small">{{ $content->venue_address_line2 }}</p>
                                    @endif
                                    @if($content->venue_city)
                                        <p class="modern-font text-muted small">
                                            {{ $content->venue_city }}
                                            @if($content->venue_region), {{ $content->venue_region }}@endif
                                            @if($content->venue_country), {{ $content->venue_country }}@endif
                                        </p>
                                    @endif
                                @elseif($event->location)
                                    <p class="elegant-font text-dark">{{ $event->location }}</p>
                                @else
                                    <p class="elegant-font text-dark">Lieu à confirmer</p>
                                @endif
                            </div>
                        </div>

                        <!-- Informations spécifiques à l'invité -->
                        @if($guest)
                        <div class="guest-info-section mb-4 p-4" style="background: rgba(102, 126, 234, 0.1); border-radius: 15px; border: 1px solid rgba(102, 126, 234, 0.2);">
                            <h4 class="modern-font gradient-text mb-3 text-center">
                                <i class="fas fa-user-circle me-2"></i>Vos Informations
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Nom :</strong> {{ $guest->first_name }} {{ $guest->last_name }}
                                    </p>
                                    @if($guest->email)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Email :</strong> {{ $guest->email }}
                                    </p>
                                    @endif
                                    @if($guest->phone)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Téléphone :</strong> {{ $guest->phone }}
                                    </p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($guest->rsvp_status)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Statut RSVP :</strong> 
                                        <span class="badge badge-{{ $guest->rsvp_status == 'confirmed' ? 'success' : ($guest->rsvp_status == 'declined' ? 'danger' : 'warning') }}">
                                            @switch($guest->rsvp_status)
                                                @case('confirmed')
                                                    ✅ Confirmé
                                                    @break
                                                @case('declined')
                                                    ❌ Décliné
                                                    @break
                                                @case('pending')
                                                    ⏳ En attente
                                                    @break
                                                @default
                                                    {{ $guest->rsvp_status }}
                                            @endswitch
                                        </span>
                                    </p>
                                    @endif
                                    @if($guest->meal_choice)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Choix de repas :</strong> {{ $guest->meal_choice }}
                                    </p>
                                    @endif
                                    @if($guest->event_table_id)
                                    <p class="modern-font text-dark mb-2">
                                        <strong>Table :</strong> {{ $guest->eventTable->name ?? 'Table ' . $guest->event_table_id }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Boutons d'action personnalisables -->
                        <div class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                @php
                                    $ctaData = [];
                                    if ($content->cta) {
                                        if (is_string($content->cta)) {
                                            $ctaData = json_decode($content->cta, true) ?: [];
                                        } elseif (is_array($content->cta)) {
                                            $ctaData = $content->cta;
                                        }
                                    }
                                @endphp
                                
                                <!-- Bouton Confirmer présence - toujours visible si invité -->
                                @if($guest)
                                <form action="{{ route('guests.rsvp', $guest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="modern-button">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ $ctaData['rsvp']['label'] ?? 'Confirmer ma présence' }}
                                    </button>
                                </form>
                                @endif

                                <!-- Bouton Voir sur la carte - TOUJOURS visible -->
                                @php
                                    $mapUrl = $content->google_maps_url ?? $event->google_maps_url;
                                    if (!$mapUrl && $content->venue_lat && $content->venue_lng) {
                                        $mapUrl = "https://maps.google.com/maps?q={$content->venue_lat},{$content->venue_lng}";
                                    } elseif (!$mapUrl && $content->venue_name) {
                                        $mapUrl = "https://maps.google.com/maps?q=" . urlencode($content->venue_name . ', ' . ($content->venue_city ?? ''));
                                    } else {
                                        $mapUrl = $mapUrl ?? 'https://maps.google.com';
                                    }
                                @endphp
                                <a href="{{ $mapUrl }}" target="_blank" class="modern-button map-button">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $ctaData['map']['label'] ?? 'Voir sur la carte' }}
                                </a>

                                <!-- Bouton Télécharger PDF - toujours visible -->
                                <a href="{{ route('invitation.download', $invitation->unique_code) }}" class="modern-button download-button">
                                    <i class="fas fa-download me-2"></i>
                                    {{ $ctaData['download']['label'] ?? 'Télécharger PDF' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Livre d'Or -->
    @if($content->guestbook_enabled)
    <section class="guestbook-section py-5" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.4); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center">
                        <h2 class="modern-font gradient-text mb-4" style="font-size: 2.5rem;">
                            {{ $content->guestbook_title ?? 'Livre d\'or' }}
                        </h2>
                        <p class="elegant-font text-dark mb-5" style="font-size: 1.1rem;">
                            {{ $content->guestbook_subtitle ?? 'Laissez-nous un mot, une pensée ou une bénédiction pour marquer ce moment unique.' }}
                        </p>

                        <!-- Formulaire d'ajout -->
                        <div class="text-start bg-light rounded-3 p-4">
                            <h3 class="modern-font text-dark mb-3">
                                @if($guest)
                                    Écrire un message - {{ $guest->first_name }} {{ $guest->last_name }}
                                @else
                                    Écrire un message
                                @endif
                            </h3>
                            <form action="{{ route('book.store', ['unique_code' => $unique_code]) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-dark">Votre message</label>
                                    <textarea rows="4" name="message" class="form-control"
                                        placeholder="Écrivez votre message ici..."></textarea>
                                </div>
                                <button type="submit" class="modern-button">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Envoyer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Programme/Schedule -->
    @if($content->schedule)
    <section class="schedule-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="invitation-card p-5 text-center">
                        <div class="schedule-header mb-5">
                            <h2 class="modern-font gradient-text mb-3" style="font-size: 2.8rem; font-weight: 700;">
                                <i class="fas fa-calendar-alt me-3" style="color: #e11d48;"></i>Programme de la Journée
                        </h2>
                            <div class="schedule-subtitle">
                                <p class="elegant-font text-muted" style="font-size: 1.2rem; font-style: italic;">
                                    Découvrez le déroulement de cette journée exceptionnelle
                                </p>
                                <div class="schedule-divider">
                                    <div class="divider-line"></div>
                                    <i class="fas fa-heart" style="color: #e11d48; font-size: 1.2rem;"></i>
                                    <div class="divider-line"></div>
                                </div>
                            </div>
                        </div>
                        
                        @php
                            $schedule = [];
                            if ($content->schedule) {
                                if (is_string($content->schedule)) {
                                    $schedule = json_decode($content->schedule, true) ?: [];
                                } elseif (is_array($content->schedule)) {
                                    $schedule = $content->schedule;
                                }
                            }
                        @endphp
                        
                        @if($schedule && is_array($schedule) && count($schedule) > 0)
                            <div class="timeline-container">
                                <div class="timeline">
                                    @foreach($schedule as $index => $item)
                                        @php
                                            $time = '';
                                            $event = '';
                                            
                                            if (is_array($item) && isset($item['time']) && isset($item['event'])) {
                                                $time = $item['time'] ?? '';
                                                $event = $item['event'] ?? '';
                                            } elseif (is_string($item)) {
                                                $scheduleKeys = array_keys($schedule);
                                                $time = $scheduleKeys[$index] ?? '';
                                                $event = $item;
                                            }
                                        @endphp
                                        
                                        @if($time && $event)
                                            <div class="timeline-item {{ $index % 2 == 0 ? 'timeline-left' : 'timeline-right' }}">
                                                <div class="timeline-marker">
                                                    <div class="timeline-icon">
                                                        @if($event && (strpos(strtolower($event), 'cérémonie') !== false || strpos(strtolower($event), 'mariage') !== false))
                                                            <i class="fas fa-heart"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'cocktail') !== false || strpos(strtolower($event), 'apéritif') !== false))
                                                            <i class="fas fa-glass-cheers"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'dîner') !== false || strpos(strtolower($event), 'repas') !== false))
                                                            <i class="fas fa-utensils"></i>
                                                        @elseif($event && (strpos(strtolower($event), 'danse') !== false || strpos(strtolower($event), 'soirée') !== false))
                                                            <i class="fas fa-music"></i>
                                                        @else
                                                            <i class="fas fa-clock"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="timeline-time">{{ $time }}</div>
                                                    <div class="timeline-event">{{ $event }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="no-schedule">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-plus text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h4 class="text-muted">Programme à venir</h4>
                                    <p class="text-muted">Le programme détaillé sera bientôt disponible</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Préférences de Boissons -->
    @if($content->drinks_enabled && $eventDrinks && $eventDrinks->count() > 0)
    <section class="drinks-section" style="background-image: url('https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1920&h=1080&fit=crop&crop=center&auto=format&q=80'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay sombre léger pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="invitation-card p-5 text-center">
                        @php
                            $drinksData = [];
                            if ($content->drinks) {
                                if (is_string($content->drinks)) {
                                    $drinksData = json_decode($content->drinks, true) ?: [];
                                } elseif (is_array($content->drinks)) {
                                    $drinksData = $content->drinks;
                                }
                            }
                        @endphp
                        <h4 class="romantic-font gradient-text mb-4" style="font-size: 2.5rem;">
                            {{ $drinksData['title'] ?? 'Choisissez vos boissons' }}
                        </h4>
                        
                        <form action="{{ route('guest_drink_choices.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="guest_id" value="{{ $guest->id ?? '' }}">
                            <input type="hidden" name="unique_code" value="{{ $unique_code }}">

                            <div class="mb-4">
                                <div class="d-flex flex-wrap justify-content-center">
                                    @foreach ($eventDrinks as $drink)
                                        <div class="drink-option">
                                            <!-- Checkbox masqué -->
                                            <input type="checkbox" 
                                                   class="btn-check" 
                                                   name="drinks[]" 
                                                   value="{{ $drink->id }}" 
                                                   id="drink{{ $drink->id }}" 
                                                   autocomplete="off">

                                            <!-- Le label devient le bouton stylisé -->
                                            <label class="btn btn-outline-primary rounded-pill px-4 py-2 drink-label" 
                                                   for="drink{{ $drink->id }}">
                                                {{ $drink->drink->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="modern-button">
                                <i class="fas fa-check me-2"></i>
                                {{ $drinksData['submit_label'] ?? 'Valider mes choix' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

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
                    <div class="invitation-card p-5 text-center">
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

    <!-- Footer -->
    <footer class="footer-section py-5">
        <div class="container text-center">
            <p class="romantic-font text-white mb-2" style="font-size: 2rem;">{{ $content->couple ?? 'Notre Mariage' }}</p>
            @if($content->event_datetime)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('d F Y') }}</p>
            @elseif($event->event_date)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('d F Y') }}</p>
            @endif
        </div>
    </footer>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function scrollToContent() {
            document.getElementById('invitation-content').scrollIntoView({
                behavior: 'smooth'
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Animation d'apparition de la carte de contenu hero au défilement
            const heroCard = document.querySelector('.hero-section .invitation-card');
            const heroSection = document.querySelector('.hero-section');
            
            // S'assurer que la carte est cachée au chargement
            if (heroCard) {
                heroCard.style.opacity = '0';
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
                    const defaultImage = 'https://cdn0.mariages.net/article-real-wedding/678/3_2/1920/jpg/3928114.webp';
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
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Afficher la carte avec fade in
                            heroCard.classList.add('show');
                        } else {
                            // Masquer la carte avec fade out
                            heroCard.classList.remove('show');
                        }
                    });
                }, {
                    threshold: 0.2 // Déclenche quand 20% de la section est visible
                });
                
                observer.observe(heroSection);
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
    </script>
</body>
</html>