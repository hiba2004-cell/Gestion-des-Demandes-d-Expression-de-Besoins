<?php
$page_title = "Gestion des Utilisateurs";
include '../includes/header.php';


/**
 * Retourne la classe Bootstrap basée sur le rôle.
 */
function getRoleClass(string $role): string {
    return match ($role) {
        'Administrateur' => 'danger',
        'Validateur' => 'warning',
        'Demandeur' => 'info',
        default => 'secondary',
    };
}


// --- Logique du Contrôleur ---

// Paramètres de pagination
$page = max(1, intval($_GET['page'] ?? 1));
$itemsPerPage = 10;

// Filtres
// J'assume que la fonction sanitize() et la constante ROLES_LIST existent
$filters = [
    'role' => sanitize($_GET['role'] ?? ''),
    'search' => sanitize($_GET['search'] ?? '')
];

// Récupération des données
try {
    // Remplacez par votre fonction BDD réelle
    $result = getUsers($filters, $itemsPerPage, ($page - 1) * $itemsPerPage);
    $utilisateurs = $result['utilisateurs'];
    $totalUtilisateurs = $result['total'];
    // J'assume que la fonction paginate() existe
    $pagination = paginate($totalUtilisateurs, $itemsPerPage, $page); 
} catch (Exception $e) {
    // J'assume que la fonction setFlashMessage() existe
    setFlashMessage('error', 'Erreur lors du chargement des utilisateurs : ' . $e->getMessage()); 
    $utilisateurs = [];
    $totalUtilisateurs = 0;
    $pagination = paginate(0, $itemsPerPage, 1);
}

// Traitement de la suppression (Méthode PRG)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    try {
        if (deleteUser($deleteId)) { // Remplacez par votre fonction BDD réelle
            setFlashMessage('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }
    } catch (Exception $e) {
        setFlashMessage('error', 'Erreur de base de données : ' . $e->getMessage());
    }
    
    // Redirection pour éviter la suppression accidentelle au rafraîchissement
    $queryParams = $_GET;
    unset($queryParams['delete']);
    $redirectUrl = 'liste-utilisateurs.php';
    if (!empty($queryParams)) {
        $redirectUrl .= '?' . http_build_query($queryParams);
    }
    // J'assume que la fonction redirect() existe
    redirect($redirectUrl); 
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people me-2 text-primary"></i>
        Liste des Utilisateurs
        <span class="badge bg-secondary ms-2"><?php echo $totalUtilisateurs; ?></span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="ajouter-utilisateur.php" class="btn btn-success">
                <i class="bi bi-person-plus me-1"></i>
                Nouvel Utilisateur
            </a>
            <button class="btn btn-outline-primary" onclick="exportData()"> 
                <i class="bi bi-download me-1"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<hr/>

<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>
            Filtres et Recherche
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($filters['search']); ?>"
                       placeholder="Nom, email...">
            </div>
            
            <div class="col-md-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Tous les Rôles</option>
                    <option value="Administrateur" <?php echo ($filters['role'] === 'Administrateur') ? 'selected' : ''; ?>>
                        Administrateur
                    </option>
                    <option value="Validateur" <?php echo ($filters['role'] === 'Validateur') ? 'selected' : ''; ?>>
                        Éditeur
                    </option>
                    <option value="Demandeur" <?php echo ($filters['role'] === 'Demandeur') ? 'selected' : ''; ?>>
                        Membre
                    </option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
             
             <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <a href="liste-utilisateurs.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Vider
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i class="bi bi-table me-2"></i>
            Résultats (<?php echo $totalUtilisateurs; ?> utilisateurs trouvés)
        </h6>
        </div>
    <div class="card-body p-0">
        <?php if (!empty($utilisateurs)): ?>
            <div id="tableView" class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%;">Nom et Email</th>
                            <th style="width: 20%;">Rôle</th>
                            <th style="width: 20%;">Date de Création</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong class="d-block"><?php echo htmlspecialchars($user['nom']); ?></strong>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope me-1"></i>
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getRoleClass($user['role']); ?> fs-6">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo formatDate($user['created_at']); ?> 
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="modifier-utilisateur.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-outline-warning"
                                           data-bs-toggle="tooltip" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['delete' => $user['id']])); ?>" 
                                           class="btn btn-outline-danger btn-delete"
                                           data-bs-toggle="tooltip" 
                                           title="Supprimer"
                                           data-user-nom="<?php echo htmlspecialchars($user['nom']); ?>">
                                            <i class="bi bi-trash"></i>
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
                <i class="bi bi-person-x display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Aucun utilisateur trouvé</h4>
                <p class="text-muted">
                    <?php if (!empty(array_filter($filters))): ?>
                        Essayez de modifier vos critères de recherche
                    <?php else: ?>
                        Commencez par ajouter votre premier utilisateur
                    <?php endif; ?>
                </p>
                <div class="mt-3">
                    <?php if (!empty(array_filter($filters))): ?>
                        <a href="liste-utilisateurs.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Réinitialiser les Filtres
                        </a>
                    <?php endif; ?>
                    <a href="ajouter-utilisateur.php" class="btn btn-success">
                        <i class="bi bi-person-plus me-1"></i>
                        Ajouter un Utilisateur
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($totalUtilisateurs > 0): ?>
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Affichage de <?php echo ($pagination['offset'] + 1); ?> à 
                        <?php echo min($pagination['offset'] + $itemsPerPage, $totalUtilisateurs); ?> 
                        sur <?php echo $totalUtilisateurs; ?> utilisateurs
                    </p>
                </div>
                <div class="col-md-6">
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm justify-content-end mb-0">
                                <?php if ($pagination['has_previous']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
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
// --- Script JS pour la confirmation de suppression et l'export ---

// Confirmation de suppression avec le nom de l'utilisateur
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            const userName = this.getAttribute('data-user-nom');
            const confirmation = confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ? Cette action est irréversible.`);
            if (!confirmation) {
                e.preventDefault();
            }
        });
    });
});

// Fonction d'exportation (similaire à la page des besoins)
function exportData() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    // Si votre fonction getUsers() gère l'export en CSV/Excel lorsque 'export=1' est présent,
    // ce lien déclenchera le téléchargement du fichier.
    window.location.href = '?' + params.toString(); 
}
</script>

<?php include '../includes/footer.php'; ?>