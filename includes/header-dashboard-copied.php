<?php
require 'config/auth.php';

$auth = getAuth();
$user = $auth->getCurrentUser();

require_once 'includes/functions.php';

// Génération du token CSRF si pas déjà fait
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Notifications non lues
try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND lu = 0");
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $notificationsCount = $stmt->fetch()['count'];
} catch (Exception $e) {
    $notificationsCount = 0;
}
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
    <link rel="stylesheet" href="assets/css/style-dashboard.css">

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
        --sidebar-width: 280px;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
    }

    /* Navigation principale */
    .navbar-main {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-color) !important;
        font-size: 1.5rem;
    }

    .navbar-nav .nav-link {
        color: #495057;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        margin: 0 0.25rem;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(0, 86, 179, 0.1);
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    .navbar-nav .nav-link.active {
        background-color: var(--primary-color);
        color: white !important;
    }

    /* Sidebar */
    .sidebar {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        width: var(--sidebar-width);
        position: fixed;
        top: 0;
        left: -var(--sidebar-width);
        transition: all 0.3s ease;
        z-index: 1050;
        padding-top: 80px;
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
    }

    .sidebar.show {
        left: 0;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.9);
        padding: 1rem 1.5rem;
        border-radius: 0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        text-decoration: none;
        border-left: 3px solid transparent;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border-left-color: #fff;
        transform: translateX(5px);
    }

    .sidebar .nav-link i {
        width: 20px;
        margin-right: 0.75rem;
    }

    /* Main content */
    .main-content {
        margin-left: 0;
        min-height: 100vh;
        padding-top: 100px;
        transition: all 0.3s ease;
    }

    .main-content.shifted {
        margin-left: var(--sidebar-width);
    }

    /* Overlay pour mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* Notifications badge */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1;
        min-width: 18px;
        text-align: center;
        animation: pulse 2s infinite;
    }

    /* User dropdown */
    .user-dropdown {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 0.25rem 1rem 0.25rem 0.25rem;
        transition: all 0.3s ease;
    }

    .user-dropdown:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 0.5rem;
    }

    /* Cards modernes */
    .modern-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
    }

    .modern-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (min-width: 992px) {
        .sidebar {
            position: relative;
            left: 0;
            padding-top: 20px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: 120px;
        }

        .sidebar-overlay {
            display: none;
        }
    }

    @media (max-width: 991.98px) {
        .main-content {
            padding-top: 100px;
        }

        .sidebar {
            padding-top: 100px;
        }
    }

    /* Animations */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Bouton sidebar toggle */
    .sidebar-toggle {
        background: none;
        border: none;
        color: #495057;
        font-size: 1.5rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .sidebar-toggle:hover {
        background-color: rgba(0, 0, 0, 0.1);
        transform: scale(1.1);
    }

    /* Breadcrumb */
    .breadcrumb {
        background: transparent;
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }
    </style>
</head>

<body>
    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-main fixed-top">
        <div class="container-fluid">
            <!-- Sidebar toggle -->
            <button class="sidebar-toggle d-lg-none me-3" type="button" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <!-- Brand -->
            <a class="navbar-brand" href="<?php echo redirectByRole($user['role']); ?>">
                <i class="bi bi-clipboard-data me-2"></i>
                Expression du Besoin
            </a>

            <!-- Navigation -->
            <div class="navbar-nav d-none d-lg-flex flex-row ms-auto me-3">
                <?php if ($auth->hasRole('Demandeur')): ?>
                <a class="nav-link" href="dashboard-demandeur.php">
                    <i class="bi bi-house me-1"></i> Accueil
                </a>
                <a class="nav-link" href="pages/demandes/nouvelle-demande.php">
                    <i class="bi bi-plus-circle me-1"></i> Nouvelle Demande
                </a>
                <a class="nav-link" href="pages/demandes/mes-demandes.php">
                    <i class="bi bi-list-ul me-1"></i> Mes Demandes
                </a>
                <?php elseif ($auth->hasRole('Validateur')): ?>
                <a class="nav-link" href="dashboard-validateur.php">
                    <i class="bi bi-house me-1"></i> Accueil
                </a>
                <a class="nav-link" href="pages/validation/demandes-a-valider.php">
                    <i class="bi bi-check-circle me-1"></i> À Valider
                </a>
                <a class="nav-link" href="pages/validation/historique.php">
                    <i class="bi bi-clock-history me-1"></i> Historique
                </a>
                <?php elseif ($auth->hasRole('Administrateur')): ?>
                <a class="nav-link" href="dashboard-admin.php">
                    <i class="bi bi-house me-1"></i> Accueil
                </a>
                <a class="nav-link" href="pages/admin/gestion-demandes.php">
                    <i class="bi bi-clipboard-data me-1"></i> Demandes
                </a>
                <a class="nav-link" href="pages/admin/gestion-utilisateurs.php">
                    <i class="bi bi-people me-1"></i> Utilisateurs
                </a>
                <?php endif; ?>

                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <?php if ($notificationsCount > 0): ?>
                        <span class="notification-badge"><?php echo $notificationsCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                        <h6 class="dropdown-header">
                            <i class="bi bi-bell me-2"></i>
                            Notifications
                        </h6>
                        <div class="dropdown-divider"></div>
                        <div id="notificationsList" style="max-height: 300px; overflow-y: auto;">
                            <!-- Contenu chargé dynamiquement -->
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="pages/notifications.php">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- User dropdown -->
            <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none user-dropdown text-dark" href="#" id="userDropdown"
                    role="button" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['nom'], 0, 1)); ?>
                    </div>
                    <span class="d-none d-sm-inline">
                        <?php echo htmlspecialchars($user['nom']); ?>
                    </span>
                    <i class="bi bi-chevron-down ms-2"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header">
                        <strong><?php echo htmlspecialchars($user['nom']); ?></strong><br>
                        <small
                            class="text-muted"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="pages/profil.php">
                            <i class="bi bi-person me-2"></i> Mon Profil
                        </a></li>
                    <li><a class="dropdown-item" href="pages/notifications.php">
                            <i class="bi bi-bell me-2"></i> Notifications
                            <?php if ($notificationsCount > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $notificationsCount; ?></span>
                            <?php endif; ?>
                        </a></li>
                    <li><a class="dropdown-item" href="pages/aide.php">
                            <i class="bi bi-question-circle me-2"></i> Aide
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="login.php?logout=1">
                            <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                        </a></li>
                </ul>
            </div>
        </div>
    </nav>

  