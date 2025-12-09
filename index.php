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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Syne', 'sans-serif'],
                        body: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0066ff',
                        'primary-dim': 'rgba(0, 102, 255, 0.15)',
                        secondary: '#00b4d8',
                        tertiary: '#7c3aed',
                        surface: '#f8fafc',
                        'surface-elevated': '#ffffff',
                        border: 'rgba(0, 0, 0, 0.08)',
                        'text-primary': '#1a1a1a',
                        'text-secondary': '#666666',
                        'text-muted': '#999999',
                    }
                }
            }
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            cursor: none;
            background: #0a0a1a;
            color: #ffffff;
            overflow-x: hidden;
        }

        /* Custom Cursor with trail */
        .cursor {
            width: 20px;
            height: 20px;
            border: 2px solid #0066ff;
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 10000;
            transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 0 20px rgba(0, 102, 255, 0.5);
        }

        .cursor-dot {
            width: 6px;
            height: 6px;
            background: #0066ff;
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 10001;
            box-shadow: 0 0 10px rgba(0, 102, 255, 0.8);
        }

        .cursor.hover {
            transform: scale(2.5);
            background: rgba(0, 102, 255, 0.3);
            box-shadow: 0 0 40px rgba(0, 102, 255, 0.8);
        }

        .cursor-trail {
            position: fixed;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(0, 102, 255, 0.4);
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s ease;
        }

        /* Starfield Background */
        .starfield {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -3;
            overflow: hidden;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s ease-in-out infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.5);
            }
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

        /* Aurora Effect - Enhanced */
        .aurora {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            opacity: 0.5;
        }

        .aurora-beam {
            position: absolute;
            width: 60%;
            height: 200%;
            background: linear-gradient(180deg,
                    transparent 0%,
                    #0066ff 20%,
                    #00b4d8 40%,
                    #7c3aed 60%,
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

        /* 3D Grid Floor */
        .grid-floor {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            z-index: -1;
            background:
                linear-gradient(90deg, rgba(0, 102, 255, 0.1) 1px, transparent 1px),
                linear-gradient(rgba(0, 102, 255, 0.1) 1px, transparent 1px);
            background-size: 60px 60px;
            transform: perspective(500px) rotateX(60deg);
            transform-origin: center top;
            animation: gridScroll 20s linear infinite;
            mask-image: linear-gradient(to top, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0) 100%);
        }

        @keyframes gridScroll {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 60px 60px;
            }
        }

        /* Floating Geometric Shapes */
        .geo-shape {
            position: absolute;
            animation: floatRotate 20s ease-in-out infinite;
        }

        .geo-shape.cube {
            width: 60px;
            height: 60px;
            transform-style: preserve-3d;
            animation: cubeRotate 15s linear infinite;
        }

        .geo-shape.cube .face {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 1px solid rgba(0, 102, 255, 0.3);
            background: rgba(0, 102, 255, 0.05);
            backdrop-filter: blur(5px);
        }

        .geo-shape.cube .face.front {
            transform: translateZ(30px);
        }

        .geo-shape.cube .face.back {
            transform: translateZ(-30px) rotateY(180deg);
        }

        .geo-shape.cube .face.right {
            transform: translateX(30px) rotateY(90deg);
        }

        .geo-shape.cube .face.left {
            transform: translateX(-30px) rotateY(-90deg);
        }

        .geo-shape.cube .face.top {
            transform: translateY(-30px) rotateX(90deg);
        }

        .geo-shape.cube .face.bottom {
            transform: translateY(30px) rotateX(-90deg);
        }

        @keyframes cubeRotate {
            0% {
                transform: rotateX(0deg) rotateY(0deg);
            }

            100% {
                transform: rotateX(360deg) rotateY(360deg);
            }
        }

        .geo-shape.ring {
            width: 100px;
            height: 100px;
            border: 2px solid rgba(0, 180, 216, 0.3);
            border-radius: 50%;
            animation: ringRotate 10s linear infinite;
        }

        @keyframes ringRotate {
            0% {
                transform: rotateX(70deg) rotateZ(0deg);
            }

            100% {
                transform: rotateX(70deg) rotateZ(360deg);
            }
        }

        .geo-shape.pyramid {
            width: 0;
            height: 0;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 50px solid rgba(124, 58, 237, 0.2);
            animation: pyramidFloat 8s ease-in-out infinite;
        }

        @keyframes pyramidFloat {

            0%,
            100% {
                transform: translateY(0) rotateY(0deg);
            }

            50% {
                transform: translateY(-30px) rotateY(180deg);
            }
        }

        @keyframes floatRotate {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(20px, -20px) rotate(90deg);
            }

            50% {
                transform: translate(0, -40px) rotate(180deg);
            }

            75% {
                transform: translate(-20px, -20px) rotate(270deg);
            }
        }

        /* Floating Orbs - Enhanced with glow */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbFloat 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.3) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 180, 216, 0.3) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
            animation-delay: -7s;
        }

        .orb-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
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

        /* Glowing Text Effect */
        .glow-text {
            text-shadow:
                0 0 10px rgba(0, 102, 255, 0.5),
                0 0 20px rgba(0, 102, 255, 0.3),
                0 0 40px rgba(0, 102, 255, 0.2);
        }

        /* Logo animation */
        .logo-mark {
            position: relative;
        }

        .logo-mark::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid #0066ff;
            border-radius: 12px;
            animation: logoRotate 10s linear infinite;
            box-shadow: 0 0 20px rgba(0, 102, 255, 0.5);
        }

        .logo-mark::after {
            content: '';
            position: absolute;
            inset: 5px;
            border: 2px solid #00b4d8;
            border-radius: 8px;
            animation: logoRotate 10s linear infinite reverse;
            box-shadow: 0 0 15px rgba(0, 180, 216, 0.5);
        }

        @keyframes logoRotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Nav link underline */
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #0066ff, #00b4d8);
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 102, 255, 0.5);
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Button shine effect with glow */
        .btn-primary {
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 102, 255, 0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .btn-primary:hover::before {
            transform: translateX(100%);
        }

        .btn-primary:hover {
            box-shadow: 0 0 50px rgba(0, 102, 255, 0.6);
        }

        /* Hero animations */
        .hero-eyebrow {
            animation: fadeInUp 1s ease-out, glowPulse 3s ease-in-out infinite;
        }

        @keyframes glowPulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 102, 255, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(0, 102, 255, 0.6);
            }
        }

        .dot {
            animation: pulse 2s infinite;
            box-shadow: 0 0 10px rgba(0, 102, 255, 0.8);
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

        /* Gradient text with glow */
        .gradient-text {
            background: linear-gradient(135deg, #0066ff 0%, #00b4d8 50%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 5s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(0, 102, 255, 0.5));
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

        .hero-subtitle {
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }

        .hero-cta {
            animation: fadeInUp 1s ease-out 0.5s backwards;
        }

        .scroll-indicator {
            animation: fadeInUp 1s ease-out 0.8s backwards;
        }

        .scroll-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 20px;
            background: #0066ff;
            animation: scrollDown 2s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(0, 102, 255, 0.8);
        }

        @keyframes scrollDown {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(300%);
            }
        }

        /* 3D Card Effect */
        .card-3d {
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .card-3d-inner {
            transition: transform 0.6s ease;
            transform-style: preserve-3d;
        }

        .card-3d:hover .card-3d-inner {
            transform: rotateY(10deg) rotateX(-5deg);
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        /* Stat card hover with 3D effect */
        .stat-card {
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
            border-radius: 24px;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-20px) rotateX(5deg);
            box-shadow:
                0 30px 60px rgba(0, 102, 255, 0.2),
                0 0 40px rgba(0, 102, 255, 0.1);
        }

        /* Feature card with 3D depth */
        .feature-card {
            transform-style: preserve-3d;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0066ff, #00b4d8, #7c3aed);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease;
            border-radius: 32px 32px 0 0;
            box-shadow: 0 0 20px rgba(0, 102, 255, 0.5);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(0, 102, 255, 0.15), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 32px;
            pointer-events: none;
        }

        .feature-card:hover::after {
            opacity: 1;
        }

        /* Step icon animation with 3D ring */
        .step-icon {
            position: relative;
            transform-style: preserve-3d;
        }

        .step-icon::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: conic-gradient(#0066ff, #00b4d8, #7c3aed, #0066ff);
            z-index: -1;
            animation: rotate 3s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .workflow-step:hover .step-icon::before {
            opacity: 1;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Workflow timeline line */
        .workflow-timeline::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 5%;
            right: 5%;
            height: 2px;
            background: linear-gradient(90deg, #0066ff, #00b4d8, #7c3aed, #0066ff);
            background-size: 300% 100%;
            animation: lineFlow 5s linear infinite;
            box-shadow: 0 0 20px rgba(0, 102, 255, 0.5);
        }

        @keyframes lineFlow {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 300% 50%;
            }
        }

        /* CTA card gradient */
        .cta-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(0, 102, 255, 0.2), transparent 50%);
            pointer-events: none;
            border-radius: 48px;
        }

        /* Reveal animations */
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

        /* Particle Canvas */
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        /* Hero 3D floating card */
        .hero-3d-card {
            transform-style: preserve-3d;
            animation: heroCardFloat 6s ease-in-out infinite;
        }

        @keyframes heroCardFloat {

            0%,
            100% {
                transform: translateY(0) rotateX(5deg) rotateY(-5deg);
            }

            50% {
                transform: translateY(-20px) rotateX(-5deg) rotateY(5deg);
            }
        }

        /* Orbiting elements */
        .orbit {
            animation: orbitAnim 8s linear infinite;
            transform-origin: center;
        }

        @keyframes orbitAnim {
            0% {
                transform: translateX(-150px) rotate(0deg) translateX(150px);
            }

            100% {
                transform: translateX(-150px) rotate(360deg) translateX(150px);
            }
        }

        .orbit-reverse {
            animation: orbitAnimReverse 12s linear infinite;
        }

        @keyframes orbitAnimReverse {
            0% {
                transform: translateX(-180px) rotate(360deg) translateX(180px);
            }

            100% {
                transform: translateX(-180px) rotate(0deg) translateX(180px);
            }
        }

        /* Floating animations */
        .float {
            animation: floatAnim 4s ease-in-out infinite;
        }

        .float-delayed {
            animation: floatAnim 4s ease-in-out infinite;
            animation-delay: -2s;
        }

        @keyframes floatAnim {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        /* Cube animation */
        .cube-container {
            perspective: 800px;
        }

        .cube {
            width: 50px;
            height: 50px;
            transform-style: preserve-3d;
            animation: cubeSpinAnim 10s linear infinite;
        }

        @keyframes cubeSpinAnim {
            0% {
                transform: rotateX(0deg) rotateY(0deg);
            }

            100% {
                transform: rotateX(360deg) rotateY(360deg);
            }
        }

        .cube-face {
            position: absolute;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.2), rgba(124, 58, 237, 0.2));
            border: 1px solid rgba(0, 102, 255, 0.3);
        }

        .cube-face.front {
            transform: translateZ(25px);
        }

        .cube-face.back {
            transform: translateZ(-25px) rotateY(180deg);
        }

        .cube-face.right {
            transform: translateX(25px) rotateY(90deg);
        }

        .cube-face.left {
            transform: translateX(-25px) rotateY(-90deg);
        }

        .cube-face.top {
            transform: translateY(-25px) rotateX(90deg);
        }

        .cube-face.bottom {
            transform: translateY(25px) rotateX(-90deg);
        }

        /* Animated gradient for elements */
        .animated-gradient {
            background: linear-gradient(135deg, #0066ff, #00b4d8, #7c3aed);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
        }

        /* Glow effect for cards */
        .glow {
            box-shadow:
                0 0 20px rgba(0, 102, 255, 0.2),
                0 0 40px rgba(0, 102, 255, 0.1);
        }

        /* Blob animation */
        .blob {
            animation: blobAnim 10s ease-in-out infinite;
        }

        @keyframes blobAnim {

            0%,
            100% {
                transform: scale(1) translate(0, 0);
            }

            33% {
                transform: scale(1.1) translate(30px, -20px);
            }

            66% {
                transform: scale(0.9) translate(-20px, 20px);
            }
        }

        /* Particle animation */
        .particle {
            animation: particleFloat 10s ease-in-out infinite;
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0.5;
            }

            25% {
                transform: translateY(-50px) translateX(30px);
                opacity: 0.8;
            }

            50% {
                transform: translateY(-20px) translateX(-20px);
                opacity: 0.3;
            }

            75% {
                transform: translateY(-70px) translateX(10px);
                opacity: 0.7;
            }
        }

        /* Holographic effect */
        .holographic {
            background: linear-gradient(135deg,
                    rgba(0, 102, 255, 0.1) 0%,
                    rgba(0, 180, 216, 0.1) 25%,
                    rgba(124, 58, 237, 0.1) 50%,
                    rgba(0, 180, 216, 0.1) 75%,
                    rgba(0, 102, 255, 0.1) 100%);
            background-size: 400% 400%;
            animation: holographicShift 8s ease infinite;
        }

        @keyframes holographicShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Scanline effect */
        .scanlines::after {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 0, 0, 0.1) 2px,
                    rgba(0, 0, 0, 0.1) 4px);
            pointer-events: none;
            border-radius: inherit;
        }

        /* Light beam effect */
        .light-beam {
            position: fixed;
            width: 2px;
            height: 100vh;
            background: linear-gradient(180deg, transparent, rgba(0, 102, 255, 0.5), transparent);
            z-index: -1;
            animation: beamMove 15s linear infinite;
        }

        @keyframes beamMove {
            0% {
                left: -10%;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                left: 110%;
                opacity: 0;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {

            .cursor,
            .cursor-dot,
            .cursor-trail {
                display: none;
            }

            body {
                cursor: auto;
            }

            .workflow-timeline::before {
                display: none;
            }

            .geo-shape {
                display: none;
            }
        }
    </style>
</head>

<body class="font-body leading-relaxed overflow-x-hidden">
    <!-- Noise Texture -->
    <div class="noise"></div>

    <!-- Custom Cursor -->
    <div class="cursor" id="cursor"></div>
    <div class="cursor-dot" id="cursor-dot"></div>
    <div id="cursor-trails"></div>

    <!-- Starfield -->
    <div class="starfield" id="starfield"></div>

    <!-- Particle Canvas -->
    <canvas id="particle-canvas"></canvas>

    <!-- Mesh Gradient Background -->
    <div class="mesh-gradient-bg">
        <canvas id="gradient-canvas"></canvas>
    </div>

    <!-- Light Beams -->
    <div class="light-beam" style="animation-delay: 0s;"></div>
    <div class="light-beam" style="animation-delay: 5s;"></div>
    <div class="light-beam" style="animation-delay: 10s;"></div>

    <!-- Aurora Effect -->
    <div class="aurora">
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
        <div class="aurora-beam"></div>
    </div>

    <!-- Floating Orbs -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-[-1]">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- 3D Grid Floor -->
    <div class="grid-floor"></div>

    <!-- Floating Geometric Shapes -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0 overflow-hidden">
        <div class="geo-shape cube" style="top: 20%; left: 10%;">
            <div class="face front"></div>
            <div class="face back"></div>
            <div class="face right"></div>
            <div class="face left"></div>
            <div class="face top"></div>
            <div class="face bottom"></div>
        </div>
        <div class="geo-shape ring" style="top: 60%; right: 15%; transform-style: preserve-3d;"></div>
        <div class="geo-shape pyramid" style="top: 30%; right: 25%;"></div>
        <div class="geo-shape cube" style="bottom: 20%; left: 20%; animation-delay: -5s;">
            <div class="face front"></div>
            <div class="face back"></div>
            <div class="face right"></div>
            <div class="face left"></div>
            <div class="face top"></div>
            <div class="face bottom"></div>
        </div>
        <div class="geo-shape ring" style="top: 40%; left: 5%; animation-delay: -3s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-[1000] px-6 lg:px-16 py-6 flex justify-between items-center transition-all duration-500"
        id="navbar">
        <a href="#" class="flex items-center gap-4 no-underline text-white">
            <div class="logo-mark w-[50px] h-[50px] relative flex items-center justify-center">
                <span class="font-display text-2xl font-extrabold z-10 glow-text">B</span>
            </div>
            <span class="font-display text-2xl font-bold tracking-tight glow-text">BesoinsFlow</span>
        </a>
        <div class="hidden md:flex items-center gap-12">
            <a href="#features"
                class="nav-link relative text-gray-300 no-underline text-sm font-medium uppercase tracking-widest transition-colors hover:text-white">Fonctionnalités</a>
            <a href="#workflow"
                class="nav-link relative text-gray-300 no-underline text-sm font-medium uppercase tracking-widest transition-colors hover:text-white">Processus</a>
            <a href="#contact"
                class="nav-link relative text-gray-300 no-underline text-sm font-medium uppercase tracking-widest transition-colors hover:text-white">Contact</a>
            <a href="login.php"
                class="btn-primary relative inline-flex items-center gap-3 px-8 py-4 rounded-full text-sm font-semibold uppercase tracking-widest bg-primary text-white overflow-hidden transition-all duration-400 hover:-translate-y-1">
                Commencer
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen flex flex-col justify-center items-center text-center px-4 pt-32 pb-16 relative">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Gradient Blobs -->
            <div class="absolute top-20 left-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl blob"></div>
            <div class="absolute bottom-20 right-10 w-80 h-80 bg-sky-500/20 rounded-full blur-3xl blob"
                style="animation-delay: -4s;"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-500/10 rounded-full blur-3xl">
            </div>

            <!-- Floating Particles -->
            <div class="particle absolute top-1/4 left-1/4 w-3 h-3 bg-indigo-400 rounded-full opacity-50"></div>
            <div class="particle absolute top-1/3 right-1/4 w-2 h-2 bg-sky-400 rounded-full opacity-50"
                style="animation-delay: -3s;"></div>
            <div class="particle absolute bottom-1/4 left-1/3 w-4 h-4 bg-violet-400 rounded-full opacity-50"
                style="animation-delay: -5s;"></div>
            <div class="particle absolute top-2/3 right-1/3 w-2 h-2 bg-indigo-300 rounded-full opacity-50"
                style="animation-delay: -7s;"></div>
        </div>

        <div class="flex flex-row relative z-10">
            <div class="w-full lg:w-3/4">
                <div
                    class="hero-eyebrow inline-flex items-center gap-3 px-6 py-3 glass-card rounded-full text-sm font-medium text-primary uppercase tracking-widest mb-10 holographic">
                    <span class="dot w-2 h-2 bg-primary rounded-full"></span>
                    <span>Plateforme Nouvelle Génération</span>
                </div>

                <h1
                    class="hero-title font-display text-5xl md:text-7xl lg:text-8xl font-extrabold leading-none tracking-tight mb-8">
                    <span class="line block overflow-hidden"><span class="word glow-text">Révolutionnez</span></span>
                    <span class="line block overflow-hidden"><span class="word"><span class="gradient-text">la gestion
                                des besoins</span></span></span>
                </h1>
            </div>
            <div class="w-1/4 hidden lg:block">
                <div class="absolute overflow-visible">
                    <!-- Gradient Blobs -->
                    <div class="absolute top-20 left-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl blob"></div>
                    <div class="absolute bottom-20 right-10 w-80 h-80 bg-sky-500/20 rounded-full blur-3xl blob"
                        style="animation-delay: -4s;"></div>
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-500/10 rounded-full blur-3xl">
                    </div>

                    <!-- Floating Particles -->
                    <div class="particle absolute top-1/4 left-1/4 w-3 h-3 bg-indigo-400 rounded-full opacity-50"></div>
                    <div class="particle absolute top-1/3 right-1/4 w-2 h-2 bg-sky-400 rounded-full opacity-50"
                        style="animation-delay: -3s;"></div>
                    <div class="particle absolute bottom-1/4 left-1/3 w-4 h-4 bg-violet-400 rounded-full opacity-50"
                        style="animation-delay: -5s;"></div>
                    <div class="particle absolute top-2/3 right-1/3 w-2 h-2 bg-indigo-300 rounded-full opacity-50"
                        style="animation-delay: -7s;"></div>
                </div>

                <div class="relative h-[500px]">
                    <!-- Central 3D Element -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <!-- Main Card -->
                        <div class="card-3d hero-3d-card">
                            <div class="card-3d-inner w-72 h-96 glass-card rounded-3xl p-6 glow scanlines">
                                <div class="h-full flex flex-col">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-12 h-12 rounded-2xl animated-gradient"></div>
                                        <div>
                                            <div class="h-3 w-24 bg-white/20 rounded-full"></div>
                                            <div class="h-2 w-16 bg-white/10 rounded-full mt-2"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-4 flex-1">
                                        <div
                                            class="p-4 bg-gradient-to-r from-indigo-500/20 to-sky-500/20 rounded-2xl border border-indigo-500/30">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-500/30 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-indigo-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-medium text-white/80">Demande approuvée</span>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-white/5 rounded-2xl">
                                            <div class="h-2 w-full bg-white/20 rounded-full"></div>
                                            <div class="h-2 w-3/4 bg-white/10 rounded-full mt-2"></div>
                                        </div>
                                        <div class="p-4 bg-white/5 rounded-2xl">
                                            <div class="h-2 w-full bg-white/20 rounded-full"></div>
                                            <div class="h-2 w-1/2 bg-white/10 rounded-full mt-2"></div>
                                        </div>
                                    </div>
                                    <button
                                        class="w-full py-3 bg-indigo-600 text-white rounded-xl font-semibold mt-4 glow">
                                        Voir détails
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Orbiting Elements -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-0 h-0">
                            <div class="orbit">
                                <div class="w-14 h-14 glass-card rounded-2xl flex items-center justify-center glow">
                                    <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-0 h-0">
                            <div class="orbit-reverse">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sky-400 to-sky-600 rounded-xl shadow-xl flex items-center justify-center glow">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Cards -->
                    <div class="absolute top-10 right-10 float">
                        <div class="w-48 glass-card rounded-2xl p-4 glow">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-500/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Efficacité</p>
                                    <p class="text-lg font-bold text-white">+85%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-20 left-0 float-delayed">
                        <div class="w-52 glass-card rounded-2xl p-4 glow">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-violet-500/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Temps de traitement</p>
                                    <p class="text-lg font-bold text-white">-60%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3D Cube -->
                    <div class="absolute bottom-10 right-20 cube-container">
                        <div class="cube">
                            <div class="cube-face front"></div>
                            <div class="cube-face back"></div>
                            <div class="cube-face right"></div>
                            <div class="cube-face left"></div>
                            <div class="cube-face top"></div>
                            <div class="cube-face bottom"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="hero-subtitle text-lg md:text-xl text-gray-300 max-w-[650px] mx-auto mb-12 leading-relaxed">
            Une expérience fluide et intelligente pour gérer les demandes d'expression de besoins.
            De la soumission à la validation, en toute transparence.
        </p>

        <div class="hero-cta flex gap-6 justify-center flex-wrap">
            <a href="login.php"
                class="btn-primary relative inline-flex items-center gap-3 px-8 py-4 rounded-full text-sm font-semibold uppercase tracking-widest bg-primary text-white overflow-hidden transition-all duration-400 hover:-translate-y-1">
                Démarrer maintenant
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#features"
                class="inline-flex items-center gap-3 px-8 py-4 rounded-full text-sm font-semibold uppercase tracking-widest glass-card text-white transition-all hover:bg-primary/20 holographic">
                <i class="bi bi-play-circle"></i>
                Découvrir
            </a>
        </div>

        <div class="scroll-indicator absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4">
            <div
                class="scroll-line w-px h-[60px] bg-gradient-to-b from-primary to-transparent relative overflow-hidden">
            </div>
            <span class="text-xs uppercase tracking-[0.2em] text-gray-500">Défiler</span>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-24 px-4 relative">
        <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div
                class="stat-card relative text-center p-12 glass-card rounded-3xl overflow-hidden transition-all duration-400 reveal holographic">
                <div class="stat-value font-display text-6xl font-extrabold leading-none mb-2 gradient-text relative z-10"
                    data-target="98">0</div>
                <div class="text-gray-400 text-sm uppercase tracking-widest relative z-10">Temps réduit</div>
            </div>
            <div
                class="stat-card relative text-center p-12 glass-card rounded-3xl overflow-hidden transition-all duration-400 reveal reveal-delay-1 holographic">
                <div class="stat-value font-display text-6xl font-extrabold leading-none mb-2 gradient-text relative z-10"
                    data-target="500">0</div>
                <div class="text-gray-400 text-sm uppercase tracking-widest relative z-10">Demandes/mois</div>
            </div>
            <div
                class="stat-card relative text-center p-12 glass-card rounded-3xl overflow-hidden transition-all duration-400 reveal reveal-delay-2 holographic">
                <div class="stat-value font-display text-6xl font-extrabold leading-none mb-2 gradient-text relative z-10"
                    data-target="100">0</div>
                <div class="text-gray-400 text-sm uppercase tracking-widest relative z-10">Traçabilité</div>
            </div>
            <div
                class="stat-card relative text-center p-12 glass-card rounded-3xl overflow-hidden transition-all duration-400 reveal reveal-delay-3 holographic">
                <div
                    class="stat-value font-display text-6xl font-extrabold leading-none mb-2 gradient-text relative z-10">
                    24/7</div>
                <div class="text-gray-400 text-sm uppercase tracking-widest relative z-10">Disponibilité</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-40 px-4 max-w-[1600px] mx-auto" id="features">
        <div class="text-center mb-24 reveal">
            <span
                class="inline-block text-primary text-sm font-semibold uppercase tracking-[0.2em] mb-6 glow-text">Fonctionnalités</span>
            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-6 glow-text">Une suite
                complète d'outils</h2>
            <p class="text-gray-400 text-xl max-w-[600px] mx-auto">
                Tout ce dont vous avez besoin pour gérer efficacement les expressions de besoins.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">01</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-send-check"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Soumission Intuitive</h3>
                <p class="text-gray-400 leading-relaxed">
                    Créez et soumettez vos demandes en quelques clics grâce à une interface moderne et épurée.
                </p>
            </div>

            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal reveal-delay-1">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">02</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Workflow Intelligent</h3>
                <p class="text-gray-400 leading-relaxed">
                    Automatisation complète du processus de validation avec routing hiérarchique.
                </p>
            </div>

            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal reveal-delay-2">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">03</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Analytics Avancés</h3>
                <p class="text-gray-400 leading-relaxed">
                    Tableaux de bord temps réel avec métriques détaillées et rapports personnalisés.
                </p>
            </div>

            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal reveal-delay-3">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">04</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Notifications Smart</h3>
                <p class="text-gray-400 leading-relaxed">
                    Alertes contextuelles par email et in-app pour ne jamais manquer une action.
                </p>
            </div>

            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal reveal-delay-4">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">05</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Sécurité Maximale</h3>
                <p class="text-gray-400 leading-relaxed">
                    Authentification robuste et gestion granulaire des permissions et rôles.
                </p>
            </div>

            <div
                class="feature-card relative glass-card rounded-[32px] p-12 overflow-hidden transition-all duration-500 reveal reveal-delay-5">
                <span
                    class="font-display text-8xl font-extrabold absolute top-4 right-8 opacity-10 leading-none gradient-text">06</span>
                <div
                    class="w-[70px] h-[70px] animated-gradient rounded-[20px] flex items-center justify-center text-3xl text-white mb-8 transition-all duration-300 glow">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3 class="font-display text-2xl font-bold mb-4 text-white">Audit Trail</h3>
                <p class="text-gray-400 leading-relaxed">
                    Historique complet et immutable de chaque action pour une traçabilité totale.
                </p>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="py-40 px-4 relative overflow-hidden" id="workflow">
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,102,255,0.15),transparent_70%)] pointer-events-none">
        </div>
        <div class="max-w-[1400px] mx-auto relative z-10">
            <div class="text-center mb-24 reveal">
                <span
                    class="inline-block text-primary text-sm font-semibold uppercase tracking-[0.2em] mb-6 glow-text">Processus</span>
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-6 glow-text">
                    Comment ça marche</h2>
                <p class="text-gray-400 text-xl max-w-[600px] mx-auto">
                    Un workflow fluide en 5 étapes pour transformer vos besoins en actions.
                </p>
            </div>

            <div class="workflow-timeline relative flex flex-col lg:flex-row justify-between mt-20">
                <div class="workflow-step text-center flex-1 px-4 relative reveal">
                    <div
                        class="step-icon w-[120px] h-[120px] glass-card rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 transition-all duration-500 overflow-hidden glow">
                        <span class="font-display text-4xl font-extrabold gradient-text">1</span>
                    </div>
                    <h4 class="font-display text-xl font-bold mb-2 text-white">Soumission</h4>
                    <p class="text-gray-400 text-sm">Création de la demande</p>
                </div>

                <div class="workflow-step text-center flex-1 px-4 relative reveal reveal-delay-1">
                    <div
                        class="step-icon w-[120px] h-[120px] glass-card rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 transition-all duration-500 overflow-hidden glow">
                        <span class="font-display text-4xl font-extrabold gradient-text">2</span>
                    </div>
                    <h4 class="font-display text-xl font-bold mb-2 text-white">Notification</h4>
                    <p class="text-gray-400 text-sm">Alerte au validateur</p>
                </div>

                <div class="workflow-step text-center flex-1 px-4 relative reveal reveal-delay-2">
                    <div
                        class="step-icon w-[120px] h-[120px] glass-card rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 transition-all duration-500 overflow-hidden glow">
                        <span class="font-display text-4xl font-extrabold gradient-text">3</span>
                    </div>
                    <h4 class="font-display text-xl font-bold mb-2 text-white">Validation</h4>
                    <p class="text-gray-400 text-sm">Approbation hiérarchique</p>
                </div>

                <div class="workflow-step text-center flex-1 px-4 relative reveal reveal-delay-3">
                    <div
                        class="step-icon w-[120px] h-[120px] glass-card rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 transition-all duration-500 overflow-hidden glow">
                        <span class="font-display text-4xl font-extrabold gradient-text">4</span>
                    </div>
                    <h4 class="font-display text-xl font-bold mb-2 text-white">Traitement</h4>
                    <p class="text-gray-400 text-sm">Exécution par l'admin</p>
                </div>

                <div class="workflow-step text-center flex-1 px-4 relative reveal reveal-delay-4">
                    <div
                        class="step-icon w-[120px] h-[120px] glass-card rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 transition-all duration-500 overflow-hidden glow">
                        <span class="font-display text-4xl font-extrabold gradient-text">5</span>
                    </div>
                    <h4 class="font-display text-xl font-bold mb-2 text-white">Clôture</h4>
                    <p class="text-gray-400 text-sm">Confirmation finale</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-40 px-4 text-center relative" id="contact">
        <div class="max-w-[1000px] mx-auto relative">
            <div
                class="cta-card relative glass-card rounded-[48px] py-24 px-8 md:px-16 overflow-hidden reveal holographic">
                <h2
                    class="font-display text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 relative z-10 glow-text">
                    Prêt à transformer votre organisation ?</h2>
                <p class="text-gray-400 text-xl mb-12 relative z-10">
                    Rejoignez les entreprises qui ont digitalisé leur gestion des besoins.
                </p>
                <div class="flex gap-6 justify-center flex-wrap relative z-10">
                    <a href="login.php"
                        class="btn-primary relative inline-flex items-center gap-3 px-8 py-4 rounded-full text-sm font-semibold uppercase tracking-widest bg-primary text-white overflow-hidden transition-all duration-400 hover:-translate-y-1">
                        Commencer gratuitement
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="login.php"
                        class="inline-flex items-center gap-3 px-8 py-4 rounded-full text-sm font-semibold uppercase tracking-widest glass-card text-white transition-all hover:bg-primary/20">
                        <i class="bi bi-person"></i>
                        Se connecter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 px-4 border-t border-white/10">
        <div class="max-w-[1400px] mx-auto flex justify-between items-center flex-wrap gap-8">
            <div class="text-gray-500 text-sm">
                &copy; 2025 BesoinsFlow. Tous droits réservés.
            </div>
            <div class="flex gap-10">
                <a href="#"
                    class="text-gray-400 text-sm no-underline transition-colors hover:text-primary">Confidentialité</a>
                <a href="#"
                    class="text-gray-400 text-sm no-underline transition-colors hover:text-primary">Conditions</a>
                <a href="#" class="text-gray-400 text-sm no-underline transition-colors hover:text-primary">Support</a>
            </div>
        </div>
    </footer>

    <script>
        // Create starfield
        const starfield = document.getElementById('starfield');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            star.style.animationDuration = (2 + Math.random() * 3) + 's';
            starfield.appendChild(star);
        }

        // Custom Cursor with trails
        const cursor = document.getElementById('cursor');
        const cursorDot = document.getElementById('cursor-dot');
        const trailsContainer = document.getElementById('cursor-trails');

        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;

        // Create cursor trails
        const trails = [];
        for (let i = 0; i < 5; i++) {
            const trail = document.createElement('div');
            trail.className = 'cursor-trail';
            trail.style.opacity = (1 - i * 0.15);
            trail.style.transform = `scale(${1 - i * 0.15})`;
            trailsContainer.appendChild(trail);
            trails.push({ element: trail, x: 0, y: 0 });
        }

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

            // Animate trails
            let prevX = cursorX;
            let prevY = cursorY;
            trails.forEach((trail, index) => {
                trail.x += (prevX - trail.x) * (0.3 - index * 0.05);
                trail.y += (prevY - trail.y) * (0.3 - index * 0.05);
                trail.element.style.left = trail.x + 'px';
                trail.element.style.top = trail.y + 'px';
                prevX = trail.x;
                prevY = trail.y;
            });

            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        // Cursor hover effect
        const hoverElements = document.querySelectorAll('a, button, .btn-primary, .feature-card, .stat-card');
        hoverElements.forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
        });

        // Particle Canvas
        const particleCanvas = document.getElementById('particle-canvas');
        const pCtx = particleCanvas.getContext('2d');

        function resizeParticleCanvas() {
            particleCanvas.width = window.innerWidth;
            particleCanvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeParticleCanvas);
        resizeParticleCanvas();

        const particles = [];
        for (let i = 0; i < 50; i++) {
            particles.push({
                x: Math.random() * particleCanvas.width,
                y: Math.random() * particleCanvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                radius: Math.random() * 2 + 1,
                color: ['rgba(0, 102, 255, 0.5)', 'rgba(0, 180, 216, 0.5)', 'rgba(124, 58, 237, 0.5)'][Math.floor(Math.random() * 3)]
            });
        }

        function drawParticles() {
            pCtx.clearRect(0, 0, particleCanvas.width, particleCanvas.height);

            particles.forEach((p, i) => {
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > particleCanvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > particleCanvas.height) p.vy *= -1;

                pCtx.beginPath();
                pCtx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                pCtx.fillStyle = p.color;
                pCtx.fill();

                // Draw connections
                particles.forEach((p2, j) => {
                    if (i !== j) {
                        const dx = p.x - p2.x;
                        const dy = p.y - p2.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 150) {
                            pCtx.beginPath();
                            pCtx.moveTo(p.x, p.y);
                            pCtx.lineTo(p2.x, p2.y);
                            pCtx.strokeStyle = `rgba(0, 102, 255, ${0.1 * (1 - dist / 150)})`;
                            pCtx.stroke();
                        }
                    }
                });
            });

            requestAnimationFrame(drawParticles);
        }
        drawParticles();

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

            ctx.fillStyle = '#0a0a1a';
            ctx.fillRect(0, 0, width, height);

            const gradient = ctx.createRadialGradient(
                width * (0.3 + Math.sin(time) * 0.2),
                height * (0.3 + Math.cos(time * 0.7) * 0.2),
                0,
                width * 0.5,
                height * 0.5,
                Math.max(width, height)
            );

            gradient.addColorStop(0, `rgba(0, ${Math.floor(102 + Math.sin(time) * 30)}, ${Math.floor(255)}, 0.15)`);
            gradient.addColorStop(0.5, `rgba(0, ${Math.floor(180 + Math.cos(time * 0.5) * 30)}, ${Math.floor(216 + Math.sin(time * 0.5) * 30)}, 0.08)`);
            gradient.addColorStop(1, 'rgba(10, 10, 26, 0)');

            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, width, height);

            const gradient2 = ctx.createRadialGradient(
                width * (0.7 + Math.cos(time * 0.8) * 0.2),
                height * (0.7 + Math.sin(time * 0.6) * 0.2),
                0,
                width * 0.5,
                height * 0.5,
                Math.max(width, height) * 0.8
            );

            gradient2.addColorStop(0, `rgba(${Math.floor(124 + Math.sin(time * 0.5) * 30)}, ${Math.floor(58 + Math.cos(time) * 20)}, 237, 0.1)`);
            gradient2.addColorStop(1, 'rgba(10, 10, 26, 0)');

            ctx.fillStyle = gradient2;
            ctx.fillRect(0, 0, width, height);

            requestAnimationFrame(drawGradient);
        }

        drawGradient();

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('backdrop-blur-xl', 'py-4', 'border-b', 'border-white/10');
                navbar.style.background = 'rgba(10, 10, 26, 0.8)';
                navbar.classList.remove('py-6');
            } else {
                navbar.classList.remove('backdrop-blur-xl', 'py-4', 'border-b', 'border-white/10');
                navbar.style.background = 'transparent';
                navbar.classList.add('py-6');
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

                let current = 0;
                const increment = target / 60;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current) + (target === 98 || target === 100 ? '%' : target === 500 ? '+' : '');
                }, 25);
            });
        };

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.py-24');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        if (statsSection) {
            statsObserver.observe(statsSection);
        }

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

        // Parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;

            document.querySelectorAll('.orb').forEach((orb, index) => {
                const speed = 0.05 + (index * 0.02);
                orb.style.transform = `translateY(${scrolled * speed}px)`;
            });

            document.querySelectorAll('.geo-shape').forEach((shape, index) => {
                const speed = 0.03 + (index * 0.01);
                shape.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Magnetic button effect
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;

                btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
            });

            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translate(0, 0)';
            });
        });

        // 3D Tilt effect for feature cards with mouse tracking
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 15;
                const rotateY = (centerX - x) / 15;

                // Update CSS custom properties for spotlight effect
                card.style.setProperty('--mouse-x', (x / rect.width * 100) + '%');
                card.style.setProperty('--mouse-y', (y / rect.height * 100) + '%');

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-15px) scale(1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
            });
        });

        // 3D Tilt for stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;

                card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-20px)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) translateY(0)';
            });
        });
    </script>
</body>

</html>