<?php
if (!isset($auth)) {
    require_once 'config/auth.php';
    $auth = requireAuth();
}

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
                <?php if ($auth->hasRole('demandeur')): ?>
                <a class="nav-link" href="dashboard-demandeur.php">
                    <i class="bi bi-house me-1"></i> Accueil
                </a>
                <a class="nav-link" href="pages/demandes/nouvelle-demande.php">
                    <i class="bi bi-plus-circle me-1"></i> Nouvelle Demande
                </a>
                <a class="nav-link" href="pages/demandes/mes-demandes.php">
                    <i class="bi bi-list-ul me-1"></i> Mes Demandes
                </a>
                <?php elseif ($auth->hasRole('validateur')): ?>
                <a class="nav-link" href="dashboard-validateur.php">
                    <i class="bi bi-house me-1"></i> Accueil
                </a>
                <a class="nav-link" href="pages/validation/demandes-a-valider.php">
                    <i class="bi bi-check-circle me-1"></i> À Valider
                </a>
                <a class="nav-link" href="pages/validation/historique.php">
                    <i class="bi bi-clock-history me-1"></i> Historique
                </a>
                <?php elseif ($auth->hasRole('administrateur')): ?>
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
                        <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                    </div>
                    <span class="d-none d-sm-inline">
                        <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                    </span>
                    <i class="bi bi-chevron-down ms-2"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header">
                        <strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong><br>
                        <small
                            class="text-muted"><?php echo htmlspecialchars($user['service'] . ' - ' . ucfirst($user['role'])); ?></small>
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

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="p-3">
            <h6 class="text-white-50 text-uppercase small mb-3">Navigation</h6>
            <ul class="nav flex-column">
                <?php if ($auth->hasRole('demandeur')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard-demandeur.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/demandes/nouvelle-demande.php">
                        <i class="bi bi-plus-circle"></i>
                        Nouvelle Demande
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/demandes/mes-demandes.php">
                        <i class="bi bi-list-ul"></i>
                        Mes Demandes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/demandes/brouillons.php">
                        <i class="bi bi-file-earmark"></i>
                        Brouillons
                    </a>
                </li>
                <?php elseif ($auth->hasRole('validateur')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard-validateur.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/validation/demandes-a-valider.php">
                        <i class="bi bi-check-circle"></i>
                        À Valider
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/validation/mon-equipe.php">
                        <i class="bi bi-people"></i>
                        Mon Équipe
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/validation/historique.php">
                        <i class="bi bi-clock-history"></i>
                        Historique
                    </a>
                </li>
                <?php elseif ($auth->hasRole('administrateur')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard-admin.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/admin/gestion-demandes.php">
                        <i class="bi bi-clipboard-data"></i>
                        Gestion Demandes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/admin/gestion-utilisateurs.php">
                        <i class="bi bi-people"></i>
                        Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/admin/types-besoins.php">
                        <i class="bi bi-tags"></i>
                        Types de Besoins
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/admin/rapports.php">
                        <i class="bi bi-graph-up"></i>
                        Rapports
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">

            <h6 class="text-white-50 text-uppercase small mb-3">Outils</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="pages/notifications.php">
                        <i class="bi bi-bell"></i>
                        Notifications
                        <?php if ($notificationsCount > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $notificationsCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/profil.php">
                        <i class="bi bi-person"></i>
                        Mon Profil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/aide.php">
                        <i class="bi bi-question-circle"></i>
                        Aide
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Overlay pour mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Contenu principal -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <!-- Messages flash -->
            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
            <div class="alert alert-<?php echo ($flash['type'] == 'error') ? 'danger' : $flash['type']; ?> alert-dismissible fade show"
                role="alert">
                <i
                    class="bi bi-<?php echo $flash['type'] == 'success' ? 'check-circle' : ($flash['type'] == 'error' ? 'exclamation-triangle' : 'info-circle'); ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Breadcrumb (optionnel) -->
            <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                    <?php if ($index === count($breadcrumbs) - 1): ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($breadcrumb['title']); ?>
                    </li>
                    <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>">
                            <?php echo htmlspecialchars($breadcrumb['title']); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>

            <script>
            // Gestion du sidebar
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const mainContent = document.getElementById('mainContent');

                if (window.innerWidth >= 992) {
                    // Desktop: slide content
                    sidebar.classList.toggle('show');
                    mainContent.classList.toggle('shifted');
                } else {
                    // Mobile: overlay
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                }
            }

            function closeSidebar() {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }

            // Gestion responsive
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    document.getElementById('sidebarOverlay').classList.remove('show');
                }
            });

            // Chargement des notifications
            document.addEventListener('DOMContentLoaded', function() {
                loadNotifications();

                // Actualiser les notifications toutes les 30 secondes
                setInterval(loadNotifications, 30000);

                // Marquer comme active le lien actuel
                markActiveNavLink();
            });

            // Chargement des notifications
            function loadNotifications() {
                fetch('api/get-notifications.php')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('notificationsList');
                        if (data.success && data.notifications) {
                            if (data.notifications.length === 0) {
                                container.innerHTML =
                                    '<div class="dropdown-item-text text-center text-muted py-3">Aucune notification</div>';
                            } else {
                                container.innerHTML = data.notifications.map(notification =>
                                    `<a class="dropdown-item ${!notification.lu ? 'bg-light' : ''}" href="pages/demandes/detail-demande.php?id=${notification.demande_id}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bi bi-${getNotificationIcon(notification.type)} text-${getNotificationColor(notification.type)}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 small">${notification.titre}</h6>
                                    <p class="mb-1 small text-muted">${notification.message}</p>
                                    <small class="text-muted">${formatDate(notification.date_creation)}</small>
                                </div>
                            </div>
                        </a>`
                                ).join('');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des notifications:', error);
                        document.getElementById('notificationsList').innerHTML =
                            '<div class="dropdown-item-text text-center text-danger py-3">Erreur de chargement</div>';
                    });
            }

            // Icônes pour les types de notifications
            function getNotificationIcon(type) {
                const icons = {
                    'nouvelle_demande': 'plus-circle',
                    'validation_requise': 'check-circle',
                    'demande_validee': 'check-circle-fill',
                    'demande_rejetee': 'x-circle',
                    'demande_traitee': 'check-all'
                };
                return icons[type] || 'bell';
            }

            // Couleurs pour les types de notifications
            function getNotificationColor(type) {
                const colors = {
                    'nouvelle_demande': 'primary',
                    'validation_requise': 'warning',
                    'demande_validee': 'success',
                    'demande_rejetee': 'danger',
                    'demande_traitee': 'success'
                };
                return colors[type] || 'primary';
            }

            // Formatage de date simple
            function formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffTime = Math.abs(now - date);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays === 1) {
                    return 'Hier';
                } else if (diffDays < 7) {
                    return `Il y a ${diffDays} jours`;
                } else {
                    return date.toLocaleDateString('fr-FR');
                }
            }

            // Marquer le lien actif
            function markActiveNavLink() {
                const currentPath = window.location.pathname;
                const navLinks = document.querySelectorAll('.sidebar .nav-link');

                navLinks.forEach(link => {
                    if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // Auto-hide des alertes
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                });
            }, 5000);
            </script>