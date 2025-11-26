<?php
$page_title = "Ajouter un Besoin";
include '../includes/header.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token de sécurité invalide.";
    }
    
    // Validation des champs
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $priorite = sanitize($_POST['priorite'] ?? '');
    $categorie = sanitize($_POST['categorie'] ?? '');
    $demandeur_nom = sanitize($_POST['demandeur_nom'] ?? '');
    $demandeur_email = sanitize($_POST['demandeur_email'] ?? '');
    $cout_estime = sanitize($_POST['cout_estime'] ?? '');
    $delai_souhaite = sanitize($_POST['delai_souhaite'] ?? '');
    
    // Validation des champs obligatoires
    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($priorite)) $errors[] = "La priorité est obligatoire.";
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
    
    // Si pas d'erreurs, enregistrer
    if (empty($errors)) {
        try {
            $data = [
                'titre' => $titre,
                'description' => $description,
                'priorite' => $priorite,
                'categorie' => $categorie,
                'demandeur_nom' => $demandeur_nom,
                'demandeur_email' => $demandeur_email,
                'cout_estime' => $cout_estime,
                'delai_souhaite' => $delai_souhaite
            ];
            
            if (createBesoin($data)) {
                setFlashMessage('success', 'Le besoin a été ajouté avec succès !');
                redirect('liste-besoins.php');
            } else {
                $errors[] = "Erreur lors de l'enregistrement du besoin.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de base de données : " . $e->getMessage();
        }
    }
}

// Générer le token CSRF
$csrfToken = generateCSRFToken();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle me-2 text-success"></i>
        Ajouter un Nouveau Besoin
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="liste-besoins.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Retour à la Liste
            </a>
        </div>
    </div>
</div>

<!-- Affichage des erreurs -->
<?php if (!empty($errors)): ?>
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
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-form me-2"></i>
                    Formulaire d'Expression du Besoin
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="besoinForm" novalidate>
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
                                value="<?php echo htmlspecialchars($titre ?? ''); ?>" required maxlength="200"
                                placeholder="Ex: Développement d'une application mobile">
                            <div class="invalid-feedback">
                                Veuillez saisir un titre pour le besoin.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priorite" class="form-label">Priorité *</label>
                            <select class="form-select" id="priorite" name="priorite" required>
                                <option value="">Sélectionnez une priorité</option>
                                <option value="faible"
                                    <?php echo (($priorite ?? '') === 'faible') ? 'selected' : ''; ?>>
                                    Faible
                                </option>
                                <option value="moyenne"
                                    <?php echo (($priorite ?? '') === 'moyenne') ? 'selected' : ''; ?>>
                                    Moyenne
                                </option>
                                <option value="haute" <?php echo (($priorite ?? '') === 'haute') ? 'selected' : ''; ?>>
                                    Haute
                                </option>
                                <option value="critique"
                                    <?php echo (($priorite ?? '') === 'critique') ? 'selected' : ''; ?>>
                                    Critique
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner une priorité.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="categorie" class="form-label">Catégorie *</label>
                            <input type="text" class="form-control" id="categorie" name="categorie"
                                value="<?php echo htmlspecialchars($categorie ?? ''); ?>" required maxlength="100"
                                placeholder="Ex: Développement, Web Design, ERP..." list="categoriesList">
                            <datalist id="categoriesList">
                                <option value="Développement">
                                <option value="Web Design">
                                <option value="ERP">
                                <option value="Formation">
                                <option value="Infrastructure">
                                <option value="Marketing">
                                <option value="Support">
                            </datalist>
                            <div class="invalid-feedback">
                                Veuillez saisir une catégorie.
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description Détaillée *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required
                                placeholder="Décrivez précisément votre besoin, les objectifs, les contraintes, les livrables attendus..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            <div class="form-text">
                                Soyez aussi précis que possible pour faciliter l'analyse et la réalisation.
                            </div>
                            <div class="invalid-feedback">
                                Veuillez fournir une description détaillée.
                            </div>
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
                                value="<?php echo htmlspecialchars($demandeur_nom ?? ''); ?>" required maxlength="100"
                                placeholder="Prénom NOM">
                            <div class="invalid-feedback">
                                Veuillez saisir le nom du demandeur.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="demandeur_email" class="form-label">Adresse Email *</label>
                            <input type="email" class="form-control" id="demandeur_email" name="demandeur_email"
                                value="<?php echo htmlspecialchars($demandeur_email ?? ''); ?>" required maxlength="150"
                                placeholder="email@exemple.com">
                            <div class="invalid-feedback">
                                Veuillez saisir une adresse email valide.
                            </div>
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
                                value="<?php echo htmlspecialchars($cout_estime ?? ''); ?>" min="0" step="0.01"
                                placeholder="Ex: 15000.00">
                            <div class="form-text">
                                Montant estimé en euros (optionnel)
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="delai_souhaite" class="form-label">Délai Souhaité</label>
                            <input type="date" class="form-control" id="delai_souhaite" name="delai_souhaite"
                                value="<?php echo htmlspecialchars($delai_souhaite ?? ''); ?>"
                                min="<?php echo date('Y-m-d'); ?>">
                            <div class="form-text">
                                Date limite souhaitée pour la réalisation (optionnel)
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="liste-besoins.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Annuler
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Enregistrer le Besoin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar avec aide -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Conseils de Rédaction
                </h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Pour une description efficace :</h6>
                <ul class="small mb-3">
                    <li>Décrivez clairement le contexte et les objectifs</li>
                    <li>Listez les fonctionnalités attendues</li>
                    <li>Mentionnez les contraintes techniques</li>
                    <li>Précisez les livrables souhaités</li>
                    <li>Indiquez les utilisateurs cibles</li>
                </ul>

                <h6 class="fw-bold">Niveaux de priorité :</h6>
                <ul class="small mb-0">
                    <li><span class="badge bg-secondary">Faible</span> - Amélioration future</li>
                    <li><span class="badge bg-info">Moyenne</span> - Important mais pas urgent</li>
                    <li><span class="badge bg-warning">Haute</span> - Urgent et important</li>
                    <li><span class="badge bg-danger">Critique</span> - Bloquant pour l'activité</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    Validation Automatique
                </h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">Le formulaire valide automatiquement :</p>
                <ul class="small mb-0">
                    <li>Format de l'adresse email</li>
                    <li>Cohérence des dates</li>
                    <li>Format des montants</li>
                    <li>Longueur des textes</li>
                    <li>Champs obligatoires</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('besoinForm');

    // Validation en temps réel
    form.addEventListener('submit', function(e) {
        if (!validateForm(this)) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });

    // Validation de l'email en temps réel
    const emailField = document.getElementById('demandeur_email');
    emailField.addEventListener('blur', function() {
        if (this.value && !isValidEmail(this.value)) {
            this.setCustomValidity('Adresse email invalide');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validation de la date
    const dateField = document.getElementById('delai_souhaite');
    dateField.addEventListener('change', function() {
        if (this.value) {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                this.setCustomValidity('La date ne peut pas être antérieure à aujourd\'hui');
            } else {
                this.setCustomValidity('');
            }
        }
    });

    // Compteur de caractères pour la description
    const descriptionField = document.getElementById('description');
    const maxLength = 5000;

    // Créer l'indicateur de compteur
    const counter = document.createElement('div');
    counter.className = 'form-text';
    counter.id = 'descriptionCounter';
    descriptionField.parentNode.appendChild(counter);

    function updateCounter() {
        const remaining = maxLength - descriptionField.value.length;
        counter.textContent = `${descriptionField.value.length} / ${maxLength} caractères`;

        if (remaining < 100) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }

        if (remaining < 0) {
            counter.classList.add('text-danger');
            counter.classList.remove('text-warning');
        } else {
            counter.classList.remove('text-danger');
        }
    }

    descriptionField.addEventListener('input', updateCounter);
    updateCounter(); // Initialisation

    // Sauvegarde automatique en brouillon (localStorage)
    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        // Charger les données sauvegardées
        const savedValue = localStorage.getItem('besoin_' + field.name);
        if (savedValue && !field.value) {
            field.value = savedValue;
        }

        // Sauvegarder à chaque changement
        field.addEventListener('input', function() {
            localStorage.setItem('besoin_' + this.name, this.value);
        });
    });

    // Nettoyer le localStorage après soumission réussie
    form.addEventListener('submit', function() {
        if (this.checkValidity()) {
            fields.forEach(field => {
                localStorage.removeItem('besoin_' + field.name);
            });
        }
    });

    // Auto-suggestion pour les catégories basée sur l'historique
    const categorieField = document.getElementById('categorie');
    categorieField.addEventListener('focus', function() {
        // Ici on pourrait charger les catégories depuis la base de données
        console.log('Chargement des suggestions de catégories...');
    });
});
</script>

<?php include '../includes/footer.php'; ?>