<?php
require_once __DIR__ . 'config/auth.php';

// Vérification du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Validateur') {
    header("Location: /besoins/index.php");
    exit;
}

// -------------------------
// FILTRES
// -------------------------
$where = [];
$params = [];

// Filtre statut
if (!empty($_GET['statut'])) {
    $where[] = "d.statut = ?";
    $params[] = $_GET['statut'];
}

// Filtre demandeur
if (!empty($_GET['demandeur'])) {
    $where[] = "u.nom LIKE ?";
    $params[] = "%" . $_GET['demandeur'] . "%";
}

// Filtre date
if (!empty($_GET['date'])) {
    $where[] = "DATE(d.date_creation) = ?";
    $params[] = $_GET['date'];
}

$query = "
    SELECT d.*, t.libelle AS type_besoin, u.nom AS demandeur
    FROM demandes d
    LEFT JOIN types_besoins t ON d.type_besoin_id = t.id
    LEFT JOIN users u ON d.user_id = u.id
";

if ($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY d.date_creation DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$demandes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Espace Validateur</title>

    <!-- Bootstrap + Icons -->
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
    }

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
            <i class="bi bi-briefcase me-2"></i>Espace Validateur
        </h2>


        <!-- FILTRAGE -->
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
                            <option>En attente</option>
                            <option>En cours de validation</option>
                            <option>Validée</option>
                            <option>Rejetée</option>
                            <option>Traitée</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Demandeur</label>
                        <input type="text" name="demandeur" class="form-control" placeholder="Nom du demandeur">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100">