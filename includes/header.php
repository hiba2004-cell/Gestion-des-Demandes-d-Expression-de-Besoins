<?php
ob_start();
session_start();
require_once __DIR__ . '/functions.php';

// Variables pour la navigation
$current_page = basename($_SERVER['PHP_SELF']);

$unreadCount = getUnreadNotificationCount($_SESSION['user_service'] ?? 0,
    isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Administrateur' ? 1 : 0
);

// call this before any output
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    // Validate $lang if needed
    setLanguage($lang);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo getSetence('header_title'); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
    :root {
        --primary-color: #0056b3;
        --secondary-color: #6c757d;
        --success-color: #198754;
        --info-color: #0dcaf0;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #212529;
    }

    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-color) !important;
    }

    .sidebar {
        background: linear-gradient(180deg, #0056b3 0%, #004494 100%);
        min-height: calc(100vh - 76px);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 2px 0;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        transform: translateX(5px);
    }

    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, #0056b3, #007bff);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #004494, #0056b3);
        transform: translateY(-1px);
    }

    .table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #495057;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
    }

    .stats-card.success {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stats-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stats-card.danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .alert {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .me-custom {
        /* Adjust this value (e.g., 10px, 12px, 15px, etc.) */
        margin-right: 90px !important;
    }

    .chat-container {
        width: 100%;
        max-width: 700px;
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .role-selector {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .role-selector button {
        padding: 8px 16px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .role-selector button.active {
        background: #667eea;
        color: white;
    }

    .role-selector button:hover {
        transform: scale(1.05);
    }

    .messages {
        max-height: 500px;
        overflow-y: auto;
        padding: 20px 0;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .msg {
        padding: 12px 16px;
        border-radius: 12px;
        max-width: 75%;
        animation: messageIn 0.4s ease-out;
        word-wrap: break-word;
    }

    @keyframes messageIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .msg:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .admin {
        background: #e3f2fd;
        color: #1976d2;
        align-self: flex-start;
        border-left: 4px solid #1976d2;
    }

    .validateur {
        background: #c8e6c9;
        color: #388e3c;
        align-self: flex-end;
        border-right: 4px solid #388e3c;
    }

    .msg strong {
        display: block;
        font-size: 0.9em;
        margin-bottom: 5px;
        opacity: 0.8;
    }

    .msg small {
        display: block;
        font-size: 0.75em;
        margin-top: 8px;
        opacity: 0.6;
    }

    .send-box {
        display: flex;
        gap: 10px;
    }

    .send-box input {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #ddd;
        border-radius: 25px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .send-box input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .send-box button {
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .send-box button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .send-box button:active {
        transform: scale(0.95);
    }

    /* Scrollbar personnalisé */
    .messages::-webkit-scrollbar {
        width: 8px;
    }

    .messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .messages::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }

    .messages::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <div class="navbar-brand flex align-items-center">
                <!-- <i class="bi bi-clipboard-data me-2"></i> -->
                <i id="my-toggler" class="bi bi-clipboard-data me-2" style="cursor: pointer; font-size: 1.25rem;"></i>
                <a class="navbar-brand" href="/besoins/index.php">
                    <?php echo getSetence('header_title'); ?>
                </a>
            </div>

            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/besoins/index.php">
                            <i class="bi bi-house me-1"></i> 
                            <!-- <?php echo getSetence('accueil'); ?> -->
                        </a>
                    </li>

                    <?php if($_SESSION['user_role'] != 'Demandeur'): ?>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="/besoins/pages/notifications.php">
                            <i class="bi bi-bell me-1"></i>
                            <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span
                                class="position-absolute -top-2 start-1 translate-middle badge rounded-pill bg-danger">
                                <?= $unreadCount ?>
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="/besoins/chat.php">
                           <i class="fas fa-comments"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    
                   <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="changeLang" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-translate me-1"></i>
                             <!-- <?php echo getSetence('langue'); ?> -->
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <!-- French -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="?lang=fr">
                                    <img src="https://flagcdn.com/w20/fr.png" class="me-2" alt="FR">
                                    Français
                                </a>
                            </li>

                            <!-- Divider -->
                            <li><hr class="dropdown-divider"></li>

                            <!-- English -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="?lang=en">
                                    <img src="https://flagcdn.com/w20/gb.png" class="me-2" alt="EN">
                                    English
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item dropdown me-custom">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i> 
                            <!-- Options -->
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/besoins/pages/profil.php">
                                    <i class="bi bi-person-circle me-1"></i> Mon Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item bg-danger text-white" href="/besoins/logout.php">
                                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">

        <div class="row">
            <!-- Sidebar -->
            <?php if($_SESSION['user_role'] == 'Administrateur'): ?>
            <nav id="mainNavbar" class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                                href="/besoins/dashboard-admin.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'liste-besoins.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/liste-besoins.php">
                                <i class="bi bi-list-ul me-2"></i>
                                Liste des Besoins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'liste-utilisateurs.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/liste-utilisateurs.php">
                                <i class="bi bi-people me-2"></i>
                                Liste Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'ajouter-besoin.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/ajouter-besoin.php">
                                <i class="bi bi-plus-circle me-2"></i>
                                Ajouter un Besoin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'statistiques.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/statistiques.php">
                                <i class="bi bi-bar-chart me-2"></i>
                                Statistiques
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <h6
                                class="text-white sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
                                <span>Filtres Rapides</span>
                            </h6>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=Traitée">
                                <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>
                                Besoins à Valider
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?priorite=Urgente">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"
                                    style="font-size: 0.8rem;"></i>
                                Priorité Critique
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=En+cours+de+validation">
                                <i class="bi bi-clock-fill text-warning me-2" style="font-size: 0.8rem;"></i>
                                En Cours
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php endif;?>
            <main class="col-md-9 ms-sm-auto mx-auto col-lg-10 px-md-4">