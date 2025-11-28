<?php
$page_title = "Mon Profil";
// Remplacez par le bon chemin si nécessaire. Assurez-vous que session_start() est fait dans l'un de ces includes.
// require_once '../config/auth.php'; 
require_once '../includes/functions.php'; // Pour getConnection, setFlashMessage, displayFlashMessages
include '../includes/header.php'; 

// --- DONNÉES UTILISATEUR DE SESSION SIMPLIFIÉES ---
$user_id = $_SESSION['user_id'] ?? 0;
$nom_initial = $_SESSION['user_nom'] ?? '';
$email_initial = $_SESSION['user_email'] ?? '';
$role = $_SESSION['user_role'] ?? 'Utilisateur'; // Rôle non modifiable

// Initialisation des variables pour le formulaire (utiliser les valeurs initiales)
$nom = $nom_initial;
$email = $email_initial;

// --- TRAITEMENT DU FORMULAIRE DE MISE À JOUR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération des données POST
    $nom_post = trim($_POST['nom'] ?? '');
    $email_post = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $errors = [];

    // Validation des champs obligatoires (en utilisant les valeurs postées)
    if (!$nom_post || !$email_post) {
        $errors[] = "Le nom et l'email sont obligatoires.";
    } elseif (!filter_var($email_post, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    // Validation du mot de passe
    if ($password) {
        if ($password !== $password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
    }
    
    // Mettre à jour les variables pour le réaffichage du formulaire en cas d'erreur
    $nom = $nom_post;
    $email = $email_post;
    
    if (empty($errors)) {
        try {
            $conn = getConnection();
            $conn->beginTransaction();

            // --- REQUÊTE SQL SIMPLIFIÉE ---
            // On ne met à jour que nom et email
            $sql = "UPDATE users SET nom=:nom, email=:email";
            if ($password) {
                $sql .= ", motdepasse=:motdepasse";
            }
            $sql .= " WHERE id=:id";

            $stmt = $conn->prepare($sql);
            
            // Lier les paramètres
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $user_id);

            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':motdepasse', $hash);
            }
            
            $stmt->execute();
            $conn->commit();

            // Mise à jour de la session après succès
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_email'] = $email;

            setFlashMessage('success', 'Profil mis à jour avec succès.');
            header("Location: profil.php");
            exit();
            
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
             if ($e->getCode() == '23000') {
                 $errors[] = "L'adresse email est déjà utilisée par un autre compte.";
             } else {
                 $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
             }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold mb-4 text-primary">
                <i class="bi bi-person-badge me-2"></i> Mon Profil
            </h1>
            <hr class="mb-4">
            
            <?php getFlashMessage(); // Affichage du message de succès ?>
            
            <?php if (!empty($errors)): ?> 
            <div class="alert alert-danger shadow-sm" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur(s) détectée(s) :</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="post" class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i> Informations de Base
                    </h4>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label fw-bold">Nom Complet *</label>
                                <input type="text" class="form-control" id="nom" name="nom"
                                    value="<?php echo htmlspecialchars($nom); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($email); ?>" required>
                                <div class="form-text">Cet email est votre identifiant de connexion.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold">Rôle</label>
                                <p class="form-control-static border p-2 rounded bg-light">
                                    <i class="bi bi-person-check me-2"></i>
                                    <?php echo htmlspecialchars($role); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h4 class="mb-3 text-dark">
                        <i class="bi bi-shield-lock me-2"></i> Changer le Mot de Passe (Optionnel)
                    </h4>
                    <div class="alert alert-info small" role="alert">
                        Laissez ces champs vides pour conserver votre mot de passe actuel.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau Mot de Passe</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="8"
                                    placeholder="Minimum 8 caractères">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmer le Mot de Passe</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                    placeholder="Confirmez le nouveau mot de passe">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer d-flex justify-content-end bg-light border-0">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-save me-2"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>