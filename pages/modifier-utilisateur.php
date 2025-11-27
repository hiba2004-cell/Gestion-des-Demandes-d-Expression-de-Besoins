<?php
$page_title = "Modifier l'Utilisateur";
include '../includes/header.php';


/**
 * Met à jour les données d'un utilisateur existant.
 * @param int $id ID de l'utilisateur.
 * @param array $userData Données à mettre à jour (nom, email, role, password_hash optionnel).
 * @return bool True si la mise à jour réussit.
 * @throws Exception En cas d'erreur BDD (ex: email déjà existant par un autre utilisateur).
 */
function updateUser(int $id, array $userData): bool {
    // 1. Logique de vérification (email unique, sauf pour l'utilisateur actuel)
    // 2. Hashage du mot de passe si fourni
    // 3. Mise à jour SQL (UPDATE users SET nom = :nom, ... WHERE id = :id)
    return true; 
}

// J'assume que sanitize(), redirect(), setFlashMessage() existent.
// J'assume que la liste des rôles $roles existe (définie ici pour la complétude)
$roles = [
    'membre' => 'Membre standard',
    'editeur' => 'Éditeur de contenu',
    'admin' => 'Administrateur (Accès complet)',
];

// --- 1. Vérification de l'ID et chargement des données ---
$userId = intval($_GET['id'] ?? 0);

if ($userId === 0) {
    setFlashMessage('error', 'Aucun identifiant utilisateur spécifié pour la modification.');
    redirect('liste-utilisateurs.php');
}

try {
    $existingUser = getUserById($userId);
} catch (Exception $e) {
    setFlashMessage('error', 'Erreur lors du chargement de l\'utilisateur : ' . $e->getMessage());
    redirect('liste-utilisateurs.php');
}

if (!$existingUser) {
    setFlashMessage('error', 'Utilisateur non trouvé.');
    redirect('liste-utilisateurs.php');
}

// Initialisation des données du formulaire avec les données existantes
$errors = [];
$formData = [
    'nom' => $existingUser['nom'],
    'email' => $existingUser['email'],
    'role' => $existingUser['role'],
    // Les champs de mot de passe sont laissés vides par sécurité
    'password' => '',
    'password_confirm' => ''
];


// --- 2. Traitement du Formulaire POST (Mise à jour) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour de $formData avec les nouvelles valeurs
    $formData['nom'] = sanitize($_POST['nom'] ?? '');
    $formData['email'] = sanitize($_POST['email'] ?? '');
    $formData['role'] = sanitize($_POST['role'] ?? '');
    $formData['password'] = $_POST['password'] ?? ''; 
    $formData['password_confirm'] = $_POST['password_confirm'] ?? '';

    // Validation des champs obligatoires (hors mot de passe)
    if (empty($formData['nom'])) {
        $errors['nom'] = "Le nom est requis.";
    }
    if (empty($formData['email'])) {
        $errors['email'] = "L'email est requis.";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Le format de l'email est invalide.";
    }
    if (empty($formData['role']) || !array_key_exists($formData['role'], $roles)) {
        $errors['role'] = "Le rôle sélectionné est invalide.";
    }

    // Validation du mot de passe SI il est saisi (changement optionnel)
    if (!empty($formData['password']) || !empty($formData['password_confirm'])) {
        if (strlen($formData['password']) < 8) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif ($formData['password'] !== $formData['password_confirm']) {
            $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
        }
    }

    // Si aucune erreur, procéder à la mise à jour
    if (empty($errors)) {
        try {
            $userToUpdate = [
                'nom' => $formData['nom'],
                'email' => $formData['email'],
                'role' => $formData['role'],
            ];

            // Ajouter le mot de passe seulement s'il a été modifié
            if (!empty($formData['password'])) {
                $userToUpdate['password'] = $formData['password'];
            }

            if (updateUser($userId, $userToUpdate)) {
                setFlashMessage('success', 'L\'utilisateur <h2>' . htmlspecialchars($formData['nom']) . '</h2> a été mis à jour avec succès.');
                redirect('liste-utilisateurs.php');
            } else {
                setFlashMessage('error', 'Erreur inconnue lors de la mise à jour.');
            }
        } catch (Exception $e) {
            $errors['general'] = 'Erreur BDD : ' . $e->getMessage();
            setFlashMessage('error', 'Échec de la mise à jour : ' . $e->getMessage());
        }
    } else {
        setFlashMessage('warning', 'Veuillez corriger les erreurs dans le formulaire.');
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 d-flex flex-row align-items-center gap-2">
            <i class="bi bi-pencil-square me-2 text-warning"></i>
            <div>
                <?php echo $page_title; ?> :
                <strong><?php echo htmlspecialchars($existingUser['nom']); ?></strong>
            </div>
    </h1>

    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="liste-utilisateurs.php" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Retour à la Liste
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="card-title mb-0">Informations Personnelles</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="modifier-utilisateur.php?id=<?php echo $userId; ?>" class="row g-3">
            
            <div class="col-md-6">
                <label for="nom" class="form-label">Nom Complet <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" 
                       id="nom" 
                       name="nom" 
                       value="<?php echo htmlspecialchars($formData['nom']); ?>"
                       required>
                <?php if (isset($errors['nom'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Adresse Email <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($formData['email']); ?>"
                       required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="role" class="form-label">Rôle de l'Utilisateur <span class="text-danger">*</span></label>
                <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                        id="role" 
                        name="role"
                        required>
                    <option value="">Sélectionner un rôle</option>
                    <?php foreach ($roles as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>"
                                <?php echo ($formData['role'] === $key) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['role']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <div class="alert alert-info mt-3 py-2 small">
                    <i class="bi bi-info-circle me-1"></i> Date de création : <?php echo htmlspecialchars($existingUser['created_at']); ?>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Changement de Mot de Passe (Optionnel)</h5>

            <div class="col-md-6">
                <label for="password" class="form-label">Nouveau Mot de Passe</label>
                <input type="password" 
                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                       id="password" 
                       name="password" 
                       minlength="8"
                       placeholder="Laisser vide pour ne pas changer">
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="password_confirm" class="form-label">Confirmer le Nouveau Mot de Passe</label>
                <input type="password" 
                       class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" 
                       id="password_confirm" 
                       name="password_confirm" 
                       minlength="8">
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password_confirm']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-warning btn-lg text-dark">
                    <i class="bi bi-save me-2"></i> Mettre à Jour l'Utilisateur
                </button>
                <a href="liste-utilisateurs.php" class="btn btn-outline-secondary btn-lg ms-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>