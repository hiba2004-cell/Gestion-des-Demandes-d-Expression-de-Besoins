<?php
session_start();

if (!isset($_SESSION['role'])) exit;

$role = $_SESSION['role'];

$conn = new PDO("mysql:host=localhost;dbname=ton_db;charset=utf8", "root", "");

$message = $_POST['message'] ?? "";

if ($message !== "") {

    if ($role == "admin") {
        $stmt = $conn->prepare("
            INSERT INTO messages (sender, message, seen_by_admin, seen_by_validateur)
            VALUES ('admin', ?, 1, 0)
        ");
    } else {
        $stmt = $conn->prepare("
            INSERT INTO messages (sender, message, seen_by_admin, seen_by_validateur)
            VALUES ('validateur', ?, 0, 1)
        ");
    }

    $stmt->execute([$message]);
}