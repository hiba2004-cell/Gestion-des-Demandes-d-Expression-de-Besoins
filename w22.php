<?php
/**
 * Welcome Page - Gestion des Besoins
 * Elegant light theme with premium animations
 */
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$redirectUrl = $isLoggedIn ? 'index.php' : 'login.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme innovante de gestion des demandes d'expression de besoins">
    <title>BesoinsFlow | Gestion Intelligente des Demandes</title>
    
    <!-- Updated fonts to elegant combination: Outfit + Inter for better readability -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* Complete light theme color palette */
        :root {
            --color-bg: #fafafa;
            --color-surface: #ffffff;
            --color-surface-elevated: #ffffff;
            --color-border: rgba(0, 0, 0, 0.08);
            --color-border-strong: rgba(0, 0, 0, 0.12);
            --color-text: #1a1a2e;
            --color-text-secondary: #64748b;
            --color-text-muted: #94a3b8;
            --color-primary: #6366f1;
            --color-primary-light: #818cf8;
            --color-primary-dark: #4f46e5;
            --color-primary-dim: rgba(99, 102, 241, 0.08);
            --color-secondary: #0ea5e9;
            --color-tertiary: #f43f5e;
            --color-accent: #10b981;
            --color-accent-glow: rgba(99, 102, 241, 0.3);
            --color-warm: #f8fafc;
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 24px 60px rgba(0, 0, 0, 0.1);
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
            font-family: var(--font-body);
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.6;
            overflow-x: hidden;
            cursor: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Updated cursor for light theme */
        .cursor {
            width: 20px;
            height: 20px;
            border: 2px solid var(--color-primary);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 10000;
            transition: transform 0.15s ease, background 0.15s ease;
        }

        .cursor-dot {
            width: 6px;
            height: 6px;
            background: var(--color-primary);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 10001;
        }

        .cursor.hover {
            transform: scale(2);
            background: rgba(99, 102, 241, 0.15);
        }

        /* Soft animated gradient background for light theme */
        .mesh-gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -3;
            background: 
                radial-gradient(ellipse 80% 50% at 20% 40%, rgba(99, 102, 241, 0.12), transparent),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(14, 165, 233, 0.1), transparent),
                radial-gradient(ellipse 50% 40% at 60% 80%, rgba(244, 63, 94, 0.08), transparent),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            animation: meshMove 20s ease-in-out infinite;
        }

        @keyframes meshMove {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Soft light beams for light theme */
        .aurora {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            pointer-events: none;
            opacity: 0.5;
        }

        .aurora-beam {
            position: absolute;
            width: 40%;
            height: 150%;
            filter: blur(120px);
            opacity: 0.4;
            animation: auroraMove 20s ease-in-out infinite;
        }

        .aurora-beam:nth-child(1) {
            background: linear-gradient(180deg, transparent, rgba(99, 102, 241, 0.2), transparent);
            left: -10%;
            animation-delay: 0s;
        }

        .aurora-beam:nth-child(2) {
            background: linear-gradient(180deg, transparent, rgba(14, 165, 233, 0.15), transparent);
            left: 40%;
            animation-delay: -7s;
            animation-duration: 25s;
        }

        .aurora-beam:nth-child(3) {
            background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.12), transparent);
            left: 70%;
            animation-delay: -14s;
            animation-duration: 18s;
        }

        @keyframes auroraMove {
            0%, 100% { transform: translateY(-20%) rotate(-10deg); }
            50% { transform: translateY(-40%) rotate(10deg); }
        }

        /* Subtle grid for light theme */
        .grid-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
        }

        /* Soft orbs for light theme */
        .orbs-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            animation: orbFloat 25s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -150px;
            right: -150px;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation-delay: -8s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            top: 40%;
            left: 30%;
            animation-delay: -16s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -40px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Floating Particles */
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--color-primary);
            border-radius: 50%;
            opacity: 0.3;
            animation: particleFloat 15s linear infinite;
        }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.3; }
            90% { opacity: 0.3; }
            100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
        }

        /* Light theme navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1.25rem 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1rem 4rem;
            box-shadow: var(--shadow-md);
            border-bottom: 1px solid var(--color-border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
            color: var(--color-text);
        }

        .logo-mark {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .logo-mark::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            z-index: -1;
            opacity: 0.5;
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.05); opacity: 0.3; }
        }

        .logo-mark span {
            color: white;
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--color-text);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .nav-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .nav-link:hover {
            color: var(--color-text);
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Light theme buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: none;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-primary:hover::before {
            transform: translateX(100%);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        .btn-outline {
            background: var(--color-surface);
            color: var(--color-text);
            border: 1px solid var(--color-border-strong);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline:hover {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
            color: var(--color-primary);
        }

        .btn-large {
            padding: 1.125rem 2.25rem;
            font-size: 1rem;
            border-radius: 14px;
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

        .hero-content {
            max-width: 900px;
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 1.25rem;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-eyebrow .dot {
            width: 8px;
            height: 8px;
            background: var(--color-accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.85); }
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.75rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
            color: var(--color-text);
        }

        .hero-title .line {
            display: block;
            overflow: hidden;
        }

        .hero-title .word {
            display: inline-block;
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) backwards;
        }

        .hero-title .line:nth-child(1) .word { animation-delay: 0.1s; }
        .hero-title .line:nth-child(2) .word { animation-delay: 0.2s; }

        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 50%, var(--color-tertiary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 6s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .hero-subtitle {
            font-size: clamp(1.05rem, 2vw, 1.25rem);
            color: var(--color-text-secondary);
            max-width: 580px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            animation: fadeInUp 0.8s ease-out 0.3s backwards;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.4s backwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 2.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            animation: fadeInUp 0.8s ease-out 0.6s backwards;
        }

        .scroll-mouse {
            width: 26px;
            height: 40px;
            border: 2px solid var(--color-border-strong);
            border-radius: 13px;
            position: relative;
        }

        .scroll-mouse::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 8px;
            background: var(--color-primary);
            border-radius: 2px;
            animation: scrollWheel 2s ease-in-out infinite;
        }

        @keyframes scrollWheel {
            0%, 100% { transform: translateX(-50%) translateY(0); opacity: 1; }
            50% { transform: translateX(-50%) translateY(10px); opacity: 0.3; }
        }

        .scroll-text {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        /* Stats Section */
        .stats-section {
            padding: 5rem 2rem;
            position: relative;
        }

        .stats-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: var(--shadow-sm);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--color-primary);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: 3rem;
            font-weight: 800;
            color: var(--color-text);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--color-text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Features Section */
        .features-section {
            padding: 8rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-tag {
            display: inline-block;
            color: var(--color-primary);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            color: var(--color-text-secondary);
            font-size: 1.1rem;
            max-width: 550px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
            border-color: transparent;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 24px;
            padding: 1px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover::after {
            opacity: 1;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: white;
            position: relative;
        }

        .feature-card:nth-child(1) .feature-icon { background: linear-gradient(135deg, #6366f1, #818cf8); }
        .feature-card:nth-child(2) .feature-icon { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
        .feature-card:nth-child(3) .feature-icon { background: linear-gradient(135deg, #10b981, #34d399); }
        .feature-card:nth-child(4) .feature-icon { background: linear-gradient(135deg, #f43f5e, #fb7185); }
        .feature-card:nth-child(5) .feature-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .feature-card:nth-child(6) .feature-icon { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

        .feature-title {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--color-text);
        }

        .feature-description {
            color: var(--color-text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Workflow Section */
        .workflow-section {
            padding: 8rem 2rem;
            background: linear-gradient(180deg, transparent, var(--color-warm), transparent);
        }

        .workflow-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .workflow-steps {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 4rem;
        }

        .workflow-step {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 2rem;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            transition: all 0.4s ease;
            cursor: none;
            box-shadow: var(--shadow-sm);
        }

        .workflow-step:hover,
        .workflow-step.active {
            border-color: var(--color-primary);
            box-shadow: var(--shadow-lg);
            transform: translateX(10px);
        }

        .workflow-step.active {
            background: var(--color-primary-dim);
        }

        .step-number {
            width: 50px;
            height: 50px;
            min-width: 50px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .step-content h4 {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.375rem;
            color: var(--color-text);
        }

        .step-content p {
            color: var(--color-text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* CTA Section */
        .cta-section {
            padding: 8rem 2rem;
            position: relative;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
        }

        .cta-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 32px;
            padding: 4rem 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .cta-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-secondary), var(--color-tertiary));
        }

        .cta-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            width: 60%;
            height: 100%;
            transform: translateX(-50%);
            background: radial-gradient(ellipse at top, var(--color-primary-dim), transparent 70%);
            pointer-events: none;
        }

        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            color: var(--color-text);
        }

        .cta-description {
            color: var(--color-text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        /* Footer */
        .footer {
            padding: 4rem 2rem 2rem;
            border-top: 1px solid var(--color-border);
            background: var(--color-surface);
        }

        .footer-content {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1rem;
        }

        .footer-brand span {
            font-family: var(--font-display);
            font-weight: 600;
            color: var(--color-text);
        }

        .footer-links {
            display: flex;
            gap: 2.5rem;
        }

        .footer-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--color-primary);
        }

        .footer-copyright {
            color: var(--color-text-muted);
            font-size: 0.85rem;
        }

        /* Animations */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .features-grid {
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .workflow-step {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .cursor, .cursor-dot {
                display: none;
            }

            body {
                cursor: auto;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Cursor -->
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <!-- Background Effects -->
    <div class="mesh-gradient-bg"></div>
    
    <div class="aurora">
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
    </div>
    
    <div class="grid-lines"></div>
    
    <div class="orbs-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="particles-container" id="particles"></div>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <a href="#" class="logo">
            <div class="logo-mark">
                <span>B</span>
            </div>
            <span class="logo-text">BesoinsFlow</span>
        </a>
        
        <div class="nav-links">
            <a href="#features" class="nav-link">Fonctionnalités</a>
            <a href="#workflow" class="nav-link">Processus</a>
            <a href="#contact" class="nav-link">Contact</a>
            <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                <?php echo $isLoggedIn ? 'Tableau de bord' : 'Connexion'; ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-eyebrow">
                <span class="dot"></span>
                Nouvelle plateforme disponible
            </div>
            
            <h1 class="hero-title">
                <span class="line"><span class="word">Simplifiez la gestion</span></span>
                <span class="line"><span class="word">de vos <span class="gradient-text">besoins</span></span></span>
            </h1>
            
            <p class="hero-subtitle">
                Une plateforme intuitive pour soumettre, suivre et valider vos demandes d'expression de besoins. Fluidifiez vos processus internes.
            </p>
            
            <div class="hero-cta">
                <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary btn-large">
                    Commencer maintenant
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="#features" class="btn btn-outline btn-large">
                    <i class="bi bi-play-circle"></i>
                    Découvrir
                </a>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <div class="scroll-mouse"></div>
            <span class="scroll-text">Défiler</span>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-value" data-count="95">0</div>
                <div class="stat-label">Taux de satisfaction</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-value" data-count="72">0</div>
                <div class="stat-label">Heures économisées</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-value" data-count="100">0</div>
                <div class="stat-label">Demandes traitées</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-value" data-count="24">0</div>
                <div class="stat-label">Délai moyen (h)</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="section-header reveal">
            <span class="section-tag">Fonctionnalités</span>
            <h2 class="section-title">Tout ce dont vous avez besoin</h2>
            <p class="section-subtitle">
                Une suite d'outils puissants pour optimiser la gestion de vos demandes internes
            </p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-send"></i>
                </div>
                <h3 class="feature-title">Soumission simple</h3>
                <p class="feature-description">
                    Créez vos demandes en quelques clics avec un formulaire intuitif et guidé.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3 class="feature-title">Suivi en temps réel</h3>
                <p class="feature-description">
                    Visualisez l'avancement de vos demandes à chaque étape du processus.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="feature-title">Validation hiérarchique</h3>
                <p class="feature-description">
                    Workflow de validation automatisé respectant votre structure organisationnelle.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="feature-title">Notifications</h3>
                <p class="feature-description">
                    Alertes automatiques pour ne jamais manquer une mise à jour importante.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <h3 class="feature-title">Tableaux de bord</h3>
                <p class="feature-description">
                    Statistiques et reporting pour piloter efficacement vos processus.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="feature-title">Multi-rôles</h3>
                <p class="feature-description">
                    Gestion des droits adaptée : demandeur, validateur, administrateur.
                </p>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="workflow-section" id="workflow">
        <div class="workflow-container">
            <div class="section-header reveal">
                <span class="section-tag">Processus</span>
                <h2 class="section-title">Un workflow fluide</h2>
                <p class="section-subtitle">
                    De la création à la réalisation, suivez chaque étape de vos demandes
                </p>
            </div>
            
            <div class="workflow-steps">
                <div class="workflow-step reveal active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Création de la demande</h4>
                        <p>L'employé remplit le formulaire avec le type, la description et le niveau d'urgence.</p>
                    </div>
                </div>
                
                <div class="workflow-step reveal" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Validation hiérarchique</h4>
                        <p>Le responsable examine la demande et décide de sa validation ou de son rejet.</p>
                    </div>
                </div>
                
                <div class="workflow-step reveal" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Traitement administratif</h4>
                        <p>L'administrateur prend en charge la demande validée et coordonne sa réalisation.</p>
                    </div>
                </div>
                
                <div class="workflow-step reveal" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Clôture et notification</h4>
                        <p>La demande est marquée comme traitée et le demandeur est automatiquement notifié.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact">
        <div class="cta-container">
            <div class="cta-card reveal">
                <h2 class="cta-title">Prêt à transformer vos processus ?</h2>
                <p class="cta-description">
                    Rejoignez les équipes qui ont déjà adopté BesoinsFlow pour une gestion simplifiée et efficace.
                </p>
                <div class="cta-buttons">
                    <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary btn-large">
                        <?php echo $isLoggedIn ? 'Accéder au tableau de bord' : 'Commencer gratuitement'; ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">B</div>
                <span>BesoinsFlow</span>
            </div>
            
            <div class="footer-links">
                <a href="#features" class="footer-link">Fonctionnalités</a>
                <a href="#workflow" class="footer-link">Processus</a>
                <a href="#contact" class="footer-link">Contact</a>
                <a href="login.php" class="footer-link">Connexion</a>
            </div>
            
            <div class="footer-copyright">
                © <?php echo date('Y'); ?> BesoinsFlow. Tous droits réservés.
            </div>
        </div>
    </footer>

    <script>
        // Custom Cursor
        const cursor = document.querySelector('.cursor');
        const cursorDot = document.querySelector('.cursor-dot');
        
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        let dotX = 0, dotY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        function animateCursor() {
            cursorX += (mouseX - cursorX) * 0.15;
            cursorY += (mouseY - cursorY) * 0.15;
            dotX += (mouseX - dotX) * 0.35;
            dotY += (mouseY - dotY) * 0.35;
            
            cursor.style.left = cursorX - 10 + 'px';
            cursor.style.top = cursorY - 10 + 'px';
            cursorDot.style.left = dotX - 3 + 'px';
            cursorDot.style.top = dotY - 3 + 'px';
            
            requestAnimationFrame(animateCursor);
        }
        animateCursor();
        
        // Cursor hover effect
        document.querySelectorAll('a, button, .btn, .feature-card, .workflow-step, .stat-card').forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
        });
        
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Create particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = 15 + Math.random() * 10 + 's';
            particle.style.opacity = 0.1 + Math.random() * 0.2;
            particlesContainer.appendChild(particle);
        }
        
        // Reveal animations on scroll
        const reveals = document.querySelectorAll('.reveal');
        
        function revealOnScroll() {
            reveals.forEach(el => {
                const elementTop = el.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementTop < windowHeight - 100) {
                    el.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll();
        
        // Counter animation
        const counters = document.querySelectorAll('.stat-value');
        let countersAnimated = false;
        
        function animateCounters() {
            if (countersAnimated) return;
            
            counters.forEach(counter => {
                const rect = counter.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    countersAnimated = true;
                    const target = parseInt(counter.dataset.count);
                    const suffix = counter.dataset.count.includes('%') ? '%' : (counter.dataset.count.includes('+') ? '+' : '');
                    let current = 0;
                    const increment = target / 60;
                    const duration = 2000;
                    const stepTime = duration / 60;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target + suffix;
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current) + suffix;
                        }
                    }, stepTime);
                }
            });
        }
        
        window.addEventListener('scroll', animateCounters);
        animateCounters();
        
        // Workflow step auto-cycle
        const workflowSteps = document.querySelectorAll('.workflow-step');
        let currentStep = 0;
        
        function cycleWorkflowSteps() {
            workflowSteps.forEach(step => step.classList.remove('active'));
            workflowSteps[currentStep].classList.add('active');
            currentStep = (currentStep + 1) % workflowSteps.length;
        }
        
        setInterval(cycleWorkflowSteps, 3000);
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
