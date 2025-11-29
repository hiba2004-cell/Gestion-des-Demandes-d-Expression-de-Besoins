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



function savePieceJointe(int $demandId, string $filePath) {
    $db = getConnection();
    
    // Assurez-vous d'utiliser une requête préparée pour éviter les injections SQL
    $sql = "INSERT INTO pieces_jointes (demande_id, chemin_fichier) VALUES (:demande_id, :chemin_fichier)";
    
    try {
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'demande_id' => $demandId,
            'chemin_fichier' => $filePath
        ]);
    } catch (Exception $e) {
        // Enregistrement d'erreur ou gestion de l'exception
        error_log("Erreur lors de l'insertion de la pièce jointe: " . $e->getMessage());
        return false;
    }
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
        case 'Urgente': 
        case 'Rejetée': 
            return 'danger';
        case 'Validée': return 'success';
        case 'Moyenne': return 'warning';
        case 'Faible': return 'info';
        case 'faible': return 'secondary';
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

function getUsers(array $filters, int $limit, int $offset): array {

    $conn = getConnection();
    // Récupération de tous les utilisateurs
    $stmt = $conn->query("SELECT * FROM users");
    $allUsers = $stmt->fetchAll();

    $filteredUsers = array_filter($allUsers, function($user) use ($filters) {
        $matchSearch = empty($filters['search']) || 
                       stripos($user['nom'], $filters['search']) !== false || 
                       stripos($user['email'], $filters['search']) !== false;
        $matchRole = empty($filters['role']) || $user['role'] === $filters['role'];
        $matchService = empty($filters['service_id']) || $user['service_id'] == $filters['service_id'];
        return $matchSearch && $matchRole && $matchService;
    });

    $total = count($filteredUsers);
    $paginatedUsers = array_slice($filteredUsers, $offset, $limit);

    return ['utilisateurs' => $paginatedUsers, 'total' => $total];
}


function updateUser(int $id, array $userData): bool {
    $pdo = getConnection();

    // 1. Vérifier que l'email est unique (sauf pour l'utilisateur actuel)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
    $stmt->execute([
        ':email' => $userData['email'],
        ':id'    => $id
    ]);
    $count = (int) $stmt->fetchColumn();
    if ($count > 0) {
        throw new Exception("Cet email est déjà utilisé par un autre utilisateur.");
    }

    // 2. Préparer les champs à mettre à jour
    $params = [
        ':nom'   => $userData['nom'],
        ':email' => $userData['email'],
        ':role'  => $userData['role'],
        ':id'    => $id
    ];

    // Si le mot de passe est fourni, le hacher
    $sqlPassword = "";
    if (!empty($userData['password'])) {
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new Exception("Erreur lors du hashage du mot de passe.");
        }
        $sqlPassword = ", password = :password";
        $params[':password'] = $passwordHash;
    }

    // 3. Exécuter la mise à jour SQL
    $sql = "UPDATE users SET nom = :nom, email = :email, role = :role $sqlPassword WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return true;
}



/**
 * Obtient tous les besoins avec filtres
 */
function getBesoins($filters = [], $limit = 10, $offset = 0) {
    $conn = getConnection();
    $where = [];
    $params = [];
    
    // PRIORITE
    if (!empty($filters['priorite'])) {
        $where[] = "d.urgence = :priorite";
        $params['priorite'] = $filters['priorite'];
    }

    // STATUT (need to filter using CASE expression)
    if (!empty($filters['statut'])) {
        $where[] = "(
            CASE 
                WHEN v.demande_id IS NULL THEN d.statut 
                ELSE v.statut_validation 
            END
        ) = :statut";
        $params['statut'] = $filters['statut'];
    }

    // CATEGORIE (LIKE)
    if (!empty($filters['categorie'])) {
        $where[] = "d.type_besoin_id LIKE :categorie";
        $params['categorie'] = '%' . $filters['categorie'] . '%';
    }

    // SEARCH
    if (!empty($filters['search'])) {
        $where[] = "(d.description LIKE :search 
                    OR u.nom LIKE :search 
                    OR u.email LIKE :search)";
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

    // COUNT QUERY
    $countQuery = "
        SELECT COUNT(*) as total
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        JOIN types_besoins t ON d.type_besoin_id = t.id
        JOIN users u ON d.user_id = u.id
        $whereClause
    ";

    $stmt = $conn->prepare($countQuery);
    $stmt->execute($params);
    $total = (int) $stmt->fetch()['total'];

    // MAIN QUERY
    $query = "
        SELECT 
            d.*,
            t.libelle AS type_besoin, 
            u.nom AS demandeur_nom,
            u.email AS demandeur_email,
            CASE 
                WHEN v.demande_id IS NULL THEN d.statut 
                ELSE v.statut_validation 
            END AS statut_final
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        JOIN types_besoins t ON d.type_besoin_id = t.id
        JOIN users u ON d.user_id = u.id
        $whereClause
        ORDER BY d.date_creation DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $conn->prepare($query);

    // Bind filter params
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }

    // Bind LIMIT/OFFSET
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $stmt = $conn->prepare("SELECT d.*, t.libelle AS type_besoin,
            u.nom as demandeur_nom,
            u.email as demandeur_email,
     CASE 
            WHEN v.demande_id IS NULL THEN d.statut
            ELSE v.statut_validation
            END AS statut_final
    FROM demandes d
    LEFT JOIN validation v ON v.demande_id = d.id
    JOIN types_besoins t ON d.type_besoin_id = t.id
    JOIN users u ON d.user_id = u.id
    WHERE d.id = :id"
    );
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

/**
 * Crée un nouveau besoin
 */
function createBesoin(array $data) {
    $conn = getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        INSERT INTO demandes (
            `user_id`, `type_besoin_id`, `description`, `urgence`
        ) VALUES (
            :demandeur_id,:categorie,:description, :priorite
        )
    ";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'demandeur_id'      => $data['demandeur_id'],
            'categorie'         => $data['categorie'],
            'description'       => $data['description'],
            'priorite'          => $data['priorite'],
        ]);

        return $conn->lastInsertId();
    } catch (PDOException $e) {
        // Ici tu peux logger $e->getMessage() ou gérer l'erreur comme tu veux
        // Par exemple :
        error_log("Error inserting besoin: " . $e->getMessage());
        return false;
    }
}


/**
 * Supprime un utilisateur par ID.
 */
function deleteUser(int $id): bool {
    // Logique de suppression de BDD (ex: DELETE FROM users WHERE id = :id)
    // Pour cet exemple, on retourne juste true
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    return true; 
}

function getUserById(int $id): ?array {
    $pdo = getConnection();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return null if no user found
    return $user ?: null;
}



/**
 * Met à jour un besoin
 */
function updateBesoin($id, $data) {
    $conn = getConnection();
    $stmt = $conn->prepare("
        UPDATE demandes 
        SET  `type_besoin_id`= :categorie,
             `description`= :description,
             `urgence`= :priorite,
             `statut`= :statut
        WHERE id = :id
    ");
    
    return $stmt->execute([
        'id' => $id,
        'description' => $data['description'],
        'priorite' => $data['priorite'],
        'statut' => $data['statut'],
        'categorie' => $data['categorie'],
    ]);
}

/**
 * Supprime un besoin
 */
function deleteBesoin($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM demandes WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function createUser(array $userData): bool {
    $pdo = getConnection();
    // 1. Vérifier que l'email est unique
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $stmt->execute([':email' => $userData['email']]);
    $count = (int) $stmt->fetchColumn();
    if ($count > 0) {
        // email déjà utilisé
        throw new Exception("Cet email est déjà utilisé.");
    }

    // 2. Hasher le mot de passe
    $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        throw new Exception("Erreur de hashage du mot de passe.");
    }

    // 3. Insérer l’utilisateur
    $insert = $pdo->prepare('
        INSERT INTO users (nom, email, password, role, created_at)
        VALUES (:nom, :email, :password, :role, NOW())
    ');
    $success = $insert->execute([
        ':nom'      => $userData['nom'],
        ':email'    => $userData['email'],
        ':password' => $passwordHash,
        ':role'     => $userData['role'],
    ]);

    return $success;
}


/**
 * Obtient les statistiques
 */
function getStatistics() {
    $conn = getConnection();
    
    // Total des besoins
    $stmt = $conn->query("SELECT COUNT(*) as total FROM demandes");
    $total = (int) $stmt->fetch()['total'];
    
    
    // Besoins par statut (statut final avec jointure + CASE)
    $queryStatut = "
        SELECT 
            CASE 
                WHEN v.demande_id IS NULL THEN d.statut
                ELSE v.statut_validation
            END AS statut_final,
            COUNT(*) AS count
        FROM demandes d
        LEFT JOIN validation v ON v.demande_id = d.id
        GROUP BY statut_final
    ";
    $stmt = $conn->query($queryStatut);
    $parStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    // Besoins par priorité
    // (Tu avais `GROUP BY urgence` mais tu selects `priorite` → corrigé)
    $queryPriorite = "
        SELECT urgence, COUNT(*) AS count
        FROM demandes
        GROUP BY urgence
    ";
    $stmt = $conn->query($queryPriorite);
    $parPriorite = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    return [
        'total' => $total,
        'par_statut' => $parStatut,
        'par_priorite' => $parPriorite,
    ];
}

?>