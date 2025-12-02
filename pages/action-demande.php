<?php
ob_start();
require_once '../config/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';


// ----------------------------------------------------
// 1. VÉRIFICATION DU RÔLE ET DES PARAMÈTRES DE BASE
// ----------------------------------------------------

// Vérification du rôle : Seul le 'Validateur' est autorisé
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Validateur') {
    header("Location: /besoins/index.php"); 
    exit;
}

// Paramètres de l'URL
$demande_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// Vérification de base des paramètres (doivent être présents pour afficher le formulaire)
if (!$demande_id || !in_array($action, ['valider', 'rejeter','send-to-admin'])) {
    header("Location: /besoins/dashboard-validateur.php"); 
    exit;
}

if($action==='send-to-admin'){
    $username = $_SESSION['user_nom'];
    createNotification($demande_id,1,1,"validateur {$username} a envoyé la demande #{$demande_id} à l'administrateur pour révision.",$_SESSION['user_id']);
    $_SESSION['action_result'] = [
        'success' => "La demande #{$demande_id} a été envoyée à l'administrateur avec succès.",
        "color"   => 'success'
    ];
    // header("Location: /besoins/dashboard-validateur.php");
    // exit;
}


$validator_id = $_SESSION['user_id'];
$libelle_action = ($action === 'valider') ? 'Valider' : 'Rejeter';

try {
    $pdo = getConnection();
    
    // Récupérer les données de la demande pour l'affichage/vérification
    $query_demande = "
        SELECT 
            d.id, d.description, d.statut, d.date_creation, 
            t.libelle AS type_besoin, 
            u.nom AS demandeur
        FROM demandes d
        LEFT JOIN types_besoins t ON d.type_besoin_id = t.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE d.id = ?
    ";
    $stmt_demande = $pdo->prepare($query_demande);
    $stmt_demande->execute([$demande_id]);
    $demande_data = $stmt_demande->fetch(PDO::FETCH_ASSOC);

    if (!$demande_data) {
        throw new Exception("Demande de besoin introuvable.");
    }
    
    // Vérification de l'état avant toute action
    if (in_array($demande_data['statut'], ['Validée', 'Rejetée', 'Traitée'])) {
        // Si la demande est déjà finalisée, on redirige immédiatement
        $_SESSION['action_result'] = [
            'error' => "Cette demande a déjà été finalisée (Statut: {$demande_data['statut']}) et ne peut être modifiée."
        ];
        header("Location: /besoins/dashboard-validateur.php");
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['action_result'] = ['error' => "Erreur de base de données lors du chargement: " . $e->getMessage()];
    header("Location: /besoins/dashboard-validateur.php");
    exit;
} catch (Exception $e) {
    $_SESSION['action_result'] = ['error' => $e->getMessage()];
    header("Location: /besoins/dashboard-validateur.php");
    exit;
}

// ----------------------------------------------------
// 2. GESTION DE LA SOUMISSION DU FORMULAIRE (POST)
// ----------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_action'])) {

    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception("Token de sécurité invalide.", 1);
    }


    $raison = filter_input(INPUT_POST, 'raison', FILTER_SANITIZE_STRING);
    $final_error_message = "";
    
    // Pour le REJET, la raison est OBLIGATOIRE. Pour la VALIDATION, elle est optionnelle.
    if ($action === 'rejeter' && empty(trim($raison))) {
        // Simplement retourner au formulaire avec un message d'erreur
        $final_error_message = "La raison du rejet est obligatoire.";
    }

    if (empty($final_error_message)) {
        $_SESSION['action_result'] = processBesoinAction($demande_id, $validator_id, $raison, $action);
        // Redirection finale après tout traitement réussi ou échoué
        header("Location: /besoins/dashboard-validateur.php");
        exit;
    }
}

// Générer le token CSRF
$csrfToken = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?php echo $libelle_action; ?> la Demande #<?php echo $demande_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">

        <h2 class="mb-4">
            <i class="bi bi-shield-fill-check me-2 
                <?php echo ($action === 'valider') ? 'text-success' : 'text-danger'; ?>"></i>
            Confirmation : <?php echo $libelle_action; ?> la Demande #<?php echo $demande_id; ?>
        </h2>

        <hr>

        <?php 
        // Affichage des erreurs de validation POST (si la raison manque)
        if (isset($final_error_message) && !empty($final_error_message)): ?>
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?php echo htmlspecialchars($final_error_message); ?>
        </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Détails de la Demande</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Demandeur :</strong> <?php echo htmlspecialchars($demande_data['demandeur']); ?></p>
                        <p><strong>Type de Besoin :</strong>
                            <?php echo htmlspecialchars($demande_data['type_besoin']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date de Création :</strong>
                            <?php echo date('d/m/Y', strtotime($demande_data['date_creation'])); ?></p>
                        <p><strong>Statut Actuel :</strong> <span
                                class="badge bg-warning text-dark"><?php echo htmlspecialchars($demande_data['statut']); ?></span>
                        </p>
                    </div>
                </div>
                <hr>
                <p><strong>Description :</strong></p>
                <blockquote class="blockquote bg-light p-3 rounded">
                    <?php echo nl2br(htmlspecialchars($demande_data['description'])); ?>
                </blockquote>
            </div>
        </div>

        <div class="card shadow-lg 
            <?php echo ($action === 'valider') ? 'border-success' : 'border-danger'; ?> mb-4">
            <div class="card-header text-white 
                <?php echo ($action === 'valider') ? 'bg-success' : 'bg-danger'; ?>">
                Action Requise
            </div>
            <div class="card-body">
                <form method="POST"
                    action="action-demande.php?id=<?php echo $demande_id; ?>&action=<?php echo $action; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="mb-3">
                        <label for="raison" class="form-label fw-bold">
                            Raison de la <?php echo $libelle_action; ?>
                            <?php if ($action === 'rejeter'): ?>
                            <span class="text-danger">* Obligatoire pour le rejet</span>
                            <?php else: ?>
                            <span class="text-muted">(Facultatif pour la validation)</span>
                            <?php endif; ?>
                        </label>
                        <textarea class="form-control" id="raison" name="raison" rows="4"
                            placeholder="Veuillez fournir un commentaire concis pour justifier cette action."><?php echo htmlspecialchars($raison ?? ''); ?></textarea>
                    </div>

                    <button type="submit" name="submit_action" class="btn btn-lg 
                        <?php echo ($action === 'valider') ? 'btn-success' : 'btn-danger'; ?>">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Confirmer <?php echo $libelle_action; ?>
                    </button>

                    <a href="dashboard-validateur.php" class="btn btn-lg btn-outline-secondary ms-2">
                        <i class="bi bi-x-lg me-2"></i>Annuler
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>