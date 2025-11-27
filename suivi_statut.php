<?php

$page_title = "Suivi des statuts";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $user_id->prepare("SELECT demandes, COUNT(*) as total FROM expression_besoin WHERE user_id = :user_id GROUP BY statut");
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
    <ul class="list-group">
        <?php foreach ($stats as $s): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo htmlspecialchars($s['statut']); ?>
            <span class="badge bg-primary rounded-pill"><?php echo $s['total']; ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p>Aucune demande enregistrée pour le moment.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>