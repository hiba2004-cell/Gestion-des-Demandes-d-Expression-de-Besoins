<?php
$page_title = "Ajouter un Besoin";
include '../includes/header.php';


// Définition du chemin d'upload (à adapter selon votre structure)
define('UPLOAD_DIR', __DIR__ . '/../uploads/pieces_jointes/');
// Taille maximale autorisée en octets (ex: 5 Mo)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
// Extensions autorisées
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);



// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $uploaded_files_paths = [];
    
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token de sécurité invalide.";
    }
    
    // Validation des champs
    $description = sanitize($_POST['description'] ?? '');
    $priorite = sanitize($_POST['priorite'] ?? '');
    $categorie = sanitize($_POST['categorie'] ?? '');

    $demandeur_id = sanitize($_SESSION['user_id'] ?? '');

    $cout_estime = sanitize($_POST['cout_estime'] ?? '');
    $delai_souhaite = sanitize($_POST['delai_souhaite'] ?? '');
    
    // Validation des champs obligatoires
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($priorite)) $errors[] = "La priorité est obligatoire.";
    if (empty($categorie)) $errors[] = "La catégorie est obligatoire.";

    
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
                'description' => $description,
                'priorite' => $priorite,
                'categorie' => $categorie,
                'demandeur_id' => $demandeur_id,
            ];
            
            $besoin_id = createBesoin($data);
           if ($besoin_id) {
            $demand_id = $_SESSION['user_id'];
            // 2. Traitement des Fichiers Joints
            if (!empty($_FILES['fichiers']['name'][0])) {
                
                // On s'assure que le répertoire d'upload existe
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0777, true);
                }

                // Boucle sur les fichiers (fichiers[] est un tableau)
                foreach ($_FILES['fichiers']['name'] as $key => $name) {
                    // Vérification de l'upload et des erreurs PHP
                    if ($_FILES['fichiers']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['fichiers']['tmp_name'][$key];
                        $file_size = $_FILES['fichiers']['size'][$key];
                        $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                        // Validation du fichier
                        if ($file_size > MAX_FILE_SIZE) {
                            $errors[] = "Le fichier " . htmlspecialchars($name) . " est trop volumineux (Max " . (MAX_FILE_SIZE / 1024 / 1024) . " Mo).";
                            continue;
                        }
                        if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
                            $errors[] = "Le format du fichier " . htmlspecialchars($name) . " n'est pas autorisé.";
                            continue;
                        }

                        // Génération d'un nom de fichier unique et sécurisé
                        $new_file_name = uniqid('pj_') . '.' . $file_ext;
                        $target_file = UPLOAD_DIR . $new_file_name;
                        
                        // Déplacement du fichier temporaire
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Enregistrement dans la table pieces_jointes
                            $file_path_for_db = '/besoins/uploads/pieces_jointes/' . $new_file_name; // Chemin relatif pour la DB
                            
                            if (savePieceJointe($demand_id, $file_path_for_db)) {
                                $uploaded_files_paths[] = $target_file; // Ajout pour le nettoyage si erreur DB
                            } else {
                                $errors[] = "Erreur lors de l'enregistrement du chemin du fichier " . htmlspecialchars($name) . " en base de données.";
                                // Le fichier est sur le disque, mais pas dans la DB. À gérer (Nettoyage à la fin)
                            }
                        } else {
                            $errors[] = "Erreur lors du déplacement du fichier " . htmlspecialchars($name) . " sur le serveur.";
                        }
                    } elseif ($_FILES['fichiers']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                         // Gérer d'autres erreurs d'upload (taille php.ini, etc.)
                        $errors[] = "Erreur d'upload PHP pour le fichier " . htmlspecialchars($name) . ": Code " . $_FILES['fichiers']['error'][$key];
                    }
                }
            }
            
            // Finalisation : Vérifier si des erreurs d'upload/DB ont été ajoutées
            if (empty($errors)) {
                // Tout est OK : Succès + Redirection
                setFlashMessage('success', 'Le besoin et ses pièces jointes ont été ajoutés avec succès !');
                redirect('liste-besoins.php');
            } else {
                 // S'il y a des erreurs dans les pièces jointes, il faut annuler
                 // la création du besoin principal et supprimer les fichiers déjà uploadés (Gestion de transaction)
                 
                 // 3. Gestion de l'échec et nettoyage
                 
                 // Suppression des fichiers déjà uploadés
                 foreach ($uploaded_files_paths as $path) {
                     if (file_exists($path)) {
                         unlink($path);
                     }
                 }
                 
                 // NOTE D'EXPERT : Idéalement, si la fonction createBesoin supporte les transactions PDO, 
                 // vous devriez faire un $db->rollBack() ici pour annuler la création du besoin dans la table principale aussi.
                 // Si createBesoin a réussi, mais les PJ ont échoué, vous devez soit supprimer le besoin, soit le marquer comme incomplet.
                 $errors[] = "Le besoin a été créé, mais certaines pièces jointes n'ont pas pu être enregistrées. Veuillez modifier le besoin pour les ajouter.";
            }

        } else {
            // Échec de la création du besoin principal
            $errors[] = "Erreur lors de l'enregistrement du besoin principal.";
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
                <form method="POST" id="besoinForm" novalidate enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Informations Générales
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priorite" class="form-label">Priorité *</label>
                            <select class="form-select" id="priorite" name="priorite" required>
                                <option value="">Sélectionnez une priorité</option>
                                <option value="Faible"
                                    <?php echo (($priorite ?? '') === 'Faible') ? 'selected' : ''; ?>>
                                    Faible
                                </option>
                                <option value="Moyenne"
                                    <?php echo (($priorite ?? '') === 'Moyenne') ? 'selected' : ''; ?>>
                                    Moyenne
                                </option>
                                <option value="Urgente" <?php echo (($priorite ?? '') === 'Urgente') ? 'selected' : ''; ?>>
                                    Urgente
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner une priorité.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="categorie" class="form-label">Catégorie *</label>
                            <select class="form-select" id="categorie" name="categorie" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="1"
                                    <?php echo (($categorie ?? '') == '1') ? 'selected' : ''; ?>>
                                    Matériel
                                </option>
                                <option value="2"
                                    <?php echo (($categorie ?? '') == '2') ? 'selected' : ''; ?>>
                                    Logiciel
                                </option>
                                <option value="3" <?php echo (($categorie ?? '') == '3') ? 'selected' : ''; ?>>
                                    Service
                                </option>
                                <option value="4"
                                    <?php echo (($categorie ?? '') === '4') ? 'selected' : ''; ?>>
                                    Autre
                                </option>
                            </select>
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

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fw-bold mb-3">
                                <i class="bi bi-paperclip me-2"></i>
                                Pièces Jointes (Optionnel)
                            </h6>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="fichiers" class="form-label">Sélectionner des Fichiers (Max 5Mo)</label>
                            <input type="file" class="form-control" id="fichiers" name="fichiers[]" multiple
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">
                                Formats acceptés : PDF, images (JPG, PNG), documents (DOC/DOCX).
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
                    <li><span class="badge mt-2 bg-secondary">Faible</span> - Amélioration future</li>
                    <li><span class="badge mt-2 bg-info">Moyenne</span> - Important mais pas urgent</li>
                    <li><span class="badge mt-2 bg-danger">Urgence</span> - Bloquant pour l'activité</li>
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