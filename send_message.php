<?php
session_start();

$role = $_POST['role'] ?? 'admin';
$_SESSION['role'] = $role;

$message = $_POST['message'] ?? "";

if (empty($message)) exit;

try {
    $conn = new PDO("mysql:host=localhost;dbname=ton_db;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_THROW);

    $stmt = $conn->prepare("
        INSERT INTO messages (sender, message, seen_by_admin, seen_by_validateur, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    if ($role === "admin") {
        $stmt->execute([$role, $message, 1, 0]);
    } else {
        $stmt->execute([$role, $message, 0, 1]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>