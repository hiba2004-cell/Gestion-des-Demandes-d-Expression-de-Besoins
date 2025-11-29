<?php
/**
 * Configuration de la base de données
 * Système d'Expression du Besoin
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'expression_besoin');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    /**
     * Connexion à la base de données
     */
    public function getConnection() {
        $this->conn = null;

        try {
            
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * Test de connexion à la base de données
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if($conn) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            return false;
        }
    }
}

/**
 * Instance globale de la base de données
 */
function getDatabase() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database;
}

/**
 * Obtenir une connexion à la base de données
 */
function getConnection() {
    $database = getDatabase();
    return $database->getConnection();
}
?>