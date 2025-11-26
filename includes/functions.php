<?php
/**
 * Fonctions utilitaires
 * Système d'Expression du Besoin
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Sécurise les données d'entrée
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valide une adresse email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valide une date
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Formate une date pour l'affichage
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $dateTime = new DateTime($date);
    return $dateTime->format($format);
}

/**
 * Formate une date et heure pour l'affichage
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    $dateTime = new DateTime($datetime);
    return $dateTime->format($format);
}

/**
 * Formate un montant en euros
 */
function formatCurrency($amount) {
    if (empty($amount)) return '';
    return number_format($amount, 2) . ' DH';
}

/**
 * Obtient la classe CSS pour la priorité
 */
function getPriorityClass($priority) {
    switch($priority) {
        case 'critique': return 'danger';
        case 'haute': return 'warning';
        case 'moyenne': return 'info';
        case 'faible': return 'secondary';
        default: return 'secondary';
    }
}

/**
 * Obtient la classe CSS pour le statut
 */
function getStatusClass($status) {
    switch($status) {
        case 'nouveau': return 'primary';
        case 'en_cours': return 'warning';
        case 'termine': return 'success';
        case 'rejete': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Obtient le libellé français pour la priorité
 */
function getPriorityLabel($priority) {
    switch($priority) {
        case 'critique': return 'Critique';
        case 'haute': return 'Haute';
        case 'moyenne': return 'Moyenne';
        case 'faible': return 'Faible';
        default: return $priority;
    }
}

/**
 * Obtient le libellé français pour le statut
 */
function getStatusLabel($status) {
    switch($status) {
        case 'nouveau': return 'Nouveau';
        case 'en_cours': return 'En cours';
        case 'termine': return 'Terminé';
        case 'rejete': return 'Rejeté';
        default: return $status;
    }
}

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige vers une page
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Affiche un message flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupère et efface le message flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Paginer les résultats
 */
function paginate($totalItems, $itemsPerPage = 10, $currentPage = 1) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($totalPages, $currentPage));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Obtient tous les besoins avec filtres
 */
function getBesoins($filters = [], $limit = 10, $offset = 0) {
    $conn = getConnection();
    $where = [];
    $params = [];
    
    // Construction des filtres
    if (!empty($filters['priorite'])) {
        $where[] = "priorite = :priorite";
        $params['priorite'] = $filters['priorite'];
    }
    
    if (!empty($filters['statut'])) {
        $where[] = "statut = :statut";
        $params['statut'] = $filters['statut'];
    }
    
    if (!empty($filters['categorie'])) {
        $where[] = "categorie LIKE :categorie";
        $params['categorie'] = '%' . $filters['categorie'] . '%';
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(titre LIKE :search OR description LIKE :search OR demandeur_nom LIKE :search)";
        $params['search'] = '%' . $filters['search'] . '%';
    }
    
    $whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    
    // Requête pour compter le total
    $countQuery = "SELECT COUNT(*) as total FROM besoins" . $whereClause;
    $stmt = $conn->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Requête pour récupérer les données
    $query = "SELECT * FROM besoins" . $whereClause . " ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $besoins = $stmt->fetchAll();
    
    return [
        'besoins' => $besoins,
        'total' => $total
    ];
}

/**
 * Obtient un besoin par son ID
 */
function getBesoinById($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM besoins WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

/**
 * Crée un nouveau besoin
 */
function createBesoin($data) {
    $conn = getConnection();
    $stmt = $conn->prepare("
        INSERT INTO besoins (titre, description, priorite, categorie, demandeur_nom, demandeur_email, cout_estime, delai_souhaite)
        VALUES (:titre, :description, :priorite, :categorie, :demandeur_nom, :demandeur_email, :cout_estime, :delai_souhaite)
    ");
    
    return $stmt->execute([
        'titre' => $data['titre'],
        'description' => $data['description'],
        'priorite' => $data['priorite'],
        'categorie' => $data['categorie'],
        'demandeur_nom' => $data['demandeur_nom'],
        'demandeur_email' => $data['demandeur_email'],
        'cout_estime' => !empty($data['cout_estime']) ? $data['cout_estime'] : null,
        'delai_souhaite' => !empty($data['delai_souhaite']) ? $data['delai_souhaite'] : null
    ]);
}

/**
 * Met à jour un besoin
 */
function updateBesoin($id, $data) {
    $conn = getConnection();
    $stmt = $conn->prepare("
        UPDATE besoins 
        SET titre = :titre, description = :description, priorite = :priorite, statut = :statut,
            categorie = :categorie, demandeur_nom = :demandeur_nom, demandeur_email = :demandeur_email,
            cout_estime = :cout_estime, delai_souhaite = :delai_souhaite
        WHERE id = :id
    ");
    
    return $stmt->execute([
        'id' => $id,
        'titre' => $data['titre'],
        'description' => $data['description'],
        'priorite' => $data['priorite'],
        'statut' => $data['statut'],
        'categorie' => $data['categorie'],
        'demandeur_nom' => $data['demandeur_nom'],
        'demandeur_email' => $data['demandeur_email'],
        'cout_estime' => !empty($data['cout_estime']) ? $data['cout_estime'] : null,
        'delai_souhaite' => !empty($data['delai_souhaite']) ? $data['delai_souhaite'] : null
    ]);
}

/**
 * Supprime un besoin
 */
function deleteBesoin($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM besoins WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Obtient les statistiques
 */
function getStatistics() {
    $conn = getConnection();
    
    // Total des besoins
    $stmt = $conn->query("SELECT COUNT(*) as total FROM besoins");
    $total = $stmt->fetch()['total'];
    
    // Besoins par statut
    $stmt = $conn->query("SELECT statut, COUNT(*) as count FROM besoins GROUP BY statut");
    $parStatut = $stmt->fetchAll();
    
    // Besoins par priorité
    $stmt = $conn->query("SELECT priorite, COUNT(*) as count FROM besoins GROUP BY priorite");
    $parPriorite = $stmt->fetchAll();
    
    // Coût total estimé
    $stmt = $conn->query("SELECT SUM(cout_estime) as total FROM besoins WHERE cout_estime IS NOT NULL");
    $coutTotal = $stmt->fetch()['total'] ?? 0;
    
    return [
        'total' => $total,
        'par_statut' => $parStatut,
        'par_priorite' => $parPriorite,
        'cout_total' => $coutTotal
    ];
}
?>