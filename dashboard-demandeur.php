<?php
$page_title = "Espace Demandeur";
require_once 'config/auth.php';

// Vérification authentification et rôle
$auth = requireAuth();
$user = $auth->getCurrentUser();

// Redirection si pas le bon rôle
if (!$auth->hasRole('Demandeur')) {
    header("Location: " . redirectByRole($user['role']));
    exit();
}

require_once 'includes/functions.php';
require_once 'includes/header-dashboard.php';

// Statistiques du demandeur
try {
    $conn = getConnection();
    
    // Mes demandes par statut
    $stmt = $conn->prepare("
        SELECT statut, COUNT(*) as count 
        FROM demandes 
        WHERE demandeur_id = :user_id 
        GROUP BY statut
    ");
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $mesDemandesParStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mes demandes récentes
    $stmt = $conn->prepare("
        SELECT d.*, tb.nom as type_besoin, tb.couleur as type_couleur,
               CONCAT(v.prenom, ' ', v.nom) as validateur_nom
        FROM demandes d
        LEFT JOIN types_besoins tb ON d.type_besoin_id = tb.id
        LEFT JOIN users v ON d.validateur_id = v.id
        WHERE d.demandeur_id = :user_id 
        ORDER BY d.date_creation DESC 
        LIMIT 5
    ");
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $mesDemandesRecentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Notifications non lues
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM notifications 
        WHERE user_id = :user_id AND lu = 0
    ");
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $notificationsNonLues = $stmt->fetch()['count'];
    
    // Total mes demandes
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total,
               COUNT(CASE WHEN statut = 'traitee' THEN 1 END) as traitees,
               COUNT(CASE WHEN statut = 'rejetee' THEN 1 END) as rejetees,
               AVG(cout_estime) as cout_moyen
        FROM demandes 
        WHERE demandeur_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $mesStats = $stmt->fetch();
    
} catch (Exception $e) {
    setFlashMessage('error', 'Erreur lors du chargement des données : ' . $e->getMessage());
    $mesDemandesParStatut = [];
    $mesDemandesRecentes = [];
    $notificationsNonLues = 0;
    $mesStats = ['total' => 0, 'traitees' => 0, 'rejetees' => 0, 'cout_moyen' => 0];
}
?>

<div class="dashboard-demandeur">
    <!-- Header avec animation -->
    <div class="dashboard-header animate__animated animate__fadeInDown">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-6 fw-bold text-primary">
                    <i class="bi bi-person-circle me-2 animate__animated animate__bounceIn"></i>
                    Bonjour <?php echo htmlspecialchars($user['prenom']); ?> !
                </h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-building me-1"></i>
                    <?php echo htmlspecialchars($user['service'] . ' - ' . $user['poste']); ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="pages/demandes/nouvelle-demande.php" class="btn btn-primary btn-lg animate-btn">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nouvelle Demande
                </a>
                <a href="pages/demandes/mes-demandes.php" class="btn btn-outline-primary">
                    <i class="bi bi-list-ul me-1"></i>
                    Mes Demandes
                </a>
            </div>
        </div>
    </div>

    <!-- Cartes KPI avec animations -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card stats-card-primary animate-card" data-delay="0">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="bi bi-clipboard-data display-4"></i>
                    </div>
                    <h3 class="card-title counter" data-count="<?php echo $mesStats['total']; ?>">0</h3>
                    <p class="card-text">Total Demandes</p>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card stats-card-success animate-card" data-delay="100">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="bi bi-check-circle display-4"></i>
                    </div>
                    <h3 class="card-title counter" data-count="<?php echo $mesStats['traitees']; ?>">0</h3>
                    <p class="card-text">Demandes Traitées</p>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success"
                            style="width: <?php echo $mesStats['total'] > 0 ? ($mesStats['traitees'] / $mesStats['total']) * 100 : 0; ?>%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card stats-card-warning animate-card" data-delay="200">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="bi bi-bell display-4"></i>
                    </div>
                    <h3 class="card-title counter" data-count="<?php echo $notificationsNonLues; ?>">0</h3>
                    <p class="card-text">Notifications</p>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning"
                            style="width: <?php echo min(100, $notificationsNonLues * 20); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card stats-card-info animate-card" data-delay="300">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="bi bi-currency-euro display-4"></i>
                    </div>
                    <h3 class="card-title"><?php echo formatCurrency($mesStats['cout_moyen']); ?></h3>
                    <p class="card-text">Coût Moyen</p>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row">
        <!-- Mes demandes récentes -->
        <div class="col-lg-8 mb-4">
            <div class="card modern-card animate-card" data-delay="400">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Mes Demandes Récentes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($mesDemandesRecentes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Demande</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Validateur</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mesDemandesRecentes as $index => $demande): ?>
                                <tr class="table-row-animate" style="animation-delay: <?php echo $index * 100; ?>ms">
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($demande['titre']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($demande['description'], 0, 50)) . '...'; ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                            style="background-color: <?php echo $demande['type_couleur']; ?>">
                                            <?php echo htmlspecialchars($demande['type_besoin']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo getStatusClass($demande['statut']); ?> status-badge">
                                            <?php echo getStatusLabel($demande['statut']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($demande['validateur_nom']): ?>
                                        <small><?php echo htmlspecialchars($demande['validateur_nom']); ?></small>
                                        <?php else: ?>
                                        <span class="text-muted">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo formatDateTime($demande['date_creation']); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="pages/demandes/detail-demande.php?id=<?php echo $demande['id']; ?>"
                                                class="btn btn-outline-primary btn-sm hover-lift"
                                                data-bs-toggle="tooltip" title="Voir les détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if (in_array($demande['statut'], ['en_attente', 'rejetee'])): ?>
                                            <a href="pages/demandes/modifier-demande.php?id=<?php echo $demande['id']; ?>"
                                                class="btn btn-outline-warning btn-sm hover-lift"
                                                data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <a href="pages/demandes/mes-demandes.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-right me-1"></i>
                            Voir toutes mes demandes
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted animate__animated animate__bounceIn"></i>
                            <h4 class="mt-3 text-muted">Aucune demande pour le moment</h4>
                            <p class="text-muted">Créez votre première demande d'expression de besoin</p>
                            <a href="pages/demandes/nouvelle-demande.php" class="btn btn-primary btn-lg mt-2">
                                <i class="bi bi-plus-circle me-2"></i>
                                Créer une Demande
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar droite -->
        <div class="col-lg-4">
            <!-- Répartition par statut -->
            <div class="card modern-card animate-card mb-4" data-delay="500">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Mes Demandes par Statut
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($mesDemandesParStatut)): ?>
                    <canvas id="mesDemandesChart" width="300" height="200"></canvas>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-graph-up text-muted display-4"></i>
                        <p class="text-muted mt-2 mb-0">Aucune donnée disponible</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card modern-card animate-card mb-4" data-delay="600">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="pages/demandes/nouvelle-demande.php" class="btn btn-primary hover-lift">
                            <i class="bi bi-plus-circle me-2"></i>
                            Nouvelle Demande
                        </a>
                        <a href="pages/demandes/mes-demandes.php" class="btn btn-outline-primary hover-lift">
                            <i class="bi bi-list-ul me-2"></i>
                            Consulter l'Historique
                        </a>
                        <a href="pages/notifications.php" class="btn btn-outline-warning hover-lift">
                            <i class="bi bi-bell me-2"></i>
                            Notifications
                            <?php if ($notificationsNonLues > 0): ?>
                            <span class="badge bg-danger ms-1"><?php echo $notificationsNonLues; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/pages/profil.php" class="btn btn-outline-secondary hover-lift">
                            <i class="bi bi-person me-2"></i>
                            Mon Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Conseils -->
            <div class="card modern-card animate-card" data-delay="700">
                <div class="card-header bg-gradient-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Conseils
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tips-container">
                        <div class="tip-item mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <small>Décrivez précisément votre besoin pour accélérer la validation</small>
                        </div>
                        <div class="tip-item mb-3">
                            <i class="bi bi-clock text-info me-2"></i>
                            <small>Les demandes urgentes sont traitées en priorité</small>
                        </div>
                        <div class="tip-item mb-3">
                            <i class="bi bi-paperclip text-warning me-2"></i>
                            <small>Ajoutez des pièces jointes pour justifier votre demande</small>
                        </div>
                        <div class="tip-item">
                            <i class="bi bi-bell text-primary me-2"></i>
                            <small>Activez les notifications pour suivre l'avancement</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animations personnalisées pour le dashboard demandeur */
.dashboard-demandeur {
    animation: fadeInUp 0.6s ease-out;
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

.animate-card {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInCard 0.6s ease forwards;
}

.animate-card[data-delay="100"] {
    animation-delay: 0.1s;
}

.animate-card[data-delay="200"] {
    animation-delay: 0.2s;
}

.animate-card[data-delay="300"] {
    animation-delay: 0.3s;
}

.animate-card[data-delay="400"] {
    animation-delay: 0.4s;
}

.animate-card[data-delay="500"] {
    animation-delay: 0.5s;
}

.animate-card[data-delay="600"] {
    animation-delay: 0.6s;
}

.animate-card[data-delay="700"] {
    animation-delay: 0.7s;
}

@keyframes slideInCard {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.stats-card:hover::before {
    transform: translateX(100%);
}

.stats-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.stats-card-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
}

.stats-card-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.icon-wrapper {
    position: relative;
    display: inline-block;
}

.icon-wrapper::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.icon-wrapper i {
    position: relative;
    z-index: 1;
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }
}

.counter {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
}

.modern-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #0dcaf0 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.animate-btn {
    position: relative;
    overflow: hidden;
}

.animate-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.animate-btn:hover::before {
    left: 100%;
}

.table-row-animate {
    animation: slideInRow 0.5s ease forwards;
    opacity: 0;
}

@keyframes slideInRow {
    to {
        opacity: 1;
    }
}

.status-badge {
    animation: pulse 1s infinite;
}

.tip-item {
    padding: 0.5rem;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.tip-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.empty-state {
    padding: 3rem 1rem;
    text-align: center;
}

/* Animations au scroll */
@media (prefers-reduced-motion: no-preference) {
    .card {
        animation: fadeInOnScroll 0.6s ease;
    }
}

@keyframes fadeInOnScroll {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    animateCounters();

    // Animation des cartes au scroll
    observeElements();

    // Graphique si données disponibles
    <?php if (!empty($mesDemandesParStatut)): ?>
    createStatusChart();
    <?php endif; ?>

    // Tooltips
    initializeTooltips();
});

// Animation des compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Observer pour animations au scroll
function observeElements() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__fadeInUp');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.animate-card').forEach(card => {
        observer.observe(card);
    });
}

// Graphique des statuts
<?php if (!empty($mesDemandesParStatut)): ?>

function createStatusChart() {
    const ctx = document.getElementById('mesDemandesChart').getContext('2d');
    const data = <?php echo json_encode($mesDemandesParStatut); ?>;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => getStatusLabel(item.statut)),
            datasets: [{
                data: data.map(item => item.count),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1000
            }
        }
    });
}

function getStatusLabel(status) {
    const labels = {
        'en_attente': 'En attente',
        'en_cours_validation': 'En cours',
        'validee': 'Validée',
        'rejetee': 'Rejetée',
        'traitee': 'Traitée'
    };
    return labels[status] || status;
}
<?php endif; ?>

// Tooltips Bootstrap
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Effet de particules sur les cartes
document.querySelectorAll('.stats-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        createParticles(this);
    });
});

function createParticles(element) {
    for (let i = 0; i < 5; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255,255,255,0.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1000;
        `;

        const rect = element.getBoundingClientRect();
        particle.style.left = (rect.left + Math.random() * rect.width) + 'px';
        particle.style.top = (rect.top + Math.random() * rect.height) + 'px';

        document.body.appendChild(particle);

        // Animation
        particle.animate([{
                transform: 'scale(0) translateY(0)',
                opacity: 1
            },
            {
                transform: 'scale(1) translateY(-20px)',
                opacity: 0
            }
        ], {
            duration: 800,
            easing: 'ease-out'
        }).onfinish = () => particle.remove();
    }
}
</script>

<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- CDN Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<?php include 'includes/footer-dashboard.php'; ?>