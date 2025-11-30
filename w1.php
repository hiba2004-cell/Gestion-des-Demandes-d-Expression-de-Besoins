<?php
/**
 * Welcome Page - Gestion des Besoins
 * A stunning, extraordinary introduction page
 */
session_start();

// Check if user is already logged in
$isLoggedIn = isset($_SESSION['user_id']);
$redirectUrl = $isLoggedIn ? 'index.php' : 'login.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme innovante de gestion des demandes d'expression de besoins - Simplifiez, automatisez, excellez.">
    <title>BesoinsFlow | Gestion Intelligente des Demandes</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --color-bg: #050505;
            --color-bg-secondary: #0a0a0a;
            --color-surface: #111111;
            --color-surface-elevated: #1a1a1a;
            --color-border: rgba(255, 255, 255, 0.08);
            --color-text: #fafafa;
            --color-text-secondary: #a0a0a0;
            --color-text-muted: #666666;
            --color-accent: #6366f1;
            --color-accent-light: #818cf8;
            --color-accent-glow: rgba(99, 102, 241, 0.4);
            --color-success: #10b981;
            --color-warning: #f59e0b;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-display: 'Playfair Display', Georgia, serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-sans);
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 40% 60%, rgba(59, 130, 246, 0.08) 0%, transparent 40%);
            animation: bgPulse 15s ease-in-out infinite;
        }

        @keyframes bgPulse {
            0%, 100% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
            33% { transform: translate(2%, 2%) rotate(1deg); opacity: 0.8; }
            66% { transform: translate(-1%, 1%) rotate(-1deg); opacity: 0.9; }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--color-accent);
            border-radius: 50%;
            opacity: 0;
            animation: floatParticle 20s infinite;
        }

        @keyframes floatParticle {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1.5rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            background: rgba(5, 5, 5, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 3rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--color-text);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--color-accent), var(--color-accent-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: 0 4px 20px var(--color-accent-glow);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .nav-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--color-accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--color-text);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-accent), var(--color-accent-light));
            color: white;
            box-shadow: 0 4px 20px var(--color-accent-glow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--color-accent-glow);
        }

        .btn-secondary {
            background: var(--color-surface-elevated);
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }

        .btn-secondary:hover {
            background: var(--color-surface);
            border-color: var(--color-accent);
        }

        .btn-ghost {
            background: transparent;
            color: var(--color-text);
            padding: 0.75rem 1.25rem;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.875rem;
            color: var(--color-accent-light);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-badge i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 600;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease-out 0.1s backwards;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, var(--color-accent-light) 0%, #c4b5fd 50%, var(--color-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1.125rem, 2vw, 1.375rem);
            color: var(--color-text-secondary);
            max-width: 600px;
            margin-bottom: 3rem;
            line-height: 1.7;
            animation: fadeInUp 0.8s ease-out 0.2s backwards;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeInUp 0.8s ease-out 0.3s backwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        .scroll-indicator i {
            font-size: 1.25rem;
        }

        /* Stats Section */
        .stats-section {
            padding: 4rem 2rem;
            background: var(--color-bg-secondary);
            border-top: 1px solid var(--color-border);
            border-bottom: 1px solid var(--color-border);
        }

        .stats-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--color-text), var(--color-text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--color-text-secondary);
            font-size: 0.95rem;
        }

        /* Features Section */
        .features-section {
            padding: 8rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-tag {
            display: inline-block;
            color: var(--color-accent-light);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            color: var(--color-text-secondary);
            font-size: 1.125rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--color-accent), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.1));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--color-accent-light);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .feature-description {
            color: var(--color-text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* Workflow Section */
        .workflow-section {
            padding: 8rem 2rem;
            background: var(--color-bg-secondary);
            position: relative;
            overflow: hidden;
        }

        .workflow-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .workflow-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .workflow-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 4rem;
        }

        .workflow-steps::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: linear-gradient(90deg, var(--color-border), var(--color-accent), var(--color-border));
        }

        .workflow-step {
            text-align: center;
            flex: 1;
            padding: 0 1rem;
            position: relative;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: var(--color-surface);
            border: 2px solid var(--color-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-accent-light);
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .workflow-step:hover .step-number {
            background: var(--color-accent);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 30px var(--color-accent-glow);
        }

        .step-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .step-description {
            color: var(--color-text-secondary);
            font-size: 0.9rem;
        }

        /* Testimonials / Trust Section */
        .trust-section {
            padding: 6rem 2rem;
            text-align: center;
        }

        .trust-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4rem;
            flex-wrap: wrap;
            opacity: 0.5;
            margin-bottom: 3rem;
        }

        .trust-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-text-secondary);
            letter-spacing: -0.02em;
        }

        .trust-text {
            font-size: 1.125rem;
            color: var(--color-text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* CTA Section */
        .cta-section {
            padding: 8rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent, rgba(99, 102, 241, 0.05), transparent);
            pointer-events: none;
        }

        .cta-card {
            max-width: 800px;
            margin: 0 auto;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 32px;
            padding: 4rem;
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            color: var(--color-text-secondary);
            font-size: 1.125rem;
            margin-bottom: 2rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer */
        .footer {
            padding: 4rem 2rem 2rem;
            border-top: 1px solid var(--color-border);
            background: var(--color-bg-secondary);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-text {
            color: var(--color-text-muted);
            font-size: 0.875rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--color-text);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .workflow-steps {
                flex-direction: column;
                gap: 2rem;
            }

            .workflow-steps::before {
                display: none;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 1.5rem;
            }

            .nav-links {
                display: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 6rem 1.5rem 4rem;
            }

            .cta-card {
                padding: 2.5rem 1.5rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animations on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Glassmorphism card variant */
        .glass-card {
            background: rgba(17, 17, 17, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Glow effect */
        .glow {
            box-shadow: 
                0 0 20px var(--color-accent-glow),
                0 0 40px rgba(99, 102, 241, 0.2),
                0 0 60px rgba(99, 102, 241, 0.1);
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
    
    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <a href="#" class="logo">
            <div class="logo-icon">B</div>
            <span class="logo-text">BesoinsFlow</span>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Fonctionnalites</a>
            <a href="#workflow" class="nav-link">Comment ca marche</a>
            <a href="#about" class="nav-link">A propos</a>
            <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                <?php echo $isLoggedIn ? 'Mon Espace' : 'Commencer'; ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-badge">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>Nouvelle generation de gestion des besoins</span>
        </div>
        
        <h1 class="hero-title">
            Transformez vos<br>
            <span class="gradient-text">demandes en actions</span>
        </h1>
        
        <p class="hero-subtitle">
            Une plateforme intelligente qui simplifie la gestion des expressions de besoins, 
            de la soumission a la validation, avec une tracabilite complete.
        </p>
        
        <div class="hero-cta">
            <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                <?php echo $isLoggedIn ? 'Acceder a mon espace' : 'Demarrer gratuitement'; ?>
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#features" class="btn btn-secondary">
                <i class="bi bi-play-circle"></i>
                Decouvrir
            </a>
        </div>
        
        <div class="scroll-indicator">
            <span>Defiler</span>
            <i class="bi bi-chevron-down"></i>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item reveal">
                <div class="stat-value">98%</div>
                <div class="stat-label">Temps de traitement reduit</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-value">500+</div>
                <div class="stat-label">Demandes gerees par mois</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-value">100%</div>
                <div class="stat-label">Tracabilite garantie</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-value">24/7</div>
                <div class="stat-label">Acces a la plateforme</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="section-header reveal">
            <span class="section-tag">Fonctionnalites</span>
            <h2 class="section-title">Tout ce dont vous avez besoin</h2>
            <p class="section-subtitle">
                Une suite complete d'outils pour gerer efficacement toutes vos demandes d'expression de besoins.
            </p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-send-check"></i>
                </div>
                <h3 class="feature-title">Soumission Simplifiee</h3>
                <p class="feature-description">
                    Creez et soumettez vos demandes en quelques clics avec notre interface intuitive et moderne.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h3 class="feature-title">Workflow Automatise</h3>
                <p class="feature-description">
                    Processus de validation hierarchique automatique avec notifications en temps reel.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="feature-title">Tableau de Bord</h3>
                <p class="feature-description">
                    Visualisez l'etat de toutes vos demandes avec des statistiques detaillees et des rapports.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="feature-title">Notifications</h3>
                <p class="feature-description">
                    Restez informe a chaque etape avec des alertes personnalisees par email ou en application.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="feature-title">Securite Renforcee</h3>
                <p class="feature-description">
                    Authentification securisee et gestion des roles pour proteger vos donnees sensibles.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3 class="feature-title">Historique Complet</h3>
                <p class="feature-description">
                    Tracabilite totale de chaque demande avec historique des actions et commentaires.
                </p>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="workflow-section" id="workflow">
        <div class="workflow-container">
            <div class="section-header reveal">
                <span class="section-tag">Processus</span>
                <h2 class="section-title">Comment ca fonctionne</h2>
                <p class="section-subtitle">
                    Un processus fluide en 5 etapes pour gerer vos demandes de bout en bout.
                </p>
            </div>
            
            <div class="workflow-steps">
                <div class="workflow-step reveal">
                    <div class="step-number">1</div>
                    <h4 class="step-title">Soumission</h4>
                    <p class="step-description">L'employe cree et soumet sa demande</p>
                </div>
                
                <div class="workflow-step reveal">
                    <div class="step-number">2</div>
                    <h4 class="step-title">Notification</h4>
                    <p class="step-description">Le chef hierarchique est alerte</p>
                </div>
                
                <div class="workflow-step reveal">
                    <div class="step-number">3</div>
                    <h4 class="step-title">Validation</h4>
                    <p class="step-description">Approbation ou rejet avec commentaire</p>
                </div>
                
                <div class="workflow-step reveal">
                    <div class="step-number">4</div>
                    <h4 class="step-title">Traitement</h4>
                    <p class="step-description">L'administrateur traite la demande</p>
                </div>
                
                <div class="workflow-step reveal">
                    <div class="step-number">5</div>
                    <h4 class="step-title">Cloture</h4>
                    <p class="step-description">Notification finale au demandeur</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="trust-section" id="about">
        <div class="reveal">
            <div class="trust-logos">
                <span class="trust-logo">RH</span>
                <span class="trust-logo">DSI</span>
                <span class="trust-logo">Achats</span>
                <span class="trust-logo">Direction</span>
                <span class="trust-logo">Services</span>
            </div>
            <p class="trust-text">
                Utilise par les directions RH, DSI, Achats et tous les services de l'entreprise 
                pour une gestion unifiee et transparente des besoins.
            </p>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-card glass-card reveal">
            <h2 class="cta-title">Pret a transformer votre gestion ?</h2>
            <p class="cta-subtitle">
                Rejoignez les organisations qui ont digitalise leur processus de gestion des besoins.
            </p>
            <div class="cta-buttons">
                <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary glow">
                    <?php echo $isLoggedIn ? 'Acceder a la plateforme' : 'Commencer maintenant'; ?>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="login.php" class="btn btn-ghost">
                    <i class="bi bi-person"></i>
                    <?php echo $isLoggedIn ? 'Mon profil' : 'Se connecter'; ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-text">
                &copy; <?php echo date('Y'); ?> BesoinsFlow. Tous droits reserves.
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Confidentialite</a>
                <a href="#" class="footer-link">Conditions</a>
                <a href="#" class="footer-link">Contact</a>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Create floating particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }

        // Reveal elements on scroll
        const revealElements = document.querySelectorAll('.reveal');
        
        const revealOnScroll = () => {
            const windowHeight = window.innerHeight;
            
            revealElements.forEach((element, index) => {
                const elementTop = element.getBoundingClientRect().top;
                const revealPoint = 150;
                
                if (elementTop < windowHeight - revealPoint) {
                    setTimeout(() => {
                        element.classList.add('active');
                    }, index * 100);
                }
            });
        };

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const hero = document.querySelector('.hero');
            if (hero && scrolled < window.innerHeight) {
                hero.style.transform = `translateY(${scrolled * 0.3}px)`;
                hero.style.opacity = 1 - (scrolled / window.innerHeight) * 0.5;
            }
        });

        // Add magnetic effect to buttons
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                btn.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translate(0, 0)';
            });
        });

        // Counter animation for stats
        const animateCounters = () => {
            const counters = document.querySelectorAll('.stat-value');
            
            counters.forEach(counter => {
                const text = counter.textContent;
                const hasPercent = text.includes('%');
                const hasPlus = text.includes('+');
                const isTime = text.includes('/');
                
                if (isTime) return;
                
                let target = parseInt(text.replace(/\D/g, ''));
                if (isNaN(target)) return;
                
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    let display = Math.floor(current);
                    if (hasPercent) display += '%';
                    if (hasPlus) display += '+';
                    counter.textContent = display;
                }, 30);
            });
        };

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.stats-section');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>
</body>
</html>
