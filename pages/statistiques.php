<?php
$page_title = "Statistiques";
include '../includes/header.php';

// Connexion à la base de données
try {
    $db = getDatabase();
    $pdo = $db->getConnection();
    
    // ============================================
    // STATISTIQUES GÉNÉRALES
    // ============================================
    
    // Total des demandes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes");
    $totalDemandes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total des utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total des validations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM validation");
    $totalValidations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total des pièces jointes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pieces_jointes");
    $totalPiecesJointes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // ============================================
    // DEMANDES PAR STATUT
    // ============================================
    $stmt = $pdo->query("
        SELECT statut, COUNT(*) as count 
        FROM demandes 
        WHERE statut != ''
        GROUP BY statut 
        ORDER BY count DESC
    ");
    $demandesParStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // DEMANDES PAR URGENCE
    // ============================================
    $stmt = $pdo->query("
        SELECT urgence, COUNT(*) as count 
        FROM demandes 
        GROUP BY urgence 
        ORDER BY FIELD(urgence, 'Urgente', 'Moyenne', 'Faible')
    ");
    $demandesParUrgence = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // DEMANDES PAR TYPE DE BESOIN
    // ============================================
    $stmt = $pdo->query("
        SELECT tb.libelle, COUNT(d.id) as count 
        FROM demandes d
        JOIN types_besoins tb ON d.type_besoin_id = tb.id
        GROUP BY tb.id, tb.libelle
        ORDER BY count DESC
    ");
    $demandesParType = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // VALIDATIONS PAR STATUT
    // ============================================
    $stmt = $pdo->query("
        SELECT statut_validation, COUNT(*) as count 
        FROM validation 
        GROUP BY statut_validation
    ");
    $validationsParStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // UTILISATEURS PAR RÔLE
    // ============================================
    $stmt = $pdo->query("
        SELECT role, COUNT(*) as count 
        FROM users 
        GROUP BY role
        ORDER BY count DESC
    ");
    $usersParRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // TOP 5 DEMANDEURS
    // ============================================
    $stmt = $pdo->query("
        SELECT u.nom, u.email, COUNT(d.id) as total_demandes
        FROM users u
        JOIN demandes d ON u.id = d.user_id
        GROUP BY u.id, u.nom, u.email
        ORDER BY total_demandes DESC
        LIMIT 5
    ");
    $topDemandeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // TOP 5 VALIDATEURS
    // ============================================
    $stmt = $pdo->query("
        SELECT u.nom, u.email, COUNT(v.id) as total_validations,
               SUM(CASE WHEN v.statut_validation = 'Validée' THEN 1 ELSE 0 END) as validees,
               SUM(CASE WHEN v.statut_validation = 'Rejetée' THEN 1 ELSE 0 END) as rejetees
        FROM users u
        JOIN validation v ON u.id = v.validateur_id
        GROUP BY u.id, u.nom, u.email
        ORDER BY total_validations DESC
        LIMIT 5
    ");
    $topValidateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // DEMANDES PAR MOIS (12 derniers mois)
    // ============================================
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(date_creation, '%Y-%m') as mois, 
               COUNT(*) as count
        FROM demandes
        WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
        ORDER BY mois ASC
    ");
    $demandesParMois = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // VALIDATIONS PAR MOIS (12 derniers mois)
    // ============================================
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(date_validation, '%Y-%m') as mois, 
               COUNT(*) as count,
               SUM(CASE WHEN statut_validation = 'Validée' THEN 1 ELSE 0 END) as validees,
               SUM(CASE WHEN statut_validation = 'Rejetée' THEN 1 ELSE 0 END) as rejetees
        FROM validation
        WHERE date_validation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_validation, '%Y-%m')
        ORDER BY mois ASC
    ");
    $validationsParMois = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ============================================
    // TEMPS MOYEN DE VALIDATION (en jours)
    // ============================================
    $stmt = $pdo->query("
        SELECT AVG(DATEDIFF(v.date_validation, d.date_creation)) as avg_days
        FROM validation v
        JOIN demandes d ON v.demande_id = d.id
    ");
    $avgValidationTime = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_days'] ?? 0, 1);
    
    // ============================================
    // TAUX DE VALIDATION
    // ============================================
    $tauxValidation = $totalValidations > 0 
        ? round((array_sum(array_map(function($v) { 
            return $v['statut_validation'] === 'Validée' ? $v['count'] : 0; 
          }, $validationsParStatut)) / $totalValidations) * 100, 1) 
        : 0;
    
    // ============================================
    // DEMANDES CETTE SEMAINE vs SEMAINE DERNIÈRE
    // ============================================
    $stmt = $pdo->query("
        SELECT COUNT(*) as count FROM demandes 
        WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
    ");
    $demandesCetteSemaine = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("
        SELECT COUNT(*) as count FROM demandes 
        WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 2 WEEK)
        AND date_creation < DATE_SUB(NOW(), INTERVAL 1 WEEK)
    ");
    $demandesSemaineDerniere = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $evolutionSemaine = $demandesSemaineDerniere > 0 
        ? round((($demandesCetteSemaine - $demandesSemaineDerniere) / $demandesSemaineDerniere) * 100, 1)
        : ($demandesCetteSemaine > 0 ? 100 : 0);

} catch (Exception $e) {
    setFlashMessage('error', 'Erreur de connexion à la base de données : ' . $e->getMessage());
    $totalDemandes = $totalUsers = $totalValidations = $totalPiecesJointes = 0;
    $demandesParStatut = $demandesParUrgence = $demandesParType = [];
    $validationsParStatut = $usersParRole = $topDemandeurs = $topValidateurs = [];
    $demandesParMois = $validationsParMois = [];
    $avgValidationTime = $tauxValidation = $demandesCetteSemaine = $evolutionSemaine = 0;
}

// Fonction pour formater les mois en français
function formatMoisFr($mois) {
    $moisFr = [
        '01' => 'Jan', '02' => 'Fév', '03' => 'Mar', '04' => 'Avr',
        '05' => 'Mai', '06' => 'Juin', '07' => 'Juil', '08' => 'Août',
        '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc'
    ];
    $parts = explode('-', $mois);
    return $moisFr[$parts[1]] . ' ' . substr($parts[0], 2);
}
?>

<!-- Contenu principal -->
<div class="pt-3 pb-2 mb-3">
    <?php
    $flash = getFlashMessage();
    if ($flash):
    ?>
    <div class="alert alert-<?php echo ($flash['type'] == 'error') ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-graph-up me-2 text-primary"></i>
            Statistiques Avancées
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>
                    Imprimer
                </button>
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="bi bi-speedometer2 me-1"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Cartes KPI principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Demandes</p>
                            <h3 class="mb-0 fw-bold"><?php echo number_format($totalDemandes); ?></h3>
                            <small class="<?php echo $evolutionSemaine >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <i class="bi bi-<?php echo $evolutionSemaine >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                <?php echo abs($evolutionSemaine); ?>% cette semaine
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clipboard-data text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Utilisateurs</p>
                            <h3 class="mb-0 fw-bold"><?php echo number_format($totalUsers); ?></h3>
                            <small class="text-muted">
                                <?php 
                                $demandeurs = array_filter($usersParRole, fn($u) => $u['role'] === 'Demandeur');
                                echo !empty($demandeurs) ? reset($demandeurs)['count'] : 0;
                                ?> demandeurs actifs
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Taux Validation</p>
                            <h3 class="mb-0 fw-bold"><?php echo $tauxValidation; ?>%</h3>
                            <small class="text-muted">
                                <?php echo $totalValidations; ?> validations traitées
                            </small>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Délai Moyen</p>
                            <h3 class="mb-0 fw-bold"><?php echo $avgValidationTime; ?> <small class="fs-6">jours</small></h3>
                            <small class="text-muted">
                                Temps de validation
                            </small>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <!-- Évolution des demandes -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                        Évolution des Demandes (12 derniers mois)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Répartition par statut -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-2 text-success"></i>
                        Répartition par Statut
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="statutChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de graphiques -->
    <div class="row mb-4">
        <!-- Par type de besoin -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-2 text-info"></i>
                        Demandes par Type de Besoin
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Par urgence -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                        Répartition par Urgence
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="urgenceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Troisième ligne: Validations et Utilisateurs -->
    <div class="row mb-4">
        <!-- Validations par mois -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-check2-square me-2 text-success"></i>
                        Historique des Validations
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="validationsChart" height="280"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Utilisateurs par rôle -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-person-badge me-2 text-primary"></i>
                        Utilisateurs par Rôle
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="rolesChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux: Top Demandeurs et Validateurs -->
    <div class="row mb-4">
        <!-- Top Demandeurs -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-trophy me-2 text-warning"></i>
                        Top 5 Demandeurs
                    </h5>
                    <span class="badge bg-primary"><?php echo count($topDemandeurs); ?> utilisateurs</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($topDemandeurs)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Nom</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0 text-center">Demandes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topDemandeurs as $index => $demandeur): ?>
                                <tr>
                                    <td>
                                        <?php if ($index === 0): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i></span>
                                        <?php elseif ($index === 1): ?>
                                            <span class="badge bg-secondary"><i class="bi bi-trophy"></i></span>
                                        <?php elseif ($index === 2): ?>
                                            <span class="badge bg-danger"><i class="bi bi-trophy"></i></span>
                                        <?php else: ?>
                                            <span class="text-muted"><?php echo $index + 1; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($demandeur['nom']); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($demandeur['email']); ?></small></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill"><?php echo $demandeur['total_demandes']; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="text-muted mt-2">Aucune donnée disponible</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Top Validateurs -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-patch-check me-2 text-success"></i>
                        Top 5 Validateurs
                    </h5>
                    <span class="badge bg-success"><?php echo count($topValidateurs); ?> validateurs</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($topValidateurs)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Nom</th>
                                    <th class="border-0 text-center">Total</th>
                                    <th class="border-0 text-center">Validées</th>
                                    <th class="border-0 text-center">Rejetées</th>
                                    <th class="border-0 text-center">Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topValidateurs as $validateur): ?>
                                <?php $taux = $validateur['total_validations'] > 0 ? round(($validateur['validees'] / $validateur['total_validations']) * 100) : 0; ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($validateur['nom']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($validateur['email']); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill"><?php echo $validateur['total_validations']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill"><?php echo $validateur['validees']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill"><?php echo $validateur['rejetees']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px; min-width: 60px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?php echo $taux; ?>%" 
                                                 aria-valuenow="<?php echo $taux; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo $taux; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="text-muted mt-2">Aucune donnée disponible</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques détaillées en cartes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-card-checklist me-2 text-info"></i>
                        Résumé Détaillé
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Statuts -->
                        <div class="col-md-4">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Par Statut</h6>
                            <?php foreach ($demandesParStatut as $statut): ?>
                            <?php 
                            $percent = $totalDemandes > 0 ? round(($statut['count'] / $totalDemandes) * 100) : 0;
                            $colorClass = match($statut['statut']) {
                                'En attente' => 'warning',
                                'En cours de validation' => 'info',
                                'Traitée' => 'success',
                                default => 'secondary'
                            };
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small"><?php echo htmlspecialchars($statut['statut']); ?></span>
                                    <span class="small fw-semibold"><?php echo $statut['count']; ?> (<?php echo $percent; ?>%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo $colorClass; ?>" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Urgences -->
                        <div class="col-md-4">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Par Urgence</h6>
                            <?php foreach ($demandesParUrgence as $urgence): ?>
                            <?php 
                            $percent = $totalDemandes > 0 ? round(($urgence['count'] / $totalDemandes) * 100) : 0;
                            $colorClass = match($urgence['urgence']) {
                                'Urgente' => 'danger',
                                'Moyenne' => 'warning',
                                'Faible' => 'success',
                                default => 'secondary'
                            };
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small"><?php echo htmlspecialchars($urgence['urgence']); ?></span>
                                    <span class="small fw-semibold"><?php echo $urgence['count']; ?> (<?php echo $percent; ?>%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo $colorClass; ?>" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Types -->
                        <div class="col-md-4">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Par Type</h6>
                            <?php 
                            $typeColors = ['primary', 'success', 'info', 'warning'];
                            foreach ($demandesParType as $index => $type): 
                            $percent = $totalDemandes > 0 ? round(($type['count'] / $totalDemandes) * 100) : 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small"><?php echo htmlspecialchars($type['libelle']); ?></span>
                                    <span class="small fw-semibold"><?php echo $type['count']; ?> (<?php echo $percent; ?>%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo $typeColors[$index % count($typeColors)]; ?>" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données PHP converties en JS
    const demandesParMois = <?php echo json_encode($demandesParMois); ?>;
    const validationsParMois = <?php echo json_encode($validationsParMois); ?>;
    const demandesParStatut = <?php echo json_encode($demandesParStatut); ?>;
    const demandesParType = <?php echo json_encode($demandesParType); ?>;
    const demandesParUrgence = <?php echo json_encode($demandesParUrgence); ?>;
    const usersParRole = <?php echo json_encode($usersParRole); ?>;

    // Couleurs
    const colors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0',
        secondary: '#6c757d'
    };

    // 1. Graphique d'évolution des demandes
    if (demandesParMois.length > 0) {
        new Chart(document.getElementById('evolutionChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: demandesParMois.map(item => {
                    const [year, month] = item.mois.split('-');
                    const moisFr = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                    return moisFr[parseInt(month) - 1] + ' ' + year.slice(2);
                }),
                datasets: [{
                    label: 'Demandes',
                    data: demandesParMois.map(item => item.count),
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Graphique par statut (Doughnut)
    if (demandesParStatut.length > 0) {
        const statutColors = {
            'En attente': colors.warning,
            'En cours de validation': colors.info,
            'Traitée': colors.success
        };
        new Chart(document.getElementById('statutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: demandesParStatut.map(item => item.statut),
                datasets: [{
                    data: demandesParStatut.map(item => item.count),
                    backgroundColor: demandesParStatut.map(item => statutColors[item.statut] || colors.secondary),
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15 }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // 3. Graphique par type de besoin
    if (demandesParType.length > 0) {
        new Chart(document.getElementById('typeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: demandesParType.map(item => item.libelle),
                datasets: [{
                    label: 'Demandes',
                    data: demandesParType.map(item => item.count),
                    backgroundColor: [colors.primary, colors.success, colors.info, colors.warning],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 5 }
                    }
                }
            }
        });
    }

    // 4. Graphique par urgence
    if (demandesParUrgence.length > 0) {
        const urgenceColors = {
            'Urgente': colors.danger,
            'Moyenne': colors.warning,
            'Faible': colors.success
        };
        new Chart(document.getElementById('urgenceChart').getContext('2d'), {
            type: 'polarArea',
            data: {
                labels: demandesParUrgence.map(item => item.urgence),
                datasets: [{
                    data: demandesParUrgence.map(item => item.count),
                    backgroundColor: demandesParUrgence.map(item => urgenceColors[item.urgence] + '80'),
                    borderColor: demandesParUrgence.map(item => urgenceColors[item.urgence]),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15 }
                    }
                }
            }
        });
    }

    // 5. Graphique des validations par mois
    if (validationsParMois.length > 0) {
        new Chart(document.getElementById('validationsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: validationsParMois.map(item => {
                    const [year, month] = item.mois.split('-');
                    const moisFr = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                    return moisFr[parseInt(month) - 1] + ' ' + year.slice(2);
                }),
                datasets: [{
                    label: 'Validées',
                    data: validationsParMois.map(item => item.validees),
                    backgroundColor: colors.success,
                    borderRadius: 4
                }, {
                    label: 'Rejetées',
                    data: validationsParMois.map(item => item.rejetees),
                    backgroundColor: colors.danger,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }

    // 6. Graphique utilisateurs par rôle
    if (usersParRole.length > 0) {
        const roleColors = {
            'Demandeur': colors.primary,
            'Validateur': colors.success,
            'Administrateur': colors.danger
        };
        new Chart(document.getElementById('rolesChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: usersParRole.map(item => item.role),
                datasets: [{
                    data: usersParRole.map(item => item.count),
                    backgroundColor: usersParRole.map(item => roleColors[item.role] || colors.secondary),
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15 }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
