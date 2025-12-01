<?php
$conn = new PDO("mysql:host=localhost;dbname=ton_db;charset=utf8", "root", "");

$messages = $conn->query("SELECT * FROM messages ORDER BY created_at ASC")->fetchAll();

foreach ($messages as $msg) {
   $class = trim(strtolower($msg['sender']));

    
    echo "<div class='msg $class'>
            <strong>" . ucfirst($msg['sender']) . "</strong><br>"
            . nl2br(htmlspecialchars($msg['message'])) . "<br>
            <small>" . $msg['created_at'] . "</small>
          </div>";
}