<?php
ob_start();
session_start();
require_once __DIR__ . '/functions.php';

// Variables pour la navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Expression du Besoin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/style.css">

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
        margin-right: 45px !important; 
    }
    </style>
</head>

<body>
    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <div class="navbar-brand flex align-items-center">
                <!-- <i class="bi bi-clipboard-data me-2"></i> -->
                <i id="my-toggler" class="bi bi-clipboard-data me-2" style="cursor: pointer; font-size: 1.25rem;"></i>
                <a class="navbar-brand" href="/besoins/dashboard-admin.php">
                    Expression du Besoin
                </a>
            </div>

            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/besoins/index.php">
                            <i class="bi bi-house me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/besoins/pages/ajouter-besoin.php">
                            <i class="bi bi-plus-circle me-1"></i> Nouveau Besoin
                        </a>
                    </li>
                    <li class="nav-item dropdown me-custom">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i> Options
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/besoins/pages/statistiques.php">
                                    <i class="bi bi-bar-chart me-1"></i> Statistiques
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" onclick="exportData()">
                                    <i class="bi bi-download me-1"></i> Exporter
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="#" onclick="exportData()">
                                    <i class="bi bi-download me-1"></i> Profil
                                </a>
                            </li>
                            <li><a class="dropdown-item bg-danger" href="#" onclick="exportData()">
                                    <i class="bi bi-download me-1"></i> Deconnection
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
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=nouveau">
                                <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>
                                Nouveaux Besoins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?priorite=critique">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"
                                    style="font-size: 0.8rem;"></i>
                                Priorité Critique
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=en_cours">
                                <i class="bi bi-clock-fill text-warning me-2" style="font-size: 0.8rem;"></i>
                                En Cours
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto mx-auto col-lg-10 px-md-4">
                <div class="pt-3 pb-2 mb-3">
                    <?php 
                    // Affichage des messages flash
                    $flash = getFlashMessage();
                    if ($flash): 
                    ?>
                    <div class="alert alert-<?php echo ($flash['type'] == 'error') ? 'danger' : $flash['type']; ?> alert-dismissible fade show"
                        role="alert">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>