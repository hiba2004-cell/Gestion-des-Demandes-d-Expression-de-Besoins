<?php
$page_title = "Mon Profil";
require_once '/config/auth.php';
require_once '../includes/functions.php';
require_once '../includes/header-dashboard.php';

// Vérification authentification
$auth = requireAuth();
$user = $auth->getCurrentUser();

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $poste = trim($_POST['poste'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $errors = [];

    // Validation
    if (!$nom || !$prenom || !$email) {
        $errors[] = "Nom, prénom et email sont obligatoires.";
    }
    if ($password && $password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        try {
            $conn = getConnection();

            // Mise à jour de la base
            $sql = "UPDATE users SET nom=:nom, prenom=:prenom, email=:email, poste=:poste, service=:service";
            if ($password) {
                $sql .= ", motdepasse=:motdepasse";
            }
            $sql .= " WHERE id=:id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':poste', $poste);
            $stmt->bindParam(':service', $service);
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':motdepasse', $hash);
            }
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();

            setFlashMessage('success', 'Profil mis à jour avec succès.');
            header("Location: profil.php");
            exit();
        } catch (Exception $e) {
            $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>

<div class="container my-4">
    <h1 class="mb-4"><i class="bi bi-person-circle me-2"></i>Mon Profil</h1>

    <?php displayFlashMessage(); ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
            <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom"
                value="<?php echo htmlspecialchars($user['nom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom"
                value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="poste" class="form-label">Poste</label>