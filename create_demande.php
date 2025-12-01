<?php
$page_title = "Créer une demande";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getConnection();

$errors = [];
$success = "";

// Définition du chemin d'upload (à adapter selon votre structure)
define('UPLOAD_DIR', __DIR__ . '/../uploads/pieces_jointes/');
// Taille maximale autorisée en octets (ex: 5 Mo)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
// Extensions autorisées
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);


// Récupération des types de besoins depuis la table types_besoins
try {
    $stmtTypes = $conn->query("SELECT id, libelle FROM types_besoins ORDER BY libelle ASC");
    $types_besoins = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

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

    // Validation des champs obligatoires
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($priorite)) $errors[] = "La priorité est obligatoire.";
    if (empty($categorie)) $errors[] = "La catégorie est obligatoire.";
    
    // Si pas d'erreurs, enregistrer
    if (empty($errors)) {
        try {
            $data = [
                'demandeur_id' => $user_id,
                'categorie' => $categorie,
                'description' => $description,
                'priorite' => $priorite,
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
                redirect('dashboard-demandeur.php');
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

<div class="container mt-4">
    <h2>Créer une nouvelle demande</h2>

     <!-- Quick action button: Check suggestions -->
    <div class="mb-4">
        <a href="suggested-demande.php" class="btn btn-success btn-lg d-flex align-items-center">
            <i class="bi bi-lightbulb me-2"></i>
            Voir les suggestions disponibles
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="POST" novalidate enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

        <div class="mb-3">
            <label for="categorie" class="form-label">Type de besoin</label>
            <select name="categorie" id="categorie" class="form-select" required>
                <option value="">-- Sélectionnez un type --</option>
                <?php foreach ($types_besoins as $type): ?>
                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="priorite" class="form-label">Urgence</label>
            <select name="priorite" id="priorite" class="form-select" required>
                <option value="">-- Sélectionnez --</option>
                <option value="Faible">Faible</option>
                <option value="Moyenne">Moyenne</option>
                <option value="Urgente">Urgente</option>
            </select>
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
</div>

<button type="submit" class="btn btn-primary">Créer la demande</button>
</form>
</div>

<?php include 'includes/footer.php'; ?>