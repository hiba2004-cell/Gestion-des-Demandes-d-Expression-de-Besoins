<?php
$page_title = "Créer une demande";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getConnection();

$errors = [];
$success = "";

// Récupération des types de besoins depuis la table types_besoins
try {
    $stmtTypes = $conn->query("SELECT id, libelle FROM types_besoins ORDER BY libelle ASC");
    $types_besoins = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $type_besoin = isset($_POST['type_besoin']) ? (int)$_POST['type_besoin'] : 0;
    $description = $_POST['description'] ?? '';
    $urgence = $_POST['urgence'] ?? '';

    if ($type_besoin <= 0) $errors[] = "Le type de besoin est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($urgence)) $errors[] = "L'urgence est obligatoire.";

    // Vérifier que l'id existe dans la table types_besoins
    if ($type_besoin > 0) {
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM types_besoins WHERE id = :id");
        $stmtCheck->bindParam(':id', $type_besoin, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() == 0) {
            $errors[] = "Le type de besoin sélectionné n'existe pas.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO demandes (user_id, type_besoin_id, description, urgence, statut, date_creation)
                VALUES (:user_id, :type_besoin, :description, :urgence, 'En attente', NOW())
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_besoin', $type_besoin, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':urgence', $urgence, PDO::PARAM_STR);
            $stmt->execute();

            $demande_id = $conn->lastInsertId();

            // Gestion des fichiers uploadés
            if (!empty($_FILES['fichiers']['name'][0])) {
                $uploadDir = 'uploads/';
                foreach ($_FILES['fichiers']['tmp_name'] as $index => $tmpName) {
                    $filename = basename($_FILES['fichiers']['name'][$index]);
                    $targetFile = $uploadDir . time() . '_' . $filename;
                    if (move_uploaded_file($tmpName, $targetFile)) {
                        $stmtFile = $conn->prepare("
                            INSERT INTO pieces_jointes (demande_id, fichier) 
                            VALUES (:demande_id, :fichier)
                        ");
                        $stmtFile->bindParam(':demande_id', $demande_id, PDO::PARAM_INT);
                        $stmtFile->bindParam(':fichier', $targetFile, PDO::PARAM_STR);
                        $stmtFile->execute();
                    }
                }
            }

            $success = "Demande créée avec succès !";

        } catch (PDOException $e) {
            $errors[] = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4">
    <h2>Créer une nouvelle demande</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="type_besoin" class="form-label">Type de besoin</label>
            <select name="type_besoin" id="type_besoin" class="form-select" required>
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
            <label for="urgence" class="form-label">Urgence</label>
            <select name="urgence" id="urgence" class="form-select" required>
                <option value="">-- Sélectionnez --</option>
                <option value="Faible">Faible</option>
                <option value="Moyenne">Moyenne</option>
                <option value="Haute">Haute</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fichiers" class="form-label">Fichier(s) (optionnel)</label>
            <input type="file" name="fichiers[]" id="fichiers" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Créer la demande</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>