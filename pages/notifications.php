<?php
$page_title = "Notifications";
include '../includes/header.php';
include '../config/auth.php';


$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}
 
$service_id = $_SESSION['user_service'];
$pdo = getConnection();

// If user clicked "mark as read"
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notifId = (int) $_GET['mark_read'];

    // Prevent SQL injection — use prepared statement
    $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE id = :id");
    $stmt->execute(['id' => $notifId]);

    header("Location: notifications.php");
    exit;
}

// $isAdmin = $auth->isAdmin();
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Administrateur' ? 1 : 0;

$serviceFilter = $isAdmin === 1 ? "" : " AND service_id = :uid";

// Fetch notifications for the user
$stmt = $pdo->prepare("
    SELECT demande_id, created_at, d.*, n.id as notification_id,seen
    FROM notifications n
    JOIN demandes d ON n.demande_id = d.id
    WHERE is_just_for_admin = :is_admin {$serviceFilter}
    ORDER BY created_at DESC
");

$params = ['is_admin' => $isAdmin];
if (!$isAdmin) {
    $params['uid'] = $service_id; 
}

$stmt->execute($params);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($notifications) === 0): ?>
<p>No notifications.</p>
<?php else: ?>
<ul class="list-group mt-4">
    <?php foreach ($notifications as $n): ?>
    <li class="list-group-item d-flex justify-content-between align-items-start 
                <?php if ($n['seen'] == 0) echo 'list-group-item-warning'; ?>">
        <div class="mb-3 p-3">
            <strong><?php echo htmlspecialchars($n['message'] ?? $n['description']); ?></strong><br>
            <small class="text-muted"><?php echo htmlspecialchars($n['created_at']); ?></small><br>

            <small>
                <a href="#" class="btn btn-sm btn-outline-primary mt-2 d-inline-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#chatModal"
                    data-demande-id="<?php echo $n['demande_id']; ?>">
                    <i class="bi bi-chat-dots me-1"></i>
                    Envoyer un message au validateur pour la demande #<?php echo $n['demande_id']; ?>
                </a>
            </small>
        </div>

        <div>
            <?php if ($n['seen'] == 0): ?>
            <a href="notifications.php?mark_read=<?php echo $n['notification_id']; ?>"
                class="btn btn-sm btn-primary">Mark as read</a>
            <?php else: ?>
            <span class="badge bg-secondary">Read</span>
            <?php endif; ?>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
</body>

</html>