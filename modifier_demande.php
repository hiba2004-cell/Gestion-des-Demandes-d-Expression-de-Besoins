<?php
$page_title = "Modifier une demande";
include 'includes/header.php';
require_once 'config/database.php';

$conn = getConnection();
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de la demande manquant.");
}

// Récupérer les types de besoins pour le select
$stmtTypes = $conn->query("SELECT id, libelle FROM types_besoins ORDER BY libelle ASC");
$types_besoins = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

// Vérifier que la demande appartient à l'utilisateur et n'est pas validée/rejetée
$stmt = $conn->prepare("SELECT * FROM demandes WHERE id = :id AND user_id = :user_id AND statut NOT IN ('Validée','Rejetée')");
$stmt->execute([':id' => $id, ':user_id' => $user_id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    die("Cette demande ne peut pas être modifiée.");
}

// Récupérer les fichiers existants pour cette demande
$stmtFiles = $conn->prepare("SELECT * FROM pieces_jointes WHERE demande_id = :demande_id");
$stmtFiles->execute([':demande_id' => $id]);
$fichiers_existants = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_besoin = isset($_POST['type_besoin']) ? (int)$_POST['type_besoin'] : 0;
    $description = $_POST['description'] ?? '';
    $urgence = $_POST['urgence'] ?? '';

    $errors = [];
    if ($type_besoin <= 0) $errors[] = "Le type de besoin est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($urgence)) $errors[] = "L'urgence est obligatoire.";

    if (empty($errors)) {
        try {
            // Mise à jour de la demande
            $stmt = $conn->prepare("
                UPDATE demandes 
                SET type_besoin_id=:type_besoin, description=:description, urgence=:urgence 
                WHERE id=:id AND user_id=:user_id
            ");
            $stmt->execute([
                ':type_besoin' => $type_besoin,
                ':description' => $description,
                ':urgence' => $urgence,
                ':id' => $id,
                ':user_id' => $user_id
            ]);

            // Gestion des fichiers uploadés
            if (!empty($_FILES['fichiers']['name'][0])) {
                $uploadDir = 'uploads/'; // créer ce dossier s'il n'existe pas
                foreach ($_FILES['fichiers']['tmp_name'] as $index => $tmpName) {
                    $filename = basename($_FILES['fichiers']['name'][$index]);
                    $targetFile = $uploadDir . time() . '_' . $filename;
                    if (move_uploaded_file($tmpName, $targetFile)) {
                        $stmtFile = $conn->prepare("INSERT INTO pieces_jointes (demande_id, fichier) VALUES (:demande_id, :fichier)");
                        $stmtFile->execute([
                            ':demande_id' => $id,
                            ':fichier' => $targetFile
                        ]);
                    }
                }
            }

            echo "<div class='alert alert-success'>Demande modifiée avec succès !</div>";

            // Recharger les données
            $stmt = $conn->prepare("SELECT * FROM demandes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $demande = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmtFiles = $conn->prepare("SELECT * FROM pieces_jointes WHERE demande_id = :demande_id");
            $stmtFiles->execute([':demande_id' => $id]);
            $fichiers_existants = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>";
        foreach ($errors as $err) echo "<p>$err</p>";
        echo "</div>";
    }
}
?>

<div class="container mt-4">
    <h2>Modifier votre demande</h2>
    <a href="dashboard-demandeur.php" class="btn btn-secondary mb-3">← Retour à mes demandes</a>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="type_besoin" class="form-label">Type de besoin</label>
            <select name="type_besoin" id="type_besoin" class="form-select" required>
                <option value="">-- Sélectionnez un type --</option>
                <?php foreach ($types_besoins as $type): ?>
                <option value="<?= $type['id'] ?>" <?= $type['id'] == $demande['type_besoin_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['libelle']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4"
                required><?= htmlspecialchars($demande['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Urgence</label>
            <select name="urgence" class="form-select" required>
                <option value="">-- Sélectionnez --</option>
                <option value="Faible" <?= $demande['urgence']=='Faible' ? 'selected' : '' ?>>Faible</option>
                <option value="Moyenne" <?= $demande['urgence']=='Moyenne' ? 'selected' : '' ?>>Moyenne</option>
                <option value="Haute" <?= $demande['urgence']=='Haute' ? 'selected' : '' ?>>Haute</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Fichiers existants</label>
            <ul>
                <?php if ($fichiers_existants): ?>
                <?php foreach ($fichiers_existants as $f): ?>
                <li><a href="<?= htmlspecialchars($f['fichier']) ?>" target="_blank">Voir</a></li>
                <?php endforeach; ?>
                <?php else: ?>
                <li>Aucun fichier joint</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="mb-3">
            <label>Ajouter de nouveaux fichiers (optionnel)</label>
            <input type="file" name="fichiers[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>