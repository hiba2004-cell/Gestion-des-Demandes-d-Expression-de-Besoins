<?php
$page_title = "Dashboard";
include 'includes/header.php';

// Récupération des statistiques
try {
    $stats = getStatistics();
    $recentBesoins = getBesoins([], 5, 0);
} catch (Exception $e) {
    $stats = ['total' => 0, 'par_statut' => [], 'par_priorite' => [], 'cout_total' => 0];
    $recentBesoins = ['besoins' => [], 'total' => 0];
    setFlashMessage('error', 'Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2 text-primary"></i>
        Dashboard - Expression du Besoin
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="pages/ajouter-besoin.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Nouveau Besoin
            </a>
            <a href="pages/liste-besoins.php" class="btn btn-outline-primary">
                <i class="bi bi-list-ul me-1"></i>
                Voir Tout
            </a>
        </div>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-data display-4 mb-2"></i>
                <h4 class="card-title"><?php echo $stats['total']; ?></h4>
                <p class="card-text">Total des Besoins</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4 mb-2"></i>
                <h4 class="card-title">
                    <?php 
                    $termines = array_filter($stats['par_statut'], function($item) {
                        return $item['statut'] === 'termine';
                    });
                    echo !empty($termines) ? reset($termines)['count'] : 0;
                    ?>
                </h4>
                <p class="card-text">Besoins Terminés</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-clock display-4 mb-2"></i>
                <h4 class="card-title">
                    <?php 
                    $enCours = array_filter($stats['par_statut'], function($item) {
                        return $item['statut'] === 'en_cours';
                    });
                    echo !empty($enCours) ? reset($enCours)['count'] : 0;
                    ?>
                </h4>
                <p class="card-text">En Cours</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="bi bi-currency-euro display-4 mb-2"></i>
                <h4 class="card-title"><?php echo formatCurrency($stats['cout_total']); ?></h4>
                <p class="card-text">Coût Total Estimé</p>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques et tableaux -->
<div class="row">
    <!-- Graphique par statut -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Répartition par Statut
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statutChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Graphique par priorité -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Répartition par Priorité
                </h5>
            </div>
            <div class="card-body">
                <canvas id="prioriteChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des besoins récents -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Besoins Récents
                </h5>
                <a href="pages/liste-besoins.php" class="btn btn-light btn-sm">
                    Voir Tous
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentBesoins['besoins'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Demandeur</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBesoins['besoins'] as $besoin): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($besoin['titre']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($besoin['description'], 0, 50)) . '...'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($besoin['demandeur_nom']); ?>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($besoin['demandeur_email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getPriorityClass($besoin['priorite']); ?>">
                                                <?php echo getPriorityLabel($besoin['priorite']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusClass($besoin['statut']); ?>">
                                                <?php echo getStatusLabel($besoin['statut']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($besoin['date_creation']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="pages/detail-besoin.php?id=<?php echo $besoin['id']; ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Voir les détails">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="pages/detail-besoin.php?id=<?php echo $besoin['id']; ?>&edit=1" 
                                                   class="btn btn-outline-warning btn-sm"
                                                   data-bs-toggle="tooltip" 
                                                   title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Aucun besoin enregistré</h4>
                        <p class="text-muted">Commencez par ajouter votre premier besoin</p>
                        <a href="pages/ajouter-besoin.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Ajouter un Besoin
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Alertes et notifications -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Besoins Critiques
                </h6>
            </div>
            <div class="card-body">
                <?php
                $critiques = getBesoins(['priorite' => 'critique'], 3, 0);
                if (!empty($critiques['besoins'])):
                ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($critiques['besoins'] as $critique): ?>
                            <li class="mb-2">
                                <a href="pages/detail-besoin.php?id=<?php echo $critique['id']; ?>" class="text-decoration-none">
                                    <strong><?php echo htmlspecialchars($critique['titre']); ?></strong>
                                </a>
                                <br>
                                <small class="text-muted">par <?php echo htmlspecialchars($critique['demandeur_nom']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($critiques['total'] > 3): ?>
                        <div class="mt-2">
                            <a href="pages/liste-besoins.php?priorite=critique" class="btn btn-sm btn-outline-warning">
                                Voir tous (<?php echo $critiques['total']; ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucun besoin critique actuellement</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations Système
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><strong>Version PHP:</strong> <?php echo PHP_VERSION; ?></li>
                    <li><strong>Total Besoins:</strong> <?php echo $stats['total']; ?></li>
                    <li><strong>Dernière Mise à Jour:</strong> <?php echo date('d/m/Y H:i'); ?></li>
                    <li><strong>Base de Données:</strong> 
                        <?php 
                        try {
                            $db = getDatabase();
                            echo $db->testConnection() ? '<span class="text-success">Connectée</span>' : '<span class="text-danger">Erreur</span>';
                        } catch (Exception $e) {
                            echo '<span class="text-danger">Erreur</span>';
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des statuts
    const statutCtx = document.getElementById('statutChart').getContext('2d');
    const statutData = <?php echo json_encode($stats['par_statut']); ?>;
    
    new Chart(statutCtx, {
        type: 'doughnut',
        data: {
            labels: statutData.map(item => {
                switch(item.statut) {
                    case 'nouveau': return 'Nouveau';
                    case 'en_cours': return 'En Cours';
                    case 'termine': return 'Terminé';
                    case 'rejete': return 'Rejeté';
                    default: return item.statut;
                }
            }),
            datasets: [{
                data: statutData.map(item => item.count),
                backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Graphique des priorités
    const prioriteCtx = document.getElementById('prioriteChart').getContext('2d');
    const prioriteData = <?php echo json_encode($stats['par_priorite']); ?>;
    
    new Chart(prioriteCtx, {
        type: 'bar',
        data: {
            labels: prioriteData.map(item => {
                switch(item.priorite) {
                    case 'critique': return 'Critique';
                    case 'haute': return 'Haute';
                    case 'moyenne': return 'Moyenne';
                    case 'faible': return 'Faible';
                    default: return item.priorite;
                }
            }),
            datasets: [{
                label: 'Nombre de Besoins',
                data: prioriteData.map(item => item.count),
                backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#6c757d'],
                borderColor: ['#dc3545', '#ffc107', '#0dcaf0', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>