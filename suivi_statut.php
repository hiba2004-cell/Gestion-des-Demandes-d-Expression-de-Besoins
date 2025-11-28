<?php

$page_title = "Suivi des statuts";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getConnection();

try {
    $stmt = $conn->prepare("
        SELECT 
            description,
            type_besoin_id,
            urgence,
            date_creation,
            statut
        FROM demandes
        WHERE user_id = :user_id
        ORDER BY date_creation DESC
    ");
    
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<div class="container mt-4">
    <h2>Suivi des statuts de vos demandes</h2>

    <?php if (count($stats) > 0): ?>
    <ul class="list-group mt-3">
        <?php foreach ($stats as $s): ?>
        <li class="list-group-item">
            <strong>Description :</strong> <?= htmlspecialchars($s['description']) ?><br>
            <strong>Type de besoin :</strong> <?= htmlspecialchars($s['type_besoin_id']) ?><br>
            <strong>Urgence :</strong> <?= htmlspecialchars($s['urgence']) ?><br>
            <strong>Date de création :</strong> <?= htmlspecialchars($s['date_creation']) ?><br>
            <strong>Statut :</strong>
            <span class="badge bg-info"><?= htmlspecialchars($s['statut']) ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p>Aucune demande enregistrée pour le moment.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>