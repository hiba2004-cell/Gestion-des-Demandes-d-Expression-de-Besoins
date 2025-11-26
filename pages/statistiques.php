<?php
$page_title = "Statistiques";
include '../includes/header.php';

// Récupération des statistiques
try {
    $stats = getStatistics();
    $recentBesoins = getBesoins([], 5, 0);
    
    // Statistiques avancées
    $conn = getConnection();
    
    // Évolution mensuelle
    $stmt = $conn->query("
        SELECT DATE_FORMAT(date_creation, '%Y-%m') as mois, 
               COUNT(*) as count,
               AVG(cout_estime) as cout_moyen
        FROM besoins 
        WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
        ORDER BY mois ASC
    ");
    $evolutionMensuelle = $stmt->fetchAll();
    
    // Top catégories
    $stmt = $conn->query("
        SELECT categorie, 
               COUNT(*) as count,
               AVG(cout_estime) as cout_moyen
        FROM besoins 
        GROUP BY categorie 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $topCategories = $stmt->fetchAll();
    
    // Délais de réalisation
    $stmt = $conn->query("
        SELECT 
            AVG(DATEDIFF(COALESCE(delai_souhaite, NOW()), date_creation)) as delai_moyen,
            COUNT(CASE WHEN delai_souhaite < NOW() AND statut != 'termine' THEN 1 END) as en_retard,
            COUNT(CASE WHEN statut = 'termine' THEN 1 END) as termines
        FROM besoins
    ");
    $delais = $stmt->fetch();
    
} catch (Exception $e) {
    setFlashMessage('error', 'Erreur lors du chargement des statistiques : ' . $e->getMessage());
    $stats = ['total' => 0, 'par_statut' => [], 'par_priorite' => [], 'cout_total' => 0];
    $evolutionMensuelle = [];
    $topCategories = [];
    $delais = ['delai_moyen' => 0, 'en_retard' => 0, 'termines' => 0];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-bar-chart me-2 text-success"></i>
        Tableau de Bord Statistiques
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>
                Imprimer
            </button>
            <button class="btn btn-primary" onclick="exportData()">
                <i class="bi bi-download me-1"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<!-- KPI principaux -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-data display-4 mb-2"></i>
                <h3 class="card-title"><?php echo $stats['total']; ?></h3>
                <p class="card-text">Total des Besoins</p>
                <div class="mt-2">
                    <small>
                        <?php 
                        $nouveaux = array_filter($stats['par_statut'], function($item) {
                            return $item['statut'] === 'nouveau';
                        });
                        $countNouveaux = !empty($nouveaux) ? reset($nouveaux)['count'] : 0;
                        echo $countNouveaux; 
                        ?> nouveau(x) ce mois
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4 mb-2"></i>
                <h3 class="card-title"><?php echo $delais['termines']; ?></h3>
                <p class="card-text">Besoins Terminés</p>
                <div class="mt-2">
                    <small>
                        <?php 
                        $total = $stats['total'] > 0 ? $stats['total'] : 1;
                        $pourcentage = round(($delais['termines'] / $total) * 100);
                        echo $pourcentage; 
                        ?>% de réussite
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle display-4 mb-2"></i>
                <h3 class="card-title"><?php echo $delais['en_retard']; ?></h3>
                <p class="card-text">En Retard</p>
                <div class="mt-2">
                    <small>
                        Délai moyen: <?php echo round($delais['delai_moyen'] ?? 0); ?> jours
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="bi bi-currency-euro display-4 mb-2"></i>
                <h3 class="card-title"><?php echo formatCurrency($stats['cout_total']); ?></h3>
                <p class="card-text">Budget Total</p>
                <div class="mt-2">
                    <small>
                        Moyenne: <?php 
                        $moyenne = $stats['total'] > 0 ? $stats['cout_total'] / $stats['total'] : 0;
                        echo formatCurrency($moyenne); 
                        ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques principaux -->
<div class="row mb-4">
    <!-- Évolution mensuelle -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Évolution des Besoins (12 derniers mois)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="evolutionChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Répartition par priorité -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Répartition par Priorité
                </h5>
            </div>
            <div class="card-body">
                <canvas id="prioriteChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Top catégories -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tags me-2"></i>
                    Top 5 des Catégories
                </h5>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="150"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Répartition par statut -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Répartition par Statut
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statutChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tableaux détaillés -->
<div class="row">
    <!-- Détails par catégorie -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>
                    Analyse par Catégorie
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($topCategories)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Nombre</th>
                                    <th>Coût Moyen</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCategories as $cat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cat['categorie']); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $cat['count']; ?></span>
                                        </td>
                                        <td>
                                            <?php echo $cat['cout_moyen'] ? formatCurrency($cat['cout_moyen']) : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $pourcentage = $stats['total'] > 0 ? round(($cat['count'] / $stats['total']) * 100) : 0;
                                            echo $pourcentage . '%'; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">Aucune donnée disponible</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Analyse des délais -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Analyse des Délais
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo round($delais['delai_moyen'] ?? 0); ?></h4>
                            <small class="text-muted">Délai Moyen (jours)</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success"><?php echo $delais['termines']; ?></h4>
                            <small class="text-muted">Terminés</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger"><?php echo $delais['en_retard']; ?></h4>
                        <small class="text-muted">En Retard</small>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <!-- Barre de progression des délais -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Respect des délais</small>
                        <small>
                            <?php 
                            $totalAvecDelai = $delais['termines'] + $delais['en_retard'];
                            $pourcentageRespect = $totalAvecDelai > 0 ? round(($delais['termines'] / $totalAvecDelai) * 100) : 0;
                            echo $pourcentageRespect; 
                            ?>%
                        </small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $pourcentageRespect; ?>%"></div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="fw-bold">Recommandations :</h6>
                    <ul class="small mb-0">
                        <?php if ($delais['en_retard'] > 0): ?>
                            <li class="text-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <?php echo $delais['en_retard']; ?> besoins en retard à traiter en priorité
                            </li>
                        <?php endif; ?>
                        <?php if ($pourcentageRespect < 70): ?>
                            <li class="text-warning">
                                <i class="bi bi-clock me-1"></i>
                                Améliorer la planification des délais
                            </li>
                        <?php endif; ?>
                        <?php if ($pourcentageRespect >= 80): ?>
                            <li class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Excellent respect des délais !
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tendances et insights -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Insights et Tendances
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="fw-bold text-primary">
                            <i class="bi bi-trending-up me-2"></i>
                            Tendances
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if (!empty($evolutionMensuelle)): ?>
                                <?php
                                $dernierMois = end($evolutionMensuelle);
                                $avantDernierMois = count($evolutionMensuelle) > 1 ? $evolutionMensuelle[count($evolutionMensuelle)-2] : null;
                                ?>
                                <li class="mb-1">
                                    <i class="bi bi-calendar-month me-1"></i>
                                    <?php echo $dernierMois['count']; ?> nouveaux besoins ce mois
                                    <?php if ($avantDernierMois): ?>
                                        <?php 
                                        $evolution = $dernierMois['count'] - $avantDernierMois['count'];
                                        $classe = $evolution > 0 ? 'success' : ($evolution < 0 ? 'danger' : 'secondary');
                                        $icone = $evolution > 0 ? 'arrow-up' : ($evolution < 0 ? 'arrow-down' : 'dash');
                                        ?>
                                        <span class="text-<?php echo $classe; ?>">
                                            <i class="bi bi-<?php echo $icone; ?>"></i>
                                            <?php echo abs($evolution); ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($topCategories)): ?>
                                <li class="mb-1">
                                    <i class="bi bi-award me-1"></i>
                                    Catégorie populaire: <strong><?php echo htmlspecialchars($topCategories[0]['categorie']); ?></strong>
                                </li>
                            <?php endif; ?>
                            
                            <li class="mb-1">
                                <i class="bi bi-speedometer me-1"></i>
                                Taux de complétion: <strong><?php echo $pourcentageRespect; ?>%</strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-4">
                        <h6 class="fw-bold text-warning">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Points d'Attention
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if ($delais['en_retard'] > 0): ?>
                                <li class="mb-1 text-danger">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo $delais['en_retard']; ?> projet(s) en retard
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $critiques = array_filter($stats['par_priorite'], function($item) {
                                return $item['priorite'] === 'critique';
                            });
                            $countCritiques = !empty($critiques) ? reset($critiques)['count'] : 0;
                            ?>
                            <?php if ($countCritiques > 0): ?>
                                <li class="mb-1 text-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <?php echo $countCritiques; ?> besoin(s) critiques
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($stats['cout_total'] > 100000): ?>
                                <li class="mb-1 text-warning">
                                    <i class="bi bi-currency-euro me-1"></i>
                                    Budget important engagé
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="col-md-4">
                        <h6 class="fw-bold text-success">
                            <i class="bi bi-target me-2"></i>
                            Recommandations
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if ($delais['en_retard'] > 0): ?>
                                <li class="mb-1">
                                    <i class="bi bi-arrow-right me-1"></i>
                                    Revoir la planification des projets
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($countCritiques > 2): ?>
                                <li class="mb-1">
                                    <i class="bi bi-arrow-right me-1"></i>
                                    Prioriser les besoins critiques
                                </li>
                            <?php endif; ?>
                            
                            <li class="mb-1">
                                <i class="bi bi-arrow-right me-1"></i>
                                Mettre en place un suivi régulier
                            </li>
                            
                            <li class="mb-1">
                                <i class="bi bi-arrow-right me-1"></i>
                                Optimiser les processus de validation
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale des graphiques
    Chart.defaults.font.family = 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif';
    Chart.defaults.color = '#495057';
    
    // Graphique d'évolution mensuelle
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = <?php echo json_encode($evolutionMensuelle); ?>;
    
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => {
                const [year, month] = item.mois.split('-');
                return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Nouveaux Besoins',
                data: evolutionData.map(item => item.count),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Graphique par priorité
    const prioriteCtx = document.getElementById('prioriteChart').getContext('2d');
    const prioriteData = <?php echo json_encode($stats['par_priorite']); ?>;
    
    new Chart(prioriteCtx, {
        type: 'doughnut',
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
                data: prioriteData.map(item => item.count),
                backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#6c757d'],
                borderColor: '#fff',
                borderWidth: 3
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
                        usePointStyle: true
                    }
                }
            }
        }
    });
    
    // Graphique des catégories
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesData = <?php echo json_encode($topCategories); ?>;
    
    new Chart(categoriesCtx, {
        type: 'bar',
        data: {
            labels: categoriesData.map(item => item.categorie),
            datasets: [{
                label: 'Nombre de Besoins',
                data: categoriesData.map(item => item.count),
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: '#17a2b8',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Graphique par statut
    const statutCtx = document.getElementById('statutChart').getContext('2d');
    const statutData = <?php echo json_encode($stats['par_statut']); ?>;
    
    new Chart(statutCtx, {
        type: 'pie',
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
                borderColor: '#fff',
                borderWidth: 3
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
                        usePointStyle: true
                    }
                }
            }
        }
    });
});

// Export des statistiques
function exportData() {
    // Créer les données d'export
    const stats = <?php echo json_encode($stats); ?>;
    const evolution = <?php echo json_encode($evolutionMensuelle); ?>;
    const categories = <?php echo json_encode($topCategories); ?>;
    const delais = <?php echo json_encode($delais); ?>;
    
    // Créer le contenu CSV
    let csvContent = "Type,Donnée,Valeur\n";
    
    // Statistiques générales
    csvContent += `Total,Besoins,${stats.total}\n`;
    csvContent += `Budget,Total,${stats.cout_total}\n`;
    csvContent += `Délai,Moyen,${Math.round(delais.delai_moyen || 0)}\n`;
    csvContent += `Retard,Nombre,${delais.en_retard}\n`;
    
    // Statuts
    stats.par_statut.forEach(item => {
        csvContent += `Statut,${item.statut},${item.count}\n`;
    });
    
    // Priorités
    stats.par_priorite.forEach(item => {
        csvContent += `Priorité,${item.priorite},${item.count}\n`;
    });
    
    // Catégories
    categories.forEach(item => {
        csvContent += `Catégorie,${item.categorie},${item.count}\n`;
    });
    
    // Télécharger le fichier
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'statistiques_besoins_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>

<style>
@media print {
    .btn-toolbar, .card-header {
        display: none !important;
    }
    
    .row {
        page-break-inside: avoid;
    }
    
    .card {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>