<?php 
//require_once  "/../config/database.php";
//require_once "/includes/header-dashboard.php";
$page_title = "Espace Validateur";
require_once '/config/auth.php';

if (!$auth->hasRole('Validateur')) {
    header("Location: " . redirectByRole($user['role']));
    exit();
}

$validator_id = $_SESSION['user_id'];

$query = "
SELECT v.id as validation_id, b.*, u.nom as demandeur_nom, v.statut
FROM validation v
JOIN besoins b ON b.id = v.demande_id
JOIN users u ON u.id = v.demandeur_id
WHERE v.superieur_id = ?
";

$filters = [];
$params = [$validator_id];

// Filtre statut
if (!empty($_GET['statut'])) {
    $query .= " AND v.statut = ?";
    $params[] = $_GET['statut'];
}

// Filtre demandeur
if (!empty($_GET['demandeur'])) {
    $query .= " AND u.nom LIKE ?";
    $params[] = "%".$_GET['demandeur']."%";
}

// Filtre date
if (!empty($_GET['date'])) {
    $query .= " AND DATE(b.date_creation) = ?";
    $params[] = $_GET['date'];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();

?>

<h3>Demandes à valider</h3>

<form method="GET" class="mb-3">
    <input type="text" name="demandeur" placeholder="Demandeur">
    <select name="statut">
        <option value="">Statut</option>
        <option value="en_attente">En attente</option>
        <option value="valide">Validé</option>
        <option value="rejete">Rejeté</option>
    </select>

    <input type="date" name="date">

    <button type="submit">Filtrer</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Besoin</th>
        <th>Demandeur</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>

    <?php foreach($results as $row): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["titre"] ?></td>
        <td><?= $row["demandeur_nom"] ?></td>
        <td><?= $row["statut"] ?></td>
        <td>
            <a href="detail-validation.php?id=<?= $row["validation_id"] ?>">Ouvrir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once "../includes/footer-dashboard.php"; ?>