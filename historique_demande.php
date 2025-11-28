<?php
$page_title = "Historique des demandes";
include 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getConnection();

try {
    // Requête avec LEFT JOIN pour récupérer les fichiers liés
    $stmt = $conn->prepare("
        SELECT 
            d.id,
            d.type_besoin_id,
            d.description,
            d.urgence,
            CASE 
                WHEN v.demande_id IS NULL THEN d.statut
                WHEN v.demande_id IS NOT NULL THEN v.statut_validation
            END AS statut,
            pj.chemin_fichier as fichiers
        FROM demandes d
        LEFT JOIN pieces_jointes pj ON pj.demande_id = d.id
        LEFT JOIN validation v ON v.demande_id = d.id 
        WHERE d.user_id = :user_id
        GROUP BY d.id
        ORDER BY d.id DESC
    ");
    
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
                <th>Fichier(s)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($besoins as $b): ?>
            <tr>
                <td><?= $b['id']; ?></td>
                <td><?= htmlspecialchars($b['type_besoin_id']); ?></td>
                <td><?= htmlspecialchars($b['description']); ?></td>
                <td><?= htmlspecialchars($b['urgence']); ?></td>
                <td><?= htmlspecialchars($b['statut']); ?></td>
                <td>
                    <?php if (!empty($b['fichiers'])): ?>
                    <?php foreach (explode('||', $b['fichiers']) as $file): ?>
                    <a href="<?= htmlspecialchars($file); ?>" target="_blank">Voir</a><br>
                    <?php endforeach; ?>
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