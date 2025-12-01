<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=ton_db;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_THROW);

    $messages = $conn->query("
        SELECT sender, message, created_at 
        FROM messages 
        ORDER BY created_at ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $msg) {
        $sender = strtolower(trim($msg['sender']));
        $class = ($sender === 'admin') ? 'admin' : 'validateur';
        
        echo "<div class='msg {$class}'>
                <strong>" . htmlspecialchars(ucfirst($msg['sender'])) . "</strong>
                " . nl2br(htmlspecialchars($msg['message'])) . "
                <small>" . date('H:i', strtotime($msg['created_at'])) . "</small>
            </div>";
    }
} catch (Exception $e) {
    echo "<div class='msg admin'><strong>Erreur</strong>" . htmlspecialchars($e->getMessage()) . "</div>";
}
?>