<?php

$page_title = "Modifier une demande";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de la demande manquant.");
}

// Vérifier que la demande appartient à l'utilisateur et n'est pas validée/rejetée
$stmt = $user_id->prepare("SELECT * FROM besoins WHERE id = :id AND user_id = :user_id AND statut NOT IN ('Validée','Rejetée')");
$stmt->execute([':id' => $id, ':user_id' => $user_id]);
$besoin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$besoin) {
    die("Cette demande ne peut pas être modifiée.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $description = $_POST['description'] ?? '';
    $urgence = $_POST['urgence'] ?? '';

    try {
        $stmt = $conn->prepare("UPDATE besoins SET type=:type, description=:description, urgence=:urgence WHERE id=:id AND user_id=:user_id");
        $stmt->execute([
            ':type' => $type,
            ':description' => $description,
            ':urgence' => $urgence,
            ':id' => $id,
            ':user_id' => $user_id
        ]);
        echo "<div class='alert alert-success'>Demande modifiée avec succès !</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-4">
    <h2>Modifier votre demande</h2>
    <form method="post">
        <div class="mb-3">
            <label>Type de besoin</label>
            <select name="type" class="form-control" required>
                <option value="Matériel" <?php if($besoin['type']=='Matériel') echo 'selected'; ?>>Matériel</option>
                <option value="Service" <?php if($besoin['type']=='Service') echo 'selected'; ?>>Service</option>
                <option value="Logiciel" <?php if($besoin['type']=='Logiciel') echo 'selected'; ?>>Logiciel</option>
                <option value="Autre" <?php if($besoin['type']=='Autre') echo 'selected'; ?>>Autre</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"
                required><?php echo htmlspecialchars($besoin['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Urgence</label>
            <select name="urgence" class="form-control" required>
                <option value="Faible" <?php if($besoin['urgence']=='Faible') echo 'selected'; ?>>Faible</option>
                <option value="Moyenne" <?php if($besoin['urgence']=='Moyenne') echo 'selected'; ?>>Moyenne</option>
                <option value="Urgente" <?php if($besoin['urgence']=='Urgente') echo 'selected'; ?>>Urgente</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>