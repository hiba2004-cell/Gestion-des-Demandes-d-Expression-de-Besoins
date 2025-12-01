<?php
/**
 * Welcome Page - Gestion des Besoins
 * An extraordinary, breathtaking introduction page with next-level animations
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Syne:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
    :root {
        --color-bg: #fffff;
        --color-surface: #0a0a0a;
        --color-surface-elevated: #141414;
        --color-border: rgba(255, 255, 255, 0.06);
        --color-text: #ffffff;
        --color-text-secondary: #888888;
        --color-text-muted: #555555;
        --color-primary: #00ff88;
        --color-primary-dim: rgba(0, 255, 136, 0.15);
        --color-secondary: #00d4ff;
        --color-tertiary: #ff00aa;
        --color-accent-glow: rgba(0, 255, 136, 0.4);
        --font-display: 'Syne', sans-serif;
        --font-body: 'Space Grotesk', sans-serif;
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
    }

    /* Custom Cursor */
    .cursor {
        width: 20px;
        height: 20px;
        border: 2px solid var(--color-primary);
        border-radius: 50%;
        position: fixed;
        pointer-events: none;
        z-index: 10000;
        transition: transform 0.15s ease, background 0.15s ease;
        mix-blend-mode: difference;
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
        background: rgba(0, 255, 136, 0.2);
    }

    /* Animated Mesh Gradient Background */
    .mesh-gradient-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        overflow: hidden;
    }

    .mesh-gradient-bg canvas {
        width: 100%;
        height: 100%;
    }

    /* Aurora Effect */
    .aurora {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
        opacity: 0.4;
    }

    .aurora-beam {
        position: absolute;
        width: 60%;
        height: 200%;
        background: linear-gradient(180deg,
                transparent 0%,
                var(--color-primary) 20%,
                var(--color-secondary) 40%,
                var(--color-tertiary) 60%,
                transparent 100%);
        filter: blur(100px);
        opacity: 0.3;
        animation: auroraMove 15s ease-in-out infinite;
    }

    .aurora-beam:nth-child(1) {
        left: -20%;
        animation-delay: 0s;
    }

    .aurora-beam:nth-child(2) {
        left: 30%;
        animation-delay: -5s;
        animation-duration: 18s;
    }

    .aurora-beam:nth-child(3) {
        left: 60%;
        animation-delay: -10s;
        animation-duration: 12s;
    }

    @keyframes auroraMove {

        0%,
        100% {
            transform: translateY(-30%) rotate(-15deg);
        }

        50% {
            transform: translateY(-50%) rotate(15deg);
        }
    }

    /* Grid Lines Background */
    .grid-lines {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        background-size: 80px 80px;
        animation: gridMove 20s linear infinite;
    }

    @keyframes gridMove {
        0% {
            transform: perspective(500px) rotateX(60deg) translateY(0);
        }

        100% {
            transform: perspective(500px) rotateX(60deg) translateY(80px);
        }
    }

    /* Floating Orbs */
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
        filter: blur(80px);
        animation: orbFloat 20s ease-in-out infinite;
    }

    .orb-1 {
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, var(--color-primary-dim) 0%, transparent 70%);
        top: -200px;
        right: -200px;
        animation-delay: 0s;
    }

    .orb-2 {
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(0, 212, 255, 0.15) 0%, transparent 70%);
        bottom: -200px;
        left: -200px;
        animation-delay: -7s;
    }

    .orb-3 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 0, 170, 0.1) 0%, transparent 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation-delay: -14s;
    }

    @keyframes orbFloat {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        25% {
            transform: translate(50px, -50px) scale(1.1);
        }

        50% {
            transform: translate(-30px, 30px) scale(0.9);
        }

        75% {
            transform: translate(-50px, -30px) scale(1.05);
        }
    }

    /* Navbar */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 1.5rem 4rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar.scrolled {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        padding: 1rem 4rem;
        border-bottom: 1px solid var(--color-border);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: var(--color-text);
    }

    .logo-mark {
        width: 50px;
        height: 50px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-mark::before {
        content: '';
        position: absolute;
        inset: 0;
        border: 2px solid var(--color-primary);
        border-radius: 12px;
        animation: logoRotate 10s linear infinite;
    }

    .logo-mark::after {
        content: '';
        position: absolute;
        inset: 5px;
        border: 2px solid var(--color-secondary);
        border-radius: 8px;
        animation: logoRotate 10s linear infinite reverse;
    }

    .logo-mark span {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 800;
        z-index: 1;
    }

    @keyframes logoRotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .logo-text {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 3rem;
    }

    .nav-link {
        color: var(--color-text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
        transition: width 0.3s ease;
    }

    .nav-link:hover {
        color: var(--color-text);
    }

    .nav-link:hover::before {
        width: 100%;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        border-radius: 100px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: none;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-primary {
        background: var(--color-primary);
        color: #000;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .btn-primary:hover::before {
        transform: translateX(100%);
    }

    .btn-primary:hover {
        box-shadow: 0 0 40px var(--color-accent-glow), 0 0 80px rgba(0, 255, 136, 0.2);
        transform: translateY(-3px);
    }

    .btn-outline {
        background: transparent;
        color: var(--color-text);
        border: 1px solid var(--color-border);
    }

    .btn-outline:hover {
        border-color: var(--color-primary);
        background: var(--color-primary-dim);
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
        max-width: 1200px;
        position: relative;
        z-index: 1;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        background: rgba(0, 255, 136, 0.05);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 2.5rem;
        animation: fadeInUp 1s ease-out;
    }

    .hero-eyebrow .dot {
        width: 8px;
        height: 8px;
        background: var(--color-primary);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.8);
        }
    }

    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(3.5rem, 10vw, 8rem);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.04em;
        margin-bottom: 2rem;
        animation: fadeInUp 1s ease-out 0.1s backwards;
    }

    .hero-title .line {
        display: block;
        overflow: hidden;
    }

    .hero-title .word {
        display: inline-block;
        animation: slideUp 1s cubic-bezier(0.4, 0, 0.2, 1) backwards;
    }

    .hero-title .line:nth-child(1) .word {
        animation-delay: 0.2s;
    }

    .hero-title .line:nth-child(2) .word {
        animation-delay: 0.3s;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .hero-title .gradient-text {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 50%, var(--color-tertiary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% 200%;
        animation: gradientShift 5s ease-in-out infinite;
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .hero-subtitle {
        font-size: clamp(1.1rem, 2vw, 1.4rem);
        color: var(--color-text-secondary);
        max-width: 650px;
        margin: 0 auto 3rem;
        line-height: 1.8;
        animation: fadeInUp 1s ease-out 0.4s backwards;
    }

    .hero-cta {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 1s ease-out 0.5s backwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scroll Indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 3rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        animation: fadeInUp 1s ease-out 0.8s backwards;
    }

    .scroll-line {
        width: 1px;
        height: 60px;
        background: linear-gradient(to bottom, var(--color-primary), transparent);
        position: relative;
        overflow: hidden;
    }

    .scroll-line::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 20px;
        background: var(--color-primary);
        animation: scrollDown 2s ease-in-out infinite;
    }

    @keyframes scrollDown {
        0% {
            transform: translateY(-100%);
        }

        100% {
            transform: translateY(300%);
        }
    }

    .scroll-text {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: var(--color-text-muted);
    }

    /* Stats Section */
    .stats-section {
        padding: 6rem 2rem;
        position: relative;
    }

    .stats-grid {
        max-width: 1400px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }

    .stat-card {
        text-align: center;
        padding: 3rem 2rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--color-border);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--color-primary-dim), transparent);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        border-color: rgba(0, 255, 136, 0.3);
    }

    .stat-value {
        font-family: var(--font-display);
        font-size: 4rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--color-text), var(--color-text-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        z-index: 1;
    }

    .stat-label {
        color: var(--color-text-secondary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        position: relative;
        z-index: 1;
    }

    /* Features Section */
    .features-section {
        padding: 10rem 2rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    .section-header {
        text-align: center;
        margin-bottom: 6rem;
    }

    .section-tag {
        display: inline-block;
        color: var(--color-primary);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-family: var(--font-display);
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        margin-bottom: 1.5rem;
    }

    .section-subtitle {
        color: var(--color-text-secondary);
        font-size: 1.2rem;
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
        border-radius: 32px;
        padding: 3rem;
        position: relative;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--color-primary), var(--color-secondary), var(--color-tertiary));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s ease;
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-15px) scale(1.02);
        border-color: rgba(0, 255, 136, 0.2);
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.5);
    }

    .feature-number {
        font-family: var(--font-display);
        font-size: 5rem;
        font-weight: 800;
        position: absolute;
        top: 1rem;
        right: 2rem;
        opacity: 0.05;
        line-height: 1;
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--color-primary-dim), rgba(0, 212, 255, 0.1));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: var(--color-primary);
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 10px 40px var(--color-accent-glow);
    }

    .feature-title {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .feature-description {
        color: var(--color-text-secondary);
        font-size: 1rem;
        line-height: 1.8;
    }

    /* Workflow Section */
    .workflow-section {
        padding: 10rem 2rem;
        background: var(--color-surface);
        position: relative;
        overflow: hidden;
    }

    .workflow-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(ellipse at center, var(--color-primary-dim), transparent 70%);
        pointer-events: none;
    }

    .workflow-container {
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .workflow-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 5rem;
    }

    .workflow-timeline::before {
        content: '';
        position: absolute;
        top: 60px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: linear-gradient(90deg,
                var(--color-primary),
                var(--color-secondary),
                var(--color-tertiary),
                var(--color-primary));
        background-size: 300% 100%;
        animation: lineFlow 5s linear infinite;
    }

    @keyframes lineFlow {
        0% {
            background-position: 0% 50%;
        }

        100% {
            background-position: 300% 50%;
        }
    }

    .workflow-step {
        text-align: center;
        flex: 1;
        padding: 0 1rem;
        position: relative;
    }

    .step-icon {
        width: 120px;
        height: 120px;
        background: var(--color-bg);
        border: 2px solid var(--color-border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        position: relative;
        z-index: 2;
        transition: all 0.5s ease;
        overflow: hidden;
    }

    .step-icon::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        background: conic-gradient(var(--color-primary), var(--color-secondary), var(--color-tertiary), var(--color-primary));
        z-index: -1;
        animation: rotate 3s linear infinite;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .workflow-step:hover .step-icon::before {
        opacity: 1;
    }

    .workflow-step:hover .step-icon {
        transform: scale(1.1);
        box-shadow: 0 0 50px var(--color-accent-glow);
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .step-number {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .step-title {
        font-family: var(--font-display);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .step-description {
        color: var(--color-text-secondary);
        font-size: 0.9rem;
    }

    /* CTA Section */
    .cta-section {
        padding: 10rem 2rem;
        text-align: center;
        position: relative;
    }

    .cta-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
    }

    .cta-card {
        background: linear-gradient(135deg, rgba(0, 255, 136, 0.05), rgba(0, 212, 255, 0.05));
        border: 1px solid var(--color-border);
        border-radius: 48px;
        padding: 6rem 4rem;
        position: relative;
        overflow: hidden;
    }

    .cta-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, var(--color-primary-dim), transparent 50%);
        pointer-events: none;
    }

    .cta-title {
        font-family: var(--font-display);
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .cta-subtitle {
        color: var(--color-text-secondary);
        font-size: 1.25rem;
        margin-bottom: 3rem;
        position: relative;
        z-index: 1;
    }

    .cta-buttons {
        display: flex;
        gap: 1.5rem;
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
        max-width: 1400px;
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
        gap: 2.5rem;
    }

    .footer-link {
        color: var(--color-text-secondary);
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.3s ease;
    }

    .footer-link:hover {
        color: var(--color-primary);
    }

    /* Reveal Animations */
    .reveal {
        opacity: 0;
        transform: translateY(60px);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-delay-1 {
        transition-delay: 0.1s;
    }

    .reveal-delay-2 {
        transition-delay: 0.2s;
    }

    .reveal-delay-3 {
        transition-delay: 0.3s;
    }

    .reveal-delay-4 {
        transition-delay: 0.4s;
    }

    .reveal-delay-5 {
        transition-delay: 0.5s;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .workflow-timeline {
            flex-direction: column;
            gap: 3rem;
        }

        .workflow-timeline::before {
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

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .hero {
            padding: 6rem 1.5rem 4rem;
        }

        .cta-card {
            padding: 3rem 1.5rem;
            border-radius: 32px;
        }

        .cursor,
        .cursor-dot {
            display: none;
        }

        body {
            cursor: auto;
        }
    }

    /* Noise texture overlay */
    .noise {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
        opacity: 0.03;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
    }

    /* Glitch text effect for special emphasis */
    .glitch {
        position: relative;
    }

    .glitch::before,
    .glitch::after {
        content: attr(data-text);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .glitch::before {
        animation: glitch-1 0.3s infinite linear alternate-reverse;
        clip-path: polygon(0 0, 100% 0, 100% 35%, 0 35%);
        color: var(--color-secondary);
    }

    .glitch::after {
        animation: glitch-2 0.3s infinite linear alternate-reverse;
        clip-path: polygon(0 65%, 100% 65%, 100% 100%, 0 100%);
        color: var(--color-tertiary);
    }

    @keyframes glitch-1 {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-3px);
        }
    }

    @keyframes glitch-2 {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(3px);
        }
    }
    </style>
</head>

<body>
    <!-- Noise Texture -->
    <div class="noise"></div>

    <!-- Custom Cursor -->
    <div class="cursor" id="cursor"></div>
    <div class="cursor-dot" id="cursor-dot"></div>

    <!-- Mesh Gradient Background -->
    <div class="mesh-gradient-bg">
        <canvas id="gradient-canvas"></canvas>
    </div>

    <!-- Aurora Effect -->
    <div class="aurora">
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
    </div>

    <!-- Floating Orbs -->
    <div class="orbs-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Grid Lines -->
    <div class="grid-lines"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <a href="#" class="logo">
            <div class="logo-mark">
                <span>B</span>
            </div>
            <span class="logo-text">BesoinsFlow</span>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Fonctionnalites</a>
            <a href="#workflow" class="nav-link">Processus</a>
            <a href="#contact" class="nav-link">Contact</a>
            <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                <?php echo $isLoggedIn ? 'Mon Espace' : 'Commencer'; ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-eyebrow">
                <span class="dot"></span>
                <span>Plateforme Nouvelle Generation</span>
            </div>

            <h1 class="hero-title">
                <span class="line"><span class="word">Revolutionnez</span></span>
                <span class="line"><span class="word"><span class="gradient-text">la gestion des
                            besoins</span></span></span>
            </h1>

            <p class="hero-subtitle">
                Une experience fluide et intelligente pour gerer les demandes d'expression de besoins.
                De la soumission a la validation, en toute transparence.
            </p>

            <div class="hero-cta">
                <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                    <?php echo $isLoggedIn ? 'Acceder au Dashboard' : 'Demarrer maintenant'; ?>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="#features" class="btn btn-outline">
                    <i class="bi bi-play-circle"></i>
                    Decouvrir
                </a>
            </div>
        </div>

        <div class="scroll-indicator">
            <div class="scroll-line"></div>
            <span class="scroll-text">Defiler</span>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-value" data-target="98">0</div>
                <div class="stat-label">Temps reduit</div>
            </div>
            <div class="stat-card reveal reveal-delay-1">
                <div class="stat-value" data-target="500">0</div>
                <div class="stat-label">Demandes/mois</div>
            </div>
            <div class="stat-card reveal reveal-delay-2">
                <div class="stat-value" data-target="100">0</div>
                <div class="stat-label">Tracabilite</div>
            </div>
            <div class="stat-card reveal reveal-delay-3">
                <div class="stat-value">24/7</div>
                <div class="stat-label">Disponibilite</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="section-header reveal">
            <span class="section-tag">Fonctionnalites</span>
            <h2 class="section-title">Une suite complete d'outils</h2>
            <p class="section-subtitle">
                Tout ce dont vous avez besoin pour gerer efficacement les expressions de besoins.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card reveal">
                <span class="feature-number">01</span>
                <div class="feature-icon">
                    <i class="bi bi-send-check"></i>
                </div>
                <h3 class="feature-title">Soumission Intuitive</h3>
                <p class="feature-description">
                    Creez et soumettez vos demandes en quelques clics grace a une interface moderne et epuree.
                </p>
            </div>

            <div class="feature-card reveal reveal-delay-1">
                <span class="feature-number">02</span>
                <div class="feature-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h3 class="feature-title">Workflow Intelligent</h3>
                <p class="feature-description">
                    Automatisation complete du processus de validation avec routing hierarchique.
                </p>
            </div>

            <div class="feature-card reveal reveal-delay-2">
                <span class="feature-number">03</span>
                <div class="feature-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="feature-title">Analytics Avances</h3>
                <p class="feature-description">
                    Tableaux de bord temps reel avec metriques detaillees et rapports personnalises.
                </p>
            </div>

            <div class="feature-card reveal reveal-delay-3">
                <span class="feature-number">04</span>
                <div class="feature-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="feature-title">Notifications Smart</h3>
                <p class="feature-description">
                    Alertes contextuelles par email et in-app pour ne jamais manquer une action.
                </p>
            </div>

            <div class="feature-card reveal reveal-delay-4">
                <span class="feature-number">05</span>
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="feature-title">Securite Maximale</h3>
                <p class="feature-description">
                    Authentification robuste et gestion granulaire des permissions et roles.
                </p>
            </div>

            <div class="feature-card reveal reveal-delay-5">
                <span class="feature-number">06</span>
                <div class="feature-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3 class="feature-title">Audit Trail</h3>
                <p class="feature-description">
                    Historique complet et immutable de chaque action pour une tracabilite totale.
                </p>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="workflow-section" id="workflow">
        <div class="workflow-container">
            <div class="section-header reveal">
                <span class="section-tag">Processus</span>
                <h2 class="section-title">Comment ca marche</h2>
                <p class="section-subtitle">
                    Un workflow fluide en 5 etapes pour transformer vos besoins en actions.
                </p>
            </div>

            <div class="workflow-timeline">
                <div class="workflow-step reveal">
                    <div class="step-icon">
                        <span class="step-number">1</span>
                    </div>
                    <h4 class="step-title">Soumission</h4>
                    <p class="step-description">Creation de la demande</p>
                </div>

                <div class="workflow-step reveal reveal-delay-1">
                    <div class="step-icon">
                        <span class="step-number">2</span>
                    </div>
                    <h4 class="step-title">Notification</h4>
                    <p class="step-description">Alerte au validateur</p>
                </div>

                <div class="workflow-step reveal reveal-delay-2">
                    <div class="step-icon">
                        <span class="step-number">3</span>
                    </div>
                    <h4 class="step-title">Validation</h4>
                    <p class="step-description">Approbation hierarchique</p>
                </div>

                <div class="workflow-step reveal reveal-delay-3">
                    <div class="step-icon">
                        <span class="step-number">4</span>
                    </div>
                    <h4 class="step-title">Traitement</h4>
                    <p class="step-description">Execution par l'admin</p>
                </div>

                <div class="workflow-step reveal reveal-delay-4">
                    <div class="step-icon">
                        <span class="step-number">5</span>
                    </div>
                    <h4 class="step-title">Cloture</h4>
                    <p class="step-description">Confirmation finale</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact">
        <div class="cta-wrapper">
            <div class="cta-card reveal">
                <h2 class="cta-title">Pret a transformer votre organisation ?</h2>
                <p class="cta-subtitle">
                    Rejoignez les entreprises qui ont digitalise leur gestion des besoins.
                </p>
                <div class="cta-buttons">
                    <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">
                        <?php echo $isLoggedIn ? 'Acceder a la plateforme' : 'Commencer gratuitement'; ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="login.php" class="btn btn-outline">
                        <i class="bi bi-person"></i>
                        <?php echo $isLoggedIn ? 'Mon profil' : 'Se connecter'; ?>
                    </a>
                </div>
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
                <a href="#" class="footer-link">Support</a>
            </div>
        </div>
    </footer>

    <script>
    // Custom Cursor
    const cursor = document.getElementById('cursor');
    const cursorDot = document.getElementById('cursor-dot');

    let mouseX = 0,
        mouseY = 0;
    let cursorX = 0,
        cursorY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        cursorDot.style.left = mouseX + 'px';
        cursorDot.style.top = mouseY + 'px';
    });

    function animateCursor() {
        cursorX += (mouseX - cursorX) * 0.1;
        cursorY += (mouseY - cursorY) * 0.1;

        cursor.style.left = cursorX + 'px';
        cursor.style.top = cursorY + 'px';

        requestAnimationFrame(animateCursor);
    }
    animateCursor();

    // Cursor hover effect
    const hoverElements = document.querySelectorAll('a, button, .btn, .feature-card, .stat-card');
    hoverElements.forEach(el => {
        el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
        el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
    });

    // Animated Mesh Gradient Background
    const canvas = document.getElementById('gradient-canvas');
    const ctx = canvas.getContext('2d');

    let width, height;
    let time = 0;

    function resizeCanvas() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    function drawGradient() {
        time += 0.002;

        const gradient = ctx.createRadialGradient(
            width * (0.3 + Math.sin(time) * 0.2),
            height * (0.3 + Math.cos(time * 0.7) * 0.2),
            0,
            width * 0.5,
            height * 0.5,
            Math.max(width, height)
        );

        gradient.addColorStop(0,
            `rgba(0, ${Math.floor(100 + Math.sin(time) * 50)}, ${Math.floor(68 + Math.cos(time) * 30)}, 0.15)`);
        gradient.addColorStop(0.5,
            `rgba(0, ${Math.floor(100 + Math.cos(time * 0.5) * 50)}, ${Math.floor(128 + Math.sin(time * 0.5) * 50)}, 0.08)`
        );
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, width, height);
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, width, height);

        // Second gradient layer
        const gradient2 = ctx.createRadialGradient(
            width * (0.7 + Math.cos(time * 0.8) * 0.2),
            height * (0.7 + Math.sin(time * 0.6) * 0.2),
            0,
            width * 0.5,
            height * 0.5,
            Math.max(width, height) * 0.8
        );

        gradient2.addColorStop(0,
            `rgba(${Math.floor(100 + Math.sin(time * 0.5) * 50)}, 0, ${Math.floor(100 + Math.cos(time) * 50)}, 0.1)`
        );
        gradient2.addColorStop(1, 'rgba(0, 0, 0, 0)');

        ctx.fillStyle = gradient2;
        ctx.fillRect(0, 0, width, height);

        requestAnimationFrame(drawGradient);
    }

    drawGradient();

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Reveal elements on scroll
    const revealElements = document.querySelectorAll('.reveal');

    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;

        revealElements.forEach((element) => {
            const elementTop = element.getBoundingClientRect().top;
            const revealPoint = 150;

            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('active');
            }
        });
    };

    window.addEventListener('scroll', revealOnScroll);
    window.addEventListener('load', revealOnScroll);

    // Counter animation
    const animateCounters = () => {
        const counters = document.querySelectorAll('.stat-value[data-target]');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const suffix = counter.textContent.includes('%') ? '%' :
                counter.textContent.includes('+') ? '+' : '';

            let current = 0;
            const increment = target / 60;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current) + (target === 98 || target === 100 ? '%' :
                    target === 500 ? '+' : '');
            }, 25);
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
    }, {
        threshold: 0.3
    });

    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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

    // Parallax effect on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;

        // Parallax for orbs
        document.querySelectorAll('.orb').forEach((orb, index) => {
            const speed = 0.05 + (index * 0.02);
            orb.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Magnetic button effect
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0, 0)';
        });
    });

    // Tilt effect for feature cards
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform =
                `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-15px) scale(1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
        });
    });
    </script>
</body>

</html>