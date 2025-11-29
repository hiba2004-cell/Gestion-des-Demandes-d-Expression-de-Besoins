<?php
$page_title = "Statistiques des Demandes";
include '../includes/header.php';

// Récupération des statistiques
try {
    $stats = getStatistics();
    $recentDemandes = getBesoins([], 5, 0);
    
    $conn = getConnection();
    
    // Évolution mensuelle des demandes
    $stmt = $conn->query("
        SELECT 
            DATE_FORMAT(date_creation, '%Y-%m') as mois, 
            COUNT(*) as count,
            COUNT(CASE WHEN v.statut_validation = '' THEN 1 END) as approuves,
            COUNT(CASE WHEN v.statut_validation = 'rejete' THEN 1 END) as rejetes
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
        ORDER BY mois ASC
    ");
    $evolutionMensuelle = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top catégories de demandes
    $stmt = $conn->query("
        SELECT 
            categorie, 
            COUNT(*) as count,
            COUNT(CASE WHEN v.statut_validation = 'approuve' THEN 1 END) as approuves
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        GROUP BY categorie 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $topCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques de validation
    $stmt = $conn->query("
        SELECT 
            COUNT(CASE WHEN v.statut_validation = 'approuve' THEN 1 END) as approuves,
            COUNT(CASE WHEN v.statut_validation = 'rejete' THEN 1 END) as rejetes,
            COUNT(CASE WHEN v.statut_validation = 'en_attente' THEN 1 END) as en_attente,
            COUNT(CASE WHEN v.demande_id IS NULL AND d.statut = 'nouveau' THEN 1 END) as sans_validation,
            AVG(CASE 
                WHEN v.date_validation IS NOT NULL 
                THEN DATEDIFF(v.date_validation, d.date_creation) 
            END) as delai_moyen_validation
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
    ");
    $statsValidation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Demandes par urgence et statut
    $stmt = $conn->query("
        SELECT 
            d.urgence,
            CASE 
                WHEN v.demande_id IS NULL THEN d.statut
                ELSE v.statut_validation
            END AS statut_final,
            COUNT(*) as count
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        GROUP BY d.urgence, statut_final
    ");
    $urgenceStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Demandes par département (si la table existe)
    $stmt = $conn->query("
        SELECT 
            u.departement,
            COUNT(d.id) as count,
            COUNT(CASE WHEN v.statut_validation = 'approuve' THEN 1 END) as approuves
        FROM demandes d
        INNER JOIN users u ON d.demandeur_id = u.id
        LEFT JOIN validation v ON v.demande_id = d.id
        WHERE u.departement IS NOT NULL
        GROUP BY u.departement
        ORDER BY count DESC
        LIMIT 5
    ");
    $parDepartement = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Taux de validation
    $totalDemandes = $stats['total'] > 0 ? $stats['total'] : 1;
    $tauxApprobation = round(($statsValidation['approuves'] / $totalDemandes) * 100);
    $tauxRejet = round(($statsValidation['rejetes'] / $totalDemandes) * 100);
    
} catch (Exception $e) {
    error_log('Erreur statistiques demandes : ' . $e->getMessage());
    setFlashMessage('error', 'Impossible de charger les statistiques actuellement.');
    $stats = ['total' => 0, 'par_statut' => [], 'par_priorite' => []];
    $evolutionMensuelle = [];
    $topCategories = [];
    $statsValidation = ['approuves' => 0, 'rejetes' => 0, 'en_attente' => 0, 'sans_validation' => 0, 'delai_moyen_validation' => 0];
    $urgenceStatut = [];
    $parDepartement = [];
    $tauxApprobation = 0;
    $tauxRejet = 0;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
        Tableau de Bord - Statistiques des Demandes
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="filterByPeriod('week')">
                <i class="bi bi-calendar-week me-1"></i>7 jours
            </button>
            <button class="btn btn-sm btn-outline-secondary active" onclick="filterByPeriod('month')">
                <i class="bi bi-calendar-month me-1"></i>30 jours
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="filterByPeriod('year')">
                <i class="bi bi-calendar-range me-1"></i>12 mois
            </button>
        </div>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Imprimer
            </button>
            <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i>Exporter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportData('csv')">
                    <i class="bi bi-filetype-csv me-2"></i>CSV
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportData('excel')">
                    <i class="bi bi-file-earmark-excel me-2"></i>Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportData('pdf')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- KPI principaux -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card bg-gradient-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-inbox display-4 mb-2 opacity-75"></i>
                <h3 class="card-title fw-bold total-demandes"><?php echo $stats['total']; ?></h3>
                <p class="card-text mb-0">Total des Demandes</p>
                <hr class="my-2 border-white opacity-25">
                <small class="d-block">
                    <?php 
                    $nouveaux = array_filter($stats['par_statut'], function($item) {
                        return $item['statut_final'] === 'nouveau';
                    });
                    $countNouveaux = !empty($nouveaux) ? reset($nouveaux)['count'] : 0;
                    echo $countNouveaux; 
                    ?> nouvelle(s) ce mois
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card bg-gradient-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4 mb-2 opacity-75"></i>
                <h3 class="card-title fw-bold"><?php echo $statsValidation['approuves']; ?></h3>
                <p class="card-text mb-0">Demandes Approuvées</p>
                <hr class="my-2 border-white opacity-25">
                <small class="d-block">
                    <i class="bi bi-arrow-up-circle me-1"></i>
                    <?php echo $tauxApprobation; ?>% du total
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card bg-gradient-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-split display-4 mb-2 opacity-75"></i>
                <h3 class="card-title fw-bold"><?php echo $statsValidation['en_attente']; ?></h3>
                <p class="card-text mb-0">En Attente</p>
                <hr class="my-2 border-white opacity-25">
                <small class="d-block">
                    <i class="bi bi-clock me-1"></i>
                    Délai moy: <?php echo round($statsValidation['delai_moyen_validation'] ?? 0); ?> jours
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card bg-gradient-danger text-white">
            <div class="card-body text-center">
                <i class="bi bi-x-circle display-4 mb-2 opacity-75"></i>
                <h3 class="card-title fw-bold"><?php echo $statsValidation['rejetes']; ?></h3>
                <p class="card-text mb-0">Demandes Rejetées</p>
                <hr class="my-2 border-white opacity-25">
                <small class="d-block">
                    <i class="bi bi-arrow-down-circle me-1"></i>
                    <?php echo $tauxRejet; ?>% du total
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques principaux -->
<div class="row mb-4">
    <!-- Évolution mensuelle -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary bg-gradient text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Évolution des Demandes (12 derniers mois)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="evolutionChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Répartition par urgence -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning bg-gradient text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Répartition par Urgence
                </h5>
            </div>
            <div class="card-body">
                <canvas id="urgenceChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Top catégories -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info bg-gradient text-white">
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
        <div class="card shadow-sm">
            <div class="card-header bg-success bg-gradient text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Répartition par Statut Final
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statutChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tableaux détaillés -->
<div class="row mb-4">
    <!-- Détails par catégorie -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>
                    Analyse par Catégorie
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($topCategories)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Catégorie</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Approuvées</th>
                                <th class="text-center">Taux</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topCategories as $cat): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-tag me-2 text-muted"></i>
                                    <?php echo htmlspecialchars($cat['categorie']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo $cat['count']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo $cat['approuves']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $taux = $cat['count'] > 0 ? round(($cat['approuves'] / $cat['count']) * 100) : 0;
                                    $colorClass = $taux >= 70 ? 'success' : ($taux >= 40 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $colorClass; ?>"><?php echo $taux; ?>%</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Aucune donnée disponible</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Demandes par département -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-building me-2"></i>
                    Top Départements
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($parDepartement)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Département</th>
                                <th class="text-center">Demandes</th>
                                <th class="text-center">Approuvées</th>
                                <th class="text-end">Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parDepartement as $dept): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-briefcase me-2 text-muted"></i>
                                    <?php echo htmlspecialchars($dept['departement']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?php echo $dept['count']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo $dept['approuves']; ?></span>
                                </td>
                                <td class="text-end">
                                    <?php 
                                    $pct = $stats['total'] > 0 ? round(($dept['count'] / $stats['total']) * 100) : 0;
                                    ?>
                                    <span class="text-muted small"><?php echo $pct; ?>%</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-building display-4 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Aucune donnée disponible</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Insights et Tendances -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header" 
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Insights et Recommandations
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-trending-up me-2"></i>
                            Tendances
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if (!empty($evolutionMensuelle)): ?>
                            <?php
                            $dernierMois = end($evolutionMensuelle);
                            $avantDernierMois = count($evolutionMensuelle) > 1 ? 
                                $evolutionMensuelle[count($evolutionMensuelle)-2] : null;
                            ?>
                            <li class="mb-2">
                                <i class="bi bi-calendar-check me-1 text-success"></i>
                                <strong><?php echo $dernierMois['count']; ?></strong> nouvelles demandes ce mois
                                <?php if ($avantDernierMois): ?>
                                <?php 
                                $evolution = $dernierMois['count'] - $avantDernierMois['count'];
                                $classe = $evolution > 0 ? 'success' : ($evolution < 0 ? 'danger' : 'secondary');
                                $icone = $evolution > 0 ? 'arrow-up' : ($evolution < 0 ? 'arrow-down' : 'dash');
                                ?>
                                <span class="badge bg-<?php echo $classe; ?> ms-1">
                                    <i class="bi bi-<?php echo $icone; ?>"></i>
                                    <?php echo abs($evolution); ?>
                                </span>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>

                            <?php if (!empty($topCategories)): ?>
                            <li class="mb-2">
                                <i class="bi bi-award me-1 text-warning"></i>
                                Catégorie la plus demandée:
                                <strong><?php echo htmlspecialchars($topCategories[0]['categorie']); ?></strong>
                            </li>
                            <?php endif; ?>

                            <li class="mb-2">
                                <i class="bi bi-percent me-1 text-info"></i>
                                Taux d'approbation global: 
                                <strong class="text-success"><?php echo $tauxApprobation; ?>%</strong>
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-clock-history me-1 text-primary"></i>
                                Délai moyen de traitement: 
                                <strong><?php echo round($statsValidation['delai_moyen_validation'] ?? 0); ?> jours</strong>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-4">
                        <h6 class="fw-bold text-warning mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Points d'Attention
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if ($statsValidation['en_attente'] > 5): ?>
                            <li class="mb-2 text-warning">
                                <i class="bi bi-hourglass me-1"></i>
                                <strong><?php echo $statsValidation['en_attente']; ?></strong> demandes en attente de validation
                            </li>
                            <?php endif; ?>

                            <?php if ($statsValidation['sans_validation'] > 0): ?>
                            <li class="mb-2 text-info">
                                <i class="bi bi-file-earmark me-1"></i>
                                <strong><?php echo $statsValidation['sans_validation']; ?></strong> nouvelles demandes non traitées
                            </li>
                            <?php endif; ?>

                            <?php
                            $urgentes = array_filter($stats['par_priorite'], function($item) {
                                return $item['urgence'] === 'urgente';
                            });
                            $countUrgentes = !empty($urgentes) ? reset($urgentes)['count'] : 0;
                            ?>
                            <?php if ($countUrgentes > 0): ?>
                            <li class="mb-2 text-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong><?php echo $countUrgentes; ?></strong> demande(s) urgente(s)
                            </li>
                            <?php endif; ?>

                            <?php if ($tauxRejet > 30): ?>
                            <li class="mb-2 text-danger">
                                <i class="bi bi-x-octagon me-1"></i>
                                Taux de rejet élevé: <strong><?php echo $tauxRejet; ?>%</strong>
                            </li>
                            <?php endif; ?>

                            <?php if (empty($urgentes) && $statsValidation['en_attente'] == 0): ?>
                            <li class="mb-2 text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Aucun point d'attention majeur
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="col-md-4">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-lightbulb me-2"></i>
                            Recommandations
                        </h6>
                        <ul class="list-unstyled small">
                            <?php if ($statsValidation['en_attente'] > 5): ?>
                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Accélérer le processus de validation
                            </li>
                            <?php endif; ?>

                            <?php if ($countUrgentes > 2): ?>
                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Prioriser les demandes urgentes
                            </li>
                            <?php endif; ?>

                            <?php if ($tauxRejet > 30): ?>
                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Revoir les critères de validation
                            </li>
                            <?php endif; ?>

                            <?php if ($statsValidation['delai_moyen_validation'] > 7): ?>
                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Optimiser les délais de traitement
                            </li>
                            <?php endif; ?>

                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Mettre en place un suivi régulier
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
                                Former les équipes aux bonnes pratiques
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance par urgence -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Performance par Niveau d'Urgence
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Urgence</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Nouveau</th>
                                <th class="text-center">Approuvé</th>
                                <th class="text-center">Rejeté</th>
                                <th class="text-center">En Attente</th>
                                <th>Progression</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $urgences = ['urgente' => 'danger', 'haute' => 'warning', 'normale' => 'info', 'basse' => 'secondary'];
                            foreach ($urgences as $urgence => $color):
                                $lignes = array_filter($urgenceStatut, function($item) use ($urgence) {
                                    return $item['urgence'] === $urgence;
                                });
                                
                                $total = array_sum(array_column($lignes, 'count'));
                                if ($total == 0) continue;
                                
                                $nouveau = 0;
                                $approuve = 0;
                                $rejete = 0;
                                $enAttente = 0;
                                
                                foreach ($lignes as $ligne) {
                                    switch ($ligne['statut_final']) {
                                        case 'nouveau': $nouveau = $ligne['count']; break;
                                        case 'approuve': $approuve = $ligne['count']; break;
                                        case 'rejete': $rejete = $ligne['count']; break;
                                        case 'en_attente': $enAttente = $ligne['count']; break;
                                    }
                                }
                                
                                $pctApprouve = round(($approuve / $total) * 100);
                            ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo ucfirst($urgence); ?>
                                    </span>
                                </td>
                                <td class="text-center"><strong><?php echo $total; ?></strong></td>
                                <td class="text-center"><span class="badge bg-primary"><?php echo $nouveau; ?></span></td>
                                <td class="text-center"><span class="badge bg-success"><?php echo $approuve; ?></span></td>
                                <td class="text-center"><span class="badge bg-danger"><?php echo $rejete; ?></span></td>
                                <td class="text-center"><span class="badge bg-warning"><?php echo $enAttente; ?></span></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo $pctApprouve; ?>%">
                                            <?php echo $pctApprouve; ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// include '../includes/footer.php';