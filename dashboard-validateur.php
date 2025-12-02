<?php
require_once './includes/header.php';
// include 'chat_button.php';


// Vérification du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Validateur') {
header("Location: /besoins/index.php");
exit;
}

$error_message = "";

// -------------------------
// FONCTIONS UTILITAIRES
// -------------------------

function getStatusClass(string $statut): string {
return str_replace(' ', '-', $statut);
}

// -------------------------
// FILTRES & RÉCUPÉRATION DES DONNÉES
// -------------------------
$where = [];
$params = [];

// Filtre statut
$selected_statut = $_GET['statut'] ?? '';
if (!empty($selected_statut)) {
$where[] = "d.statut = ?";
$params[] = $selected_statut;
}

// Filtre demandeur
$selected_demandeur = $_GET['demandeur'] ?? '';
if (!empty($selected_demandeur)) {
$where[] = "u.nom LIKE ?";
$params[] = "%" . $selected_demandeur . "%";
}

// Filtre date
$selected_date = $_GET['date'] ?? '';
if (!empty($selected_date)) {
$where[] = "DATE(d.date_creation) = ?";
$params[] = $selected_date;
}

// Requête de base
$query = "
SELECT d.*, t.libelle AS type_besoin, u.nom AS demandeur, u.email AS demandeur_email
FROM demandes d
LEFT JOIN types_besoins t ON d.type_besoin_id = t.id
LEFT JOIN users u ON d.user_id = u.id
LEFT JOIN validation v ON v.demande_id = d.id
WHERE v.demande_id IS NULL AND d.type_besoin_id = :service_id AND d.statut NOT IN ('Traitée')
";

$params['service_id'] = $_SESSION['user_service'];


// Ajouter les filtres éventuels
if ($where) {
$query .= " AND " . implode(" AND ", $where);
}

// Tri par statut prioritaire puis date de création
$query .= "
ORDER BY
CASE d.statut
WHEN 'En attente' THEN 1
WHEN 'En cours de validation' THEN 2
ELSE 3
END,
d.date_creation DESC
";

try {
$pdo = getConnection();
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_demandes = count($demandes);
} catch (PDOException $e) {
$demandes = [];
$total_demandes = 0;
$error_message = "Erreur de base de données : " . $e->getMessage();
}

// Messages de succès / erreur depuis les actions précédentes
$success_message = null;
if(isset($_SESSION['action_result']['error'])){
$error_message .= '<br>' . $_SESSION['action_result']['error'];
} elseif(isset($_SESSION['action_result']['success'])){
$success_message = $_SESSION['action_result']['success'];
}
$_SESSION['action_result'] = null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Espace Validateur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6f9;
    }

    .page-title {
        font-weight: 700;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 18px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Styles basés sur les classes des statuts */
    .En-attente {
        background: #ffeeba;
        color: #856404;
    }

    .En-cours-de-validation {
        background: #cce5ff;
        color: #004085;
    }

    .Validée {
        background: #d4edda;
        color: #155724;
    }

    .Rejetée {
        background: #f8d7da;
        color: #721c24;
    }

    .Traitée {
        background: #e2e3e5;
        color: #41464b;
    }
    </style>
</head>

<body>

    <div class="container py-4">

        <h2 class="page-title mb-4">
            <i class="bi bi-briefcase me-2 text-primary"></i>Espace Validateur
            <span class="badge bg-secondary ms-2"><?php echo $total_demandes; ?></span>
        </h2>

        <?php if (isset($error_message) && $error_message): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php elseif ($success_message): ?>
        <div class="alert alert-<?php echo $success_message['color'];?>" role="alert">
            <?php echo htmlspecialchars($success_message['success']); ?>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-funnel me-2"></i>Filtrer les demandes
            </div>
            <div class="card-body">

                <form method="GET" class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous</option>
                            <option value="En attente"
                                <?php echo ($selected_statut === 'En attente') ? 'selected' : ''; ?>>En attente</option>
                            <option value="En cours de validation"
                                <?php echo ($selected_statut === 'En cours de validation') ? 'selected' : ''; ?>>En
                                cours de validation</option>
                            <option value="Validée" <?php echo ($selected_statut === 'Validée') ? 'selected' : ''; ?>>
                                Validée</option>
                            <option value="Rejetée" <?php echo ($selected_statut === 'Rejetée') ? 'selected' : ''; ?>>
                                Rejetée</option>
                            <option value="Traitée" <?php echo ($selected_statut === 'Traitée') ? 'selected' : ''; ?>>
                                Traitée</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Demandeur</label>
                        <input type="text" name="demandeur" class="form-control" placeholder="Nom du demandeur"
                            value="<?php echo htmlspecialchars($selected_demandeur); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control"
                            value="<?php echo htmlspecialchars($selected_date); ?>">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="dashboard-validateur.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Réinitialiser
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <hr />

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-list-task me-2"></i>
                Demandes de Besoins à Valider (<?php echo $total_demandes; ?>)
            </div>
            <div class="card-body p-0">
                <?php if ($total_demandes > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#ID</th>
                                <th style="width: 25%;">Description</th>
                                <th style="width: 20%;">Demandeur</th>
                                <th style="width: 15%;">Date de Création</th>
                                <th style="width: 15%;">Statut</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($demande['id']); ?></td>
                                <td>
                                    <strong
                                        class="d-block"><?php echo htmlspecialchars($demande['description']); ?></strong>
                                    <small class="text-muted">Type:
                                        <?php echo htmlspecialchars($demande['type_besoin']); ?></small>
                                </td>
                                <td>
                                    <span
                                        class="d-block fw-bold"><?php echo htmlspecialchars($demande['demandeur']); ?></span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($demande['demandeur_email']); ?></small>
                                </td>
                                <td>
                                    <?php 
                                            // Affichage formaté de la date (ex: jj/mm/aaaa)
                                            echo date('d/m/Y', strtotime($demande['date_creation'])); 
                                            ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo getStatusClass($demande['statut']); ?>">
                                        <?php echo htmlspecialchars($demande['statut']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="./pages/action-demande.php?id=<?php echo $demande['id']; ?>&action=valider"
                                            class="btn btn-success" title="Valider la demande">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="./pages/action-demande.php?id=<?php echo $demande['id']; ?>&action=rejeter"
                                            class="btn btn-danger" title="Rejeter la demande">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                        <a href="./pages/action-demande.php?id=<?php echo $demande['id']; ?>&action=send-to-admin"
                                            class="btn btn-primary" title="Envoyer à l'admin">
                                            <i class="bi bi-send-fill"></i>
                                        </a>



                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info m-4 text-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Aucune demande de besoin ne correspond aux critères de filtre.
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>