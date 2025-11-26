<?php
$page_title = "Détail du Besoin";
include '../includes/header.php';


// Récupération de l'ID
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlashMessage('error', 'ID de besoin invalide.');
    redirect('liste-besoins.php');
}

// Mode édition
$editMode = isset($_GET['edit']) && $_GET['edit'] == '1';

// Récupération du besoin
try {
    $besoin = getBesoinById($id);
    if (!$besoin) {
        setFlashMessage('error', 'Besoin non trouvé.');
        redirect('liste-besoins.php');
    }
} catch (Exception $e) {
    setFlashMessage('error', 'Erreur lors du chargement du besoin : ' . $e->getMessage());
    redirect('liste-besoins.php');
}

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $editMode) {
    $errors = [];
    
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token de sécurité invalide.";
    }
    
    // Validation des champs
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $priorite = sanitize($_POST['priorite'] ?? '');
    $statut = sanitize($_POST['statut'] ?? '');
    $categorie = sanitize($_POST['categorie'] ?? '');
    $demandeur_nom = sanitize($_POST['demandeur_nom'] ?? '');
    $demandeur_email = sanitize($_POST['demandeur_email'] ?? '');
    $cout_estime = sanitize($_POST['cout_estime'] ?? '');
    $delai_souhaite = sanitize($_POST['delai_souhaite'] ?? '');
    
    // Validation des champs obligatoires
    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($priorite)) $errors[] = "La priorité est obligatoire.";
    if (empty($statut)) $errors[] = "Le statut est obligatoire.";
    if (empty($categorie)) $errors[] = "La catégorie est obligatoire.";
    if (empty($demandeur_nom)) $errors[] = "Le nom du demandeur est obligatoire.";
    if (empty($demandeur_email)) $errors[] = "L'email du demandeur est obligatoire.";
    
    // Validation de l'email
    if (!empty($demandeur_email) && !validateEmail($demandeur_email)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    
    // Validation de la date
    if (!empty($delai_souhaite) && !validateDate($delai_souhaite)) {
        $errors[] = "La date souhaitée n'est pas valide.";
    }
    
    // Validation du coût
    if (!empty($cout_estime) && (!is_numeric($cout_estime) || $cout_estime < 0)) {
        $errors[] = "Le coût estimé doit être un nombre positif.";
    }
    
    // Si pas d'erreurs, mettre à jour
    if (empty($errors)) {
        try {
            $data = [
                'titre' => $titre,
                'description' => $description,
                'priorite' => $priorite,
                'statut' => $statut,
                'categorie' => $categorie,
                'demandeur_nom' => $demandeur_nom,
                'demandeur_email' => $demandeur_email,
                'cout_estime' => $cout_estime,
                'delai_souhaite' => $delai_souhaite
            ];
            
            if (updateBesoin($id, $data)) {
                setFlashMessage('success', 'Le besoin a été mis à jour avec succès !');
                redirect("detail-besoin.php?id=$id");
            } else {
                $errors[] = "Erreur lors de la mise à jour du besoin.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de base de données : " . $e->getMessage();
        }
    }
}

// Générer le token CSRF pour l'édition
if ($editMode) {
    $csrfToken = generateCSRFToken();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-file-text me-2 text-primary"></i>
        <?php echo $editMode ? 'Modifier le Besoin' : 'Détail du Besoin'; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="liste-besoins.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Retour à la Liste
            </a>
            <?php if (!$editMode): ?>
            <a href="detail-besoin.php?id=<?php echo $id; ?>&edit=1" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>
                Modifier
            </a>
            <a href="liste-besoins.php?delete=<?php echo $id; ?>" class="btn btn-danger btn-delete">
                <i class="bi bi-trash me-1"></i>
                Supprimer
            </a>
            <?php else: ?>
            <a href="detail-besoin.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                <i class="bi bi-x me-1"></i>
                Annuler
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Affichage des erreurs en mode édition -->
<?php if (!empty($errors) && $editMode): ?>
<div class="alert alert-danger">
    <h5><i class="bi bi-exclamation-triangle me-2"></i>Erreurs détectées :</h5>
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <?php if ($editMode): ?>
        <!-- Formulaire d'édition -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Modification du Besoin
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="editBesoinForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Informations Générales
                            </h6>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="titre" class="form-label">Titre du Besoin *</label>
                            <input type="text" class="form-control" id="titre" name="titre"
                                value="<?php echo htmlspecialchars($besoin['titre']); ?>" required maxlength="200">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priorite" class="form-label">Priorité *</label>
                            <select class="form-select" id="priorite" name="priorite" required>
                                <option value="faible"
                                    <?php echo ($besoin['priorite'] === 'faible') ? 'selected' : ''; ?>>
                                    Faible
                                </option>
                                <option value="moyenne"
                                    <?php echo ($besoin['priorite'] === 'moyenne') ? 'selected' : ''; ?>>
                                    Moyenne
                                </option>
                                <option value="haute"
                                    <?php echo ($besoin['priorite'] === 'haute') ? 'selected' : ''; ?>>
                                    Haute
                                </option>
                                <option value="critique"
                                    <?php echo ($besoin['priorite'] === 'critique') ? 'selected' : ''; ?>>
                                    Critique
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut *</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="nouveau"
                                    <?php echo ($besoin['statut'] === 'nouveau') ? 'selected' : ''; ?>>
                                    Nouveau
                                </option>
                                <option value="en_cours"
                                    <?php echo ($besoin['statut'] === 'en_cours') ? 'selected' : ''; ?>>
                                    En cours
                                </option>
                                <option value="termine"
                                    <?php echo ($besoin['statut'] === 'termine') ? 'selected' : ''; ?>>
                                    Terminé
                                </option>
                                <option value="rejete"
                                    <?php echo ($besoin['statut'] === 'rejete') ? 'selected' : ''; ?>>
                                    Rejeté
                                </option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="categorie" class="form-label">Catégorie *</label>
                            <input type="text" class="form-control" id="categorie" name="categorie"
                                value="<?php echo htmlspecialchars($besoin['categorie']); ?>" required maxlength="100">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description Détaillée *</label>
                            <textarea class="form-control" id="description" name="description" rows="5"
                                required><?php echo htmlspecialchars($besoin['description']); ?></textarea>
                        </div>
                    </div>

                    <!-- Informations demandeur -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fw-bold mb-3">
                                <i class="bi bi-person me-2"></i>
                                Informations du Demandeur
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="demandeur_nom" class="form-label">Nom Complet *</label>
                            <input type="text" class="form-control" id="demandeur_nom" name="demandeur_nom"
                                value="<?php echo htmlspecialchars($besoin['demandeur_nom']); ?>" required
                                maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="demandeur_email" class="form-label">Adresse Email *</label>
                            <input type="email" class="form-control" id="demandeur_email" name="demandeur_email"
                                value="<?php echo htmlspecialchars($besoin['demandeur_email']); ?>" required
                                maxlength="150">
                        </div>
                    </div>

                    <!-- Informations projet -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fw-bold mb-3">
                                <i class="bi bi-calendar-check me-2"></i>
                                Informations Projet
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cout_estime" class="form-label">Coût Estimé (€)</label>
                            <input type="number" class="form-control" id="cout_estime" name="cout_estime"
                                value="<?php echo htmlspecialchars($besoin['cout_estime'] ?? ''); ?>" min="0"
                                step="0.01">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="delai_souhaite" class="form-label">Délai Souhaité</label>
                            <input type="date" class="form-control" id="delai_souhaite" name="delai_souhaite"
                                value="<?php echo $besoin['delai_souhaite'] ?? ''; ?>">
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="detail-besoin.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Mettre à Jour
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Affichage des détails -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    <?php echo htmlspecialchars($besoin['titre']); ?>
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-<?php echo getPriorityClass($besoin['priorite']); ?> fs-6">
                        <?php echo getPriorityLabel($besoin['priorite']); ?>
                    </span>
                    <span class="badge bg-<?php echo getStatusClass($besoin['statut']); ?> fs-6">
                        <?php echo getStatusLabel($besoin['statut']); ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Description -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">
                        <i class="bi bi-file-text me-2"></i>
                        Description
                    </h6>
                    <p class="text-justify lh-lg">
                        <?php echo nl2br(htmlspecialchars($besoin['description'])); ?>
                    </p>
                </div>

                <!-- Informations du demandeur -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">
                        <i class="bi bi-person me-2"></i>
                        Demandeur
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($besoin['demandeur_nom']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email :</strong>
                                <a href="mailto:<?php echo htmlspecialchars($besoin['demandeur_email']); ?>"
                                    class="text-decoration-none">
                                    <?php echo htmlspecialchars($besoin['demandeur_email']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informations projet -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">
                        <i class="bi bi-briefcase me-2"></i>
                        Informations Projet
                    </h6>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Catégorie :</strong><br>
                                <span class="badge bg-light text-dark border fs-6">
                                    <?php echo htmlspecialchars($besoin['categorie']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Coût Estimé :</strong><br>
                                <span class="fs-5 fw-bold text-success">
                                    <?php echo $besoin['cout_estime'] ? formatCurrency($besoin['cout_estime']) : 'Non spécifié'; ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Délai Souhaité :</strong><br>
                                <span
                                    class="<?php echo $besoin['delai_souhaite'] && strtotime($besoin['delai_souhaite']) < time() ? 'text-danger fw-bold' : ''; ?>">
                                    <?php echo $besoin['delai_souhaite'] ? formatDate($besoin['delai_souhaite']) : 'Non spécifié'; ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Date de Création :</strong><br>
                                <?php echo formatDateTime($besoin['date_creation']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($besoin['date_modification'] !== $besoin['date_creation']): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Dernière modification :</strong> <?php echo formatDateTime($besoin['date_modification']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Informations rapides -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Résumé
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>ID :</strong> #<?php echo $besoin['id']; ?>
                    </li>
                    <li class="mb-2">
                        <strong>Priorité :</strong>
                        <span class="badge bg-<?php echo getPriorityClass($besoin['priorite']); ?>">
                            <?php echo getPriorityLabel($besoin['priorite']); ?>
                        </span>
                    </li>
                    <li class="mb-2">
                        <strong>Statut :</strong>
                        <span class="badge bg-<?php echo getStatusClass($besoin['statut']); ?>">
                            <?php echo getStatusLabel($besoin['statut']); ?>
                        </span>
                    </li>
                    <li class="mb-2">
                        <strong>Catégorie :</strong> <?php echo htmlspecialchars($besoin['categorie']); ?>
                    </li>
                    <?php if ($besoin['cout_estime']): ?>
                    <li class="mb-2">
                        <strong>Budget :</strong> <?php echo formatCurrency($besoin['cout_estime']); ?>
                    </li>
                    <?php endif; ?>
                    <?php if ($besoin['delai_souhaite']): ?>
                    <li class="mb-2">
                        <strong>Échéance :</strong>
                        <span
                            class="<?php echo strtotime($besoin['delai_souhaite']) < time() ? 'text-danger fw-bold' : ''; ?>">
                            <?php echo formatDate($besoin['delai_souhaite']); ?>
                            <?php if (strtotime($besoin['delai_souhaite']) < time()): ?>
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (!$editMode): ?>
                    <a href="detail-besoin.php?id=<?php echo $id; ?>&edit=1" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Modifier ce Besoin
                    </a>

                    <a href="mailto:<?php echo htmlspecialchars($besoin['demandeur_email']); ?>?subject=Concernant votre besoin: <?php echo urlencode($besoin['titre']); ?>"
                        class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-1"></i>
                        Contacter le Demandeur
                    </a>

                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i>
                        Imprimer
                    </button>

                    <a href="liste-besoins.php?delete=<?php echo $id; ?>" class="btn btn-outline-danger btn-delete">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </a>
                    <?php else: ?>
                    <a href="detail-besoin.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x me-1"></i>
                        Annuler la Modification
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i>
                    Navigation
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="liste-besoins.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>
                        Tous les Besoins
                    </a>
                    <a href="ajouter-besoin.php" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nouveau Besoin
                    </a>
                    <a href="../index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-house me-1"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($editMode): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editBesoinForm');

    // Validation en temps réel
    form.addEventListener('submit', function(e) {
        if (!validateForm(this)) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
});
</script>
<?php endif; ?>

<style>
@media print {

    .btn,
    .card-header,
    .btn-toolbar,
    .border-bottom {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .col-lg-4 {
        display: none !important;
    }

    .col-lg-8 {
        width: 100% !important;
    }

    .badge {
        border: 1px solid #000 !important;
        background: white !important;
        color: black !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>