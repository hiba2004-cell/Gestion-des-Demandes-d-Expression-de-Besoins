<?php
require_once 'config/auth.php';

$auth = new Auth();

// Si déjà connecté, rediriger
// if ($auth->isLoggedIn()) {
//     $user = $auth->getCurrentUser();
//     header("Location: " . redirectByRole($user['role']));
//     exit();
// }
if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    $redirectUrl = redirectByRole($user['role']);
    
    if ($redirectUrl) {
        header("Location: $redirectUrl");
        exit();
    } else {
        // echo "Rôle inconnu. Contactez l’administrateur.";
        print_r($auth->get_current_user());
        exit();
    }
}


$error = '';
$success = '';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez renseigner tous les champs.';
    } else {
        if ($auth->login($email, $password)) {
            $user = $auth->getCurrentUser();
            header("Location: " . redirectByRole($user['role']));
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    $auth->logout();
    $success = 'Vous avez été déconnecté avec succès.';
}

// Génération du token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Expression du Besoin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: slideInUp 0.8s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .login-header::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: white;
        border-radius: 50% 50% 0 0 / 100% 100% 0 0;
    }

    .login-logo {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
        background: white;
        transform: translateY(-2px);
    }

    .btn-login {
        background: var(--primary-gradient);
        border: none;
        border-radius: 15px;
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .alert {
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(248, 215, 218, 0.8) 0%, rgba(245, 198, 203, 0.8) 100%);
        color: #721c24;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(212, 237, 218, 0.8) 0%, rgba(195, 230, 203, 0.8) 100%);
        color: #155724;
    }

    .floating-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: -1;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 120px;
        height: 120px;
        top: 20%;
        right: 10%;
        animation-delay: -2s;
    }

    .shape:nth-child(3) {
        width: 60px;
        height: 60px;
        bottom: 10%;
        left: 20%;
        animation-delay: -4s;
    }

    .shape:nth-child(4) {
        width: 100px;
        height: 100px;
        bottom: 20%;
        right: 20%;
        animation-delay: -1s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }

    .demo-accounts {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 15px;
        padding: 1rem;
        margin-top: 1rem;
        color: white;
    }

    .demo-account {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 0.5rem;
        margin: 0.5rem 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .demo-account:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateX(5px);
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-group i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
        transition: color 0.3s ease;
    }

    .input-group .form-control {
        padding-left: 55px;
    }

    .input-group .form-control:focus+i {
        color: #667eea;
    }

    .password-toggle {
        position: absolute;
        right: 1px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 10;
        color: #6c757d;
        transition: color 0.3s ease;
        width: 20px;
        height: 20px;

    }





    .password-toggle:hover {
        color: #667eea;
    }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <!-- Header -->
                    <div class="login-header">
                        <div class="login-logo">
                            <i class="bi bi-clipboard-check display-4"></i>
                        </div>
                        <h2 class="fw-bold mb-0">Expression du Besoin</h2>
                        <p class="mb-0 opacity-75">Connectez-vous à votre espace</p>
                    </div>

                    <!-- Formulaire -->
                    <div class="p-4">
                        <!-- Alertes -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Adresse email" required
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                <i class="bi bi-envelope-fill"></i>
                            </div>

                            <div class="input-group">
                                <div class="position-relative w-100">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Mot de passe" required>
                                    <i class="bi bi-lock-fill"></i>
                                    <!-- <i class="bi bi-eye password-toggle right-0" onclick="togglePassword()"></i> -->
                                </div>
                            </div>


                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>

                            <button type="submit" name="login" class="btn btn-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Se connecter
                            </button>
                        </form>

                        <!-- Comptes de démonstration -->
                        <div class="demo-accounts">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-info-circle me-2"></i>
                                Comptes de démonstration
                            </h6>
                            <div class="demo-account" onclick="fillCredentials('admin@admin.com', '12345')">
                                <strong>Administrateur:</strong> admin@admin.com
                            </div>
                            <div class="demo-account" onclick="fillCredentials('chef@company.com', '12345')">
                                <strong>Validateur:</strong> chef@company.com
                            </div>
                            <div class="demo-account" onclick="fillCredentials('leila.smith@tech.org', '12345')">
                                <strong>Demandeur:</strong> leila.smith@tech.org
                            </div>
                            <small class="text-white-50 d-block mt-2">
                                Mot de passe pour tous: 12345
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des éléments
        const elements = document.querySelectorAll('.input-group, .btn-login, .demo-accounts');
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';

            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Effet de focus amélioré
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentNode.style.transform = 'scale(1)';
            });
        });

        // Auto-masquage des alertes
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });

    // Basculer visibilité mot de passe
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Remplir les credentials de démo
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;

        // Animation de remplissage
        const inputs = [document.getElementById('email'), document.getElementById('password')];
        inputs.forEach((input, index) => {
            setTimeout(() => {
                input.style.background = '#e3f2fd';
                setTimeout(() => {
                    input.style.background = '';
                }, 500);
            }, index * 100);
        });
    }

    // Validation du formulaire
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            e.preventDefault();

            // Secouer le formulaire
            this.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        }
    });

    // Animation de secousse
    const shakeCSS = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
    const style = document.createElement('style');
    style.textContent = shakeCSS;
    document.head.appendChild(style);

    // Particules flottantes interactives
    document.addEventListener('mousemove', function(e) {
        const shapes = document.querySelectorAll('.shape');
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;

        shapes.forEach((shape, index) => {
            const speed = (index + 1) * 0.5;
            const xMove = (x - 0.5) * speed;
            const yMove = (y - 0.5) * speed;

            shape.style.transform = `translate(${xMove}px, ${yMove}px)`;
        });
    });

    // Effet de frappe pour le titre
    const title = document.querySelector('.login-header h2');
    const originalText = title.textContent;
    title.textContent = '';

    let i = 0;
    const typeWriter = () => {
        if (i < originalText.length) {
            title.textContent += originalText.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        }
    };

    setTimeout(typeWriter, 500);
    </script>
</body>

</html>