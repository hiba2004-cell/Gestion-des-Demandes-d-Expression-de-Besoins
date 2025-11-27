<?php
session_start();
$page_title = "Historique des demandes";
include 'includes/header.php';
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à l'historique.");
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM besoins WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<div class="container mt-4">
    <h2>Historique de vos demandes</h2>

    <?php if (count($besoins) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Description</th>
                <th>Urgence</th>
                <th>Statut</th>
                <th>Fichier</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($besoins as $b): ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><?php echo htmlspecialchars($b['type']); ?></td>
                <td><?php echo htmlspecialchars($b['description']); ?></td>
                <td><?php echo htmlspecialchars($b['urgence']); ?></td>
                <td><?php echo htmlspecialchars($b['statut']); ?></td>
                <td>
                    <?php if ($b['fichier']): ?>
                    <a href="<?php echo $b['fichier']; ?>" target="_blank">Voir</a>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Aucune demande pour le moment.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>