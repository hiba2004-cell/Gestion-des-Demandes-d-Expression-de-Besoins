<?php
/**
 * AJAX Handler for Material Demand Submission
 * Expression des Besoins System
 */

session_start();
header('Content-Type: application/json');

// Include database configuration
require_once '../config/database.php';
require_once '../includes/functions.php';

// Response helper function
function jsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Vous devez être connecté pour effectuer cette action.');
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Méthode non autorisée.');
}

// Validate required fields
$requiredFields = ['material_id', 'type_besoin_id', 'description', 'urgence'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        jsonResponse(false, "Le champ '$field' est requis.");
    }
}

// Sanitize inputs
$materialId = intval($_POST['material_id']);
$typeBesoinId = intval($_POST['type_besoin_id']);
$description = trim(htmlspecialchars($_POST['description']));
$urgence = $_POST['urgence'];

// Validate urgence value
$validUrgences = ['Faible', 'Moyenne', 'Urgente'];
if (!in_array($urgence, $validUrgences)) {
    jsonResponse(false, 'Niveau d\'urgence invalide.');
}

// Get database connection
$pdo = getConnection();

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if material exists and is available
    $checkStmt = $pdo->prepare("
        SELECT id, titre, quantite_disponible, statut 
        FROM available_material 
        WHERE id = ? AND statut = 'Disponible' AND quantite_disponible > 0
    ");
    $checkStmt->execute([$materialId]);
    $material = $checkStmt->fetch();
    
    if (!$material) {
        $pdo->rollBack();
        jsonResponse(false, 'Ce matériel n\'est plus disponible.');
    }
    
    
    //create besoin
    $dataBesoin = [
        'demandeur_id' => $_SESSION['user_id'],
        'categorie' => $typeBesoinId,
        'description' => $description,
        'priorite' => $urgence,
    ];
    createBesoin($dataBesoin);
    
    // Update material quantity
    $newQuantity = $material['quantite_disponible'] - 1;
    $newStatut = $newQuantity > 0 ? 'Disponible' : 'Réservé';
    
    $updateStmt = $pdo->prepare("
        UPDATE available_material 
        SET quantite_disponible = ?, statut = ? 
        WHERE id = ?
    ");
    $updateStmt->execute([$newQuantity, $newStatut, $materialId]);
    
    // Commit transaction
    $pdo->commit();
    
    jsonResponse(true, 'Votre demande a été soumise avec succès.', [
        'demande_id' => 10,
        'material_title' => $material['titre']
    ]);
    
} catch (PDOException $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error (in production, use proper logging)
    error_log('Demand submission error: ' . $e->getMessage());
    
    jsonResponse(false, 'Une erreur est survenue lors de la soumission de votre demande. ');
}
?>
