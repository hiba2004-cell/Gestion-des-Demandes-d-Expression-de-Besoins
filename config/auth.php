<?php
/**
 * Gestion de l'authentification et des sessions
 * Système d'Expression du Besoin
 */

session_start();

require_once __DIR__ . '/database.php';

/**
 * Classe d'authentification
 */
class Auth {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Connexion utilisateur
     */
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        // $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email AND actif = 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch();

        if (!$user) {
            // pas d'utilisateur avec cet email
            return false;
        }

        $hashedPassword = $user['password'];
        
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_service'] = $user['service'];
            $_SESSION['user_poste'] = $user['poste'];
            $_SESSION['user_chef_id'] = $user['chef_id'];
            $_SESSION['last_activity'] = time();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Vérifier l'expiration de la session (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            $this->logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Obtenir les informations de l'utilisateur connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'nom' => $_SESSION['user_nom'],
            'prenom' => $_SESSION['user_prenom'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'service' => $_SESSION['user_service'],
            'poste' => $_SESSION['user_poste'],
            'chef_id' => $_SESSION['user_chef_id']
        ];
    }
    
    /**
     * Vérifier les permissions selon le rôle
     */
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Vérifier si l'utilisateur peut valider (est chef)
     */
    public function canValidate() {
        return $this->hasRole('validateur') || $this->hasRole('administrateur');
    }
    
    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin() {
        return $this->hasRole('administrateur');
    }
    
    /**
     * Obtenir l'ID du chef de l'utilisateur connecté
     */
    public function getChefId() {
        return $_SESSION['user_chef_id'] ?? null;
    }
    
    /**
     * Créer un nouveau mot de passe hashé
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Générer un token de réinitialisation
     */
    public function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Mettre à jour le dernier accès
     */
    public function updateLastAccess() {
        if ($this->isLoggedIn()) {
            $stmt = $this->conn->prepare("UPDATE users SET date_modification = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
        }
    }
}

/**
 * Fonction globale d'authentification
 */
function requireAuth() {
    $auth = new Auth();
    if (!$auth->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    return $auth;
}

/**
 * Fonction pour vérifier les rôles
 */
function requireRole($role) {
    $auth = requireAuth();
    if (!$auth->hasRole($role)) {
        header("Location: access-denied.php");
        exit();
    }
    return $auth;
}

/**
 * Redirection selon le rôle
 */
function redirectByRole($role) {
    switch($role) {
        case 'Demandeur':
            return 'dashboard-demandeur.php';
        case 'Validateur':
            return 'dashboard-validateur.php';
        case 'Administrateur':
            return 'dashboard-admin.php';
        default:
            return null;
    }
}

/**
 * Obtenir l'instance Auth globale
 */
function getAuth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}

/**
 * Middleware de sécurité
 */
function securityMiddleware() {
    // Protection CSRF
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            die('Token CSRF invalide');
        }
    }
    
    // Headers de sécurité
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Appliquer le middleware de sécurité
securityMiddleware();
?>