<?php
//session_start();
$page_title = "Dashboard Demandeur";
include 'includes/header.php';
require_once 'config/database.php';

// Vérifier la connexion
//if ($conn === null) {
  //  die("Erreur : la connexion à la base de données n'a pas été établie.");
//}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

$user_id = $_SESSION['user_id'];
?>

<div class="container mt-4">
    <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Demandeur'); ?> !</h1>
    <h3>Gestion de vos demandes</h3>

    <div class="mb-4">
        <!-- Bouton pour créer une nouvelle demande -->
        <a href="pages/ajouter-besoin.php" class="btn btn-primary">Créer une demande</a>
        <!-- Bouton pour consulter l'historique -->
        <a href="historique_demande.php" class="btn btn-secondary">Historique des demandes</a>
        <!-- Bouton pour suivre les statuts -->
        <a href="suivi_statut.php" class="btn btn-info">Suivi des statuts</a>
        <!-- Bouton pour modifier une demande (sera actif seulement pour les demandes non validées) -->
        <a href="modifier_demande.php" class="btn btn-warning">Modifier une demande</a>
    </div>

    <h4>Vos demandes récentes</h4>

    <?php
    // Récupérer les demandes de l'utilisateur
    try {
        $stmt = $conn->prepare("SELECT * FROM besoins WHERE user_id = :user_id ORDER BY id DESC LIMIT 2");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des demandes : " . $e->getMessage());
    }
    ?>

    <?php if (count($besoins) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Description</th>
                <th>Urgence</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($besoins as $besoin): ?>
            <tr>
                <td><?php echo $besoin['id']; ?></td>
                <td><?php echo htmlspecialchars($besoin['type']); ?></td>
                <td><?php echo htmlspecialchars($besoin['description']); ?></td>
                <td><?php echo htmlspecialchars($besoin['urgence']); ?></td>
                <td><?php echo htmlspecialchars($besoin['statut']); ?></td>
                <td>
                    <?php if ($besoin['statut'] !== 'Validée' && $besoin['statut'] !== 'Rejetée'): ?>
                    <a href="modifier_demande.php?id=<?php echo $besoin['id']; ?>"
                        class="btn btn-sm btn-warning">Modifier</a>
                    <?php else: ?>
                    <span class="text-muted">Non modifiable</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Vous n'avez encore créé aucune demande.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>