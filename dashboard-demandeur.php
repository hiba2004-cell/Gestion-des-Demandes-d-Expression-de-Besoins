<?php

$page_title = "Dashboard Demandeur";
include 'includes/header.php';
require_once 'config/database.php';

$conn = getConnection();
$user_id = $_SESSION['user_id'];
?>

<div class="container mt-4">
    <h1><?php echo getSetence('greeting'); ?>, <?php echo htmlspecialchars($_SESSION['user_nom']); ?> </h1>
    <h3><?php echo getSetence('gestion_demande'); ?></h3>

    <div class="mb-4">
        <a href="create_demande.php" class="btn btn-primary me-2">
            <i class="bi bi-plus-circle me-1"></i> <?php echo getSetence('create_request'); ?>
        </a>
        <a href="historique_demande.php" class="btn btn-secondary me-2">
            <i class="bi bi-clock-history me-1"></i> <?php echo getSetence('historique_demande'); ?>
        </a>
        <a href="suivi_statut.php" class="btn btn-info me-2">
            <i class="bi bi-eye me-1"></i> <?php echo getSetence('suivi_status'); ?>
        </a>

    </div>

    <h4><?php echo getSetence('recent_request'); ?></h4>

    <?php
    try {
        $stmt = $conn->prepare("
            SELECT d.*, t.libelle AS type_besoin 
            FROM demandes d
            JOIN types_besoins t ON d.type_besoin_id = t.id
            WHERE d.user_id = :user_id 
            ORDER BY d.date_creation DESC 
            LIMIT 10
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des demandes : " . $e->getMessage());
    }
    ?>

    <?php if (count($besoins) > 0): ?>
    <div class="row">
        <?php foreach ($besoins as $besoin): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    <?php echo getSetence('demande'); ?> #<?php echo $besoin['id']; ?> - <?= htmlspecialchars($besoin['type_besoin']) ?>
                </div>
                <div class="card-body">
                    <p><strong>Description :</strong> <?= htmlspecialchars($besoin['description']) ?></p>
                    <p><strong>Urgence :</strong> <?= htmlspecialchars($besoin['urgence']) ?></p>
                    <p><strong>Statut :</strong> <?= htmlspecialchars($besoin['statut']) ?></p>
                    <?php
                        $stmtFiles = $conn->prepare("SELECT * FROM pieces_jointes WHERE demande_id = :demande_id");
                        $stmtFiles->execute([':demande_id' => $besoin['id']]);
                        $fichiers = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <p><strong><?php echo getSetence('Fichiers'); ?> :</strong>
                        <?php if ($fichiers): ?>
                    <ul>
                        <?php foreach ($fichiers as $f): ?>
                        <li><a href="<?= htmlspecialchars($f['fichier']) ?>" target="_blank">Voir</a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    Aucun fichier
                    <?php endif; ?>
                    </p>
                </div>
                <div class="card-footer text-end">
                    <?php if (!in_array($besoin['statut'], ['Validée','Rejetée','Traitée'])): ?>
                    <a href="modifier_demande.php?id=<?= $besoin['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> <?php echo getSetence('modify'); ?>
                    </a>
                    <?php else: ?>
                    <span class="text-muted">Non modifiable</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p>Vous n'avez encore créé aucune demande.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>