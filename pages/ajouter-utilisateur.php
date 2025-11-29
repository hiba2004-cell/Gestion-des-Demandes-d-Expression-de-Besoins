<?php
$page_title = "Ajouter un Nouvel Utilisateur";
include '../includes/header.php';


// J'assume que la fonction sanitize() et redirect() existent déjà dans un fichier inclus.
// J'assume que la fonction setFlashMessage() existe pour afficher les notifications.

// Liste des rôles disponibles pour le formulaire
$roles = [
    'Demandeur' => 'Membre standard',
    'Validateur' => 'Validateur de contenu',
    'Administrateur' => 'Administrateur (Accès complet)',
];

// Initialisation des variables pour le formulaire
$errors = [];
$formData = [
    'nom' => '',
    'email' => '',
    'role' => 'membre', // Rôle par défaut
    'password' => '',
    'password_confirm' => ''
];


// --- Traitement du Formulaire POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Assainissement des données et mise à jour de $formData
    $formData['nom'] = sanitize($_POST['nom'] ?? '');
    $formData['email'] = sanitize($_POST['email'] ?? '');
    $formData['role'] = sanitize($_POST['role'] ?? '');
    $formData['password'] = $_POST['password'] ?? ''; // Ne pas sanitize/échapper le mot de passe avant le hash
    $formData['password_confirm'] = $_POST['password_confirm'] ?? '';

    // 2. Validation
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
    if (empty($formData['password'])) {
        $errors['password'] = "Le mot de passe est requis.";
    } elseif (strlen($formData['password']) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($formData['password'] !== $formData['password_confirm']) {
        $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
    }

    // 3. Si aucune erreur, procéder à l'enregistrement
    if (empty($errors)) {
        try {
            // Créer le tableau de données propre à la fonction BDD
            $userToCreate = [
                'nom' => $formData['nom'],
                'email' => $formData['email'],
                'role' => $formData['role'],
                'password' => $formData['password'], // La fonction createUser() doit le hasher
            ];

            if (createUser($userToCreate)) {
                setFlashMessage('success', 'L\'utilisateur ' . htmlspecialchars($formData['nom']) . ' a été ajouté avec succès.');
                redirect('liste-utilisateurs.php');
            } else {
                setFlashMessage('error', 'Erreur inconnue lors de l\'ajout de l\'utilisateur.');
            }
        } catch (Exception $e) {
            // Gérer les erreurs spécifiques de BDD (ex: email déjà existant)
            $errors['general'] = 'Erreur BDD : ' . $e->getMessage();
            setFlashMessage('error', 'Échec de l\'enregistrement : ' . $e->getMessage());
        }
    } else {
        setFlashMessage('warning', 'Veuillez corriger les erreurs dans le formulaire.');
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-plus me-2 text-success"></i>
        <?php echo $page_title; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="liste-utilisateurs.php" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Retour à la Liste
        </a>
    </div>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($errors['general']); ?>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">Informations de l'Utilisateur</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="ajouter-utilisateur.php" class="row g-3">
            
            <div class="col-md-6">
                <label for="nom" class="form-label">Nom Complet <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" 
                       id="nom" 
                       name="nom" 
                       value="<?php echo htmlspecialchars($formData['nom']); ?>"
                       required
                       placeholder="Ex: Jean Dupond">
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
                       required
                       placeholder="utilisateur@domaine.com">
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
                </div>

            <div class="col-md-6">
                <label for="password" class="form-label">Mot de Passe <span class="text-danger">*</span></label>
                <input type="password" 
                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                       id="password" 
                       name="password" 
                       required
                       minlength="8"
                       placeholder="Minimum 8 caractères">
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="password_confirm" class="form-label">Confirmer le Mot de Passe <span class="text-danger">*</span></label>
                <input type="password" 
                       class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" 
                       id="password_confirm" 
                       name="password_confirm" 
                       required
                       minlength="8">
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password_confirm']; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-2"></i> Enregistrer l'Utilisateur
                </button>
                <a href="liste-utilisateurs.php" class="btn btn-outline-secondary btn-lg ms-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>