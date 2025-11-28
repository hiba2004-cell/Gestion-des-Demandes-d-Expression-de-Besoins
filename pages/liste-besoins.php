<?php
$page_title = "Liste des Besoins";
include '../includes/header.php';

// Paramètres de pagination
$page = max(1, intval($_GET['page'] ?? 1));
$itemsPerPage = 10;

// Filtres
$filters = [
    'priorite' => sanitize($_GET['priorite'] ?? ''),
    'statut' => sanitize($_GET['statut'] ?? ''),
    'categorie' => sanitize($_GET['categorie'] ?? ''),
    'search' => sanitize($_GET['search'] ?? '')
];

// Récupération des données
try {
    $result = getBesoins($filters, $itemsPerPage, ($page - 1) * $itemsPerPage);
    $besoins = $result['besoins'];
    $totalBesoins = $result['total'];
    $pagination = paginate($totalBesoins, $itemsPerPage, $page);
} catch (Exception $e) {
    setFlashMessage('error', 'Erreur lors du chargement des besoins : ' . $e->getMessage());
    $besoins = [];
    $totalBesoins = 0;
    $pagination = paginate(0, $itemsPerPage, 1);
}

// Traitement de la suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    try {
        if (deleteBesoin($deleteId)) {
            setFlashMessage('success', 'Le besoin a été supprimé avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression du besoin.');
        }
    } catch (Exception $e) {
        setFlashMessage('error', 'Erreur de base de données : ' . $e->getMessage());
    }
    
    // Redirection pour éviter la suppression accidentelle au rafraîchissement
    $queryParams = $_GET;
    unset($queryParams['delete']);
    $redirectUrl = 'liste-besoins.php';
    if (!empty($queryParams)) {
        $redirectUrl .= '?' . http_build_query($queryParams);
    }
    redirect($redirectUrl);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
<h1 class="h2">
        <i class="bi bi-list-ul me-2 text-primary"></i>
        Liste des Besoins
        <span class="badge bg-secondary ms-2"><?php echo $totalBesoins; ?></span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="ajouter-besoin.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>
                Nouveau Besoin
            </a>
            <button class="btn btn-outline-primary" onclick="exportData()">
                <i class="bi bi-download me-1"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>
            Filtres et Recherche
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($filters['search']); ?>"
                       placeholder="Titre, description, demandeur...">
            </div>
            
            <div class="col-md-2">
                <label for="priorite" class="form-label">Priorité</label>
                <select class="form-select" id="priorite" name="priorite">
                    <option value="">Toutes</option>
                    <option value="critique" <?php echo ($filters['priorite'] === 'critique') ? 'selected' : ''; ?>>
                        Critique
                    </option>
                    <option value="haute" <?php echo ($filters['priorite'] === 'haute') ? 'selected' : ''; ?>>
                        Haute
                    </option>
                    <option value="moyenne" <?php echo ($filters['priorite'] === 'moyenne') ? 'selected' : ''; ?>>
                        Moyenne
                    </option>
                    <option value="faible" <?php echo ($filters['priorite'] === 'faible') ? 'selected' : ''; ?>>
                        Faible
                    </option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut">
                    <option value="">Tous</option>
                    <option value="nouveau" <?php echo ($filters['statut'] === 'nouveau') ? 'selected' : ''; ?>>
                        Nouveau
                    </option>
                    <option value="en_cours" <?php echo ($filters['statut'] === 'en_cours') ? 'selected' : ''; ?>>
                        En cours
                    </option>
                    <option value="termine" <?php echo ($filters['statut'] === 'termine') ? 'selected' : ''; ?>>
                        Terminé
                    </option>
                    <option value="rejete" <?php echo ($filters['statut'] === 'rejete') ? 'selected' : ''; ?>>
                        Rejeté
                    </option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="categorie" class="form-label">Catégorie</label>
                <input type="text" 
                       class="form-control" 
                       id="categorie" 
                       name="categorie" 
                       value="<?php echo htmlspecialchars($filters['categorie']); ?>"
                       placeholder="Ex: Développement">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
        
        <?php if (!empty(array_filter($filters))): ?>
            <div class="mt-3">
                <a href="liste-besoins.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i>
                    Réinitialiser les Filtres
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Résultats -->
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i class="bi bi-table me-2"></i>
            Résultats (<?php echo $totalBesoins; ?> besoins trouvés)
        </h6>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-light btn-sm" onclick="toggleView('table')" id="tableViewBtn">
                <i class="bi bi-table"></i> Tableau
            </button>
            <button class="btn btn-outline-light btn-sm" onclick="toggleView('cards')" id="cardsViewBtn">
                <i class="bi bi-grid-3x3-gap"></i> Cartes
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($besoins)): ?>
            <!-- Vue tableau -->
            <div id="tableView" class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%;">Besoin</th>
                            <th style="width: 20%;">Demandeur</th>
                            <th style="width: 10%;">Priorité</th>
                            <th style="width: 10%;">Statut</th>
                            <th style="width: 10%;">Coût</th>
                            <th style="width: 10%;">Date</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($besoins as $besoin): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong class="d-block"><?php echo htmlspecialchars($besoin['titre']); ?></strong>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($besoin['description'], 0, 80)) . '...'; ?>
                                        </small>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border">
                                                <?php echo htmlspecialchars($besoin['categorie']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block"><?php echo htmlspecialchars($besoin['demandeur_nom']); ?></strong>
                                        <small class="text-muted"><?php echo htmlspecialchars($besoin['demandeur_email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getPriorityClass($besoin['priorite']); ?> fs-6">
                                        <?php echo getPriorityLabel($besoin['priorite']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusClass($besoin['statut']); ?> fs-6">
                                        <?php echo getStatusLabel($besoin['statut']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($besoin['cout_estime']): ?>
                                        <span class="fw-bold"><?php echo formatCurrency($besoin['cout_estime']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                               <td>
                                <?php echo formatDate($besoin['date_creation']); ?>
                                    <!-- <small>
                                        <?php echo formatDate($besoin['date_creation']); ?><?php if ($besoin['delai_souhaite']): ?><span class="text-warning ms-2">
                                            | <i class="bi bi-clock me-1"></i> <?php echo formatDate($besoin['delai_souhaite']); ?>
                                        </span><?php endif; ?>
                                    </small> -->
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="detail-besoin.php?id=<?php echo $besoin['id']; ?>" 
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip" 
                                           title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="detail-besoin.php?id=<?php echo $besoin['id']; ?>&edit=1" 
                                           class="btn btn-outline-warning"
                                           data-bs-toggle="tooltip" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['delete' => $besoin['id']])); ?>" 
                                           class="btn btn-outline-danger btn-delete"
                                           data-bs-toggle="tooltip" 
                                           title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vue cartes -->
            <div id="cardsView" class="p-3" style="display: none;">
                <div class="row">
                    <?php foreach ($besoins as $besoin): ?>
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100 border-start border-<?php echo getPriorityClass($besoin['priorite']); ?> border-3">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($besoin['titre']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($besoin['categorie']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?php echo getPriorityClass($besoin['priorite']); ?> mb-1">
                                            <?php echo getPriorityLabel($besoin['priorite']); ?>
                                        </span>
                                        <br>
                                        <span class="badge bg-<?php echo getStatusClass($besoin['statut']); ?>">
                                            <?php echo getStatusLabel($besoin['statut']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text small">
                                        <?php echo htmlspecialchars(substr($besoin['description'], 0, 150)) . '...'; ?>
                                    </p>
                                    <div class="row small">
                                        <div class="col-6">
                                            <strong>Demandeur:</strong><br>
                                            <?php echo htmlspecialchars($besoin['demandeur_nom']); ?>
                                        </div>
                                        <div class="col-6 text-end">
                                            <strong>Date:</strong><br>
                                            <?php echo formatDate($besoin['date_creation']); ?>
                                            <?php if ($besoin['cout_estime']): ?>
                                                <br><strong><?php echo formatCurrency($besoin['cout_estime']); ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <a href="detail-besoin.php?id=<?php echo $besoin['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i> Détails
                                        </a>
                                        <a href="detail-besoin.php?id=<?php echo $besoin['id']; ?>&edit=1" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-pencil me-1"></i> Modifier
                                        </a>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['delete' => $besoin['id']])); ?>" 
                                           class="btn btn-outline-danger btn-sm btn-delete">
                                            <i class="bi bi-trash me-1"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Aucun besoin trouvé</h4>
                <p class="text-muted">
                    <?php if (!empty(array_filter($filters))): ?>
                        Essayez de modifier vos critères de recherche
                    <?php else: ?>
                        Commencez par ajouter votre premier besoin
                    <?php endif; ?>
                </p>
                <div class="mt-3">
                    <?php if (!empty(array_filter($filters))): ?>
                        <a href="liste-besoins.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Réinitialiser les Filtres
                        </a>
                    <?php endif; ?>
                    <a href="ajouter-besoin.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>
                        Ajouter un Besoin
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($totalBesoins > 0): ?>
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Affichage de <?php echo ($pagination['offset'] + 1); ?> à 
                        <?php echo min($pagination['offset'] + $itemsPerPage, $totalBesoins); ?> 
                        sur <?php echo $totalBesoins; ?> besoins
                    </p>
                </div>
                <div class="col-md-6">
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm justify-content-end mb-0">
                                <!-- Page précédente -->
                                <?php if ($pagination['has_previous']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Pages -->
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($pagination['total_pages'], $page + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Page suivante -->
                                <?php if ($pagination['has_next']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche en temps réel (optionnelle)
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            // Décommenter pour activer la recherche en temps réel
            // searchTimeout = setTimeout(() => {
            //     this.form.submit();
            // }, 1000);
        });
    }
});

// Basculer entre vue tableau et cartes
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardsBtn = document.getElementById('cardsViewBtn');
    
    if (view === 'table') {
        tableView.style.display = 'block';
        cardsView.style.display = 'none';
        tableBtn.className = 'btn btn-light btn-sm';
        cardsBtn.className = 'btn btn-outline-light btn-sm';
        localStorage.setItem('besoins_view', 'table');
    } else {
        tableView.style.display = 'none';
        cardsView.style.display = 'block';
        tableBtn.className = 'btn btn-outline-light btn-sm';
        cardsBtn.className = 'btn btn-light btn-sm';
        localStorage.setItem('besoins_view', 'cards');
    }
}

// Restaurer la vue précédente
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('besoins_view');
    if (savedView === 'cards') {
        toggleView('cards');
    }
});

// Export des données
function exportData() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    
    const link = document.createElement('a');
    link.href = '?' + params.toString();
    link.download = 'besoins_export_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php include '../includes/footer.php'; ?>