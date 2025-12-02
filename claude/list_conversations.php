<?php
// include '../includes/header.php';
ob_start();
session_start();
require_once '../includes/functions.php';

$current_user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for demo

$pdo = getConnection();
// Get all conversations with the last message
$sql = "SELECT 
            u.id as user_id,
            u.nom,
            c.message as last_message,
            c.sent_time,
            c.is_read,
            c.sender_id,
            COUNT(CASE WHEN c.is_read = 0 AND c.receiver_id = :uid_recv THEN 1 END) as unread_count
        FROM users u
        INNER JOIN Conversations c ON (c.sender_id = u.id OR c.receiver_id = u.id)
        WHERE (c.sender_id = :uid1 OR c.receiver_id = :uid2)
        AND u.id != :uid3
        AND c.id IN (
            SELECT MAX(id) 
            FROM Conversations 
            WHERE sender_id = :uid4 OR receiver_id = :uid5
            GROUP BY CASE 
                WHEN sender_id = :uid6 THEN receiver_id 
                ELSE sender_id 
            END
        )
        GROUP BY u.id, u.nom, c.message, c.sent_time, c.is_read, c.sender_id
        ORDER BY c.sent_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'uid_recv' => $current_user_id,
    'uid1'     => $current_user_id,
    'uid2'     => $current_user_id,
    'uid3'     => $current_user_id,
    'uid4'     => $current_user_id,
    'uid5'     => $current_user_id,
    'uid6'     => $current_user_id
]);

$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Chat Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --bg-light: #f8f9fa;
            --border-color: #e5e7eb;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .chat-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .chat-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .conversation-list {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .conversation-item {
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: inherit;
        }
        
        .conversation-item:hover {
            background: var(--bg-light);
            transform: translateX(5px);
        }
        
        .conversation-item.unread {
            background: #f0f7ff;
        }
        
        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            flex-shrink: 0;
        }
        
        .avatar-placeholder {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .conversation-content {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .username {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
        }
        
        .time {
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .last-message {
            color: #6b7280;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        
        .conversation-item.unread .last-message {
            font-weight: 600;
            color: #374151;
        }
        
        .badge-unread {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 30px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: var(--border-color);
            margin-bottom: 20px;
        }
        
        .search-box {
            padding: 20px 30px;
            border-bottom: 2px solid var(--border-color);
            background: var(--bg-light);
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h1><i class="bi bi-chat-dots-fill me-2"></i>Messages</h1>
            <span class="badge bg-light text-dark"><?php echo count($conversations); ?> Chats</span>
        </div>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search conversations..." class="form-control">
        </div>
        
        <div class="conversation-list" id="conversationList">
            <?php if (empty($conversations)): ?>
                <div class="empty-state">
                    <i class="bi bi-chat-square-text"></i>
                    <h3>No conversations yet</h3>
                    <p>Start chatting with someone to see your conversations here</p>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <a href="chat.php?user_id=<?php echo $conv['user_id']; ?>" 
                       class="conversation-item <?php echo $conv['unread_count'] > 0 ? 'unread' : ''; ?>"
                       data-username="<?php echo htmlspecialchars($conv['nom']); ?>">
                        <?php if (!empty($conv['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($conv['avatar']); ?>" 
                                 alt="Avatar" class="avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo strtoupper(substr($conv['nom'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="conversation-content">
                            <div class="conversation-header">
                                <span class="username"><?php echo htmlspecialchars($conv['nom']); ?></span>
                                <span class="time">
                                    <?php 
                                    $time = strtotime($conv['sent_time']);
                                    $now = time();
                                    $diff = $now - $time;
                                    
                                    if ($diff < 60) echo 'Just now';
                                    elseif ($diff < 3600) echo floor($diff / 60) . 'm ago';
                                    elseif ($diff < 86400) echo floor($diff / 3600) . 'h ago';
                                    elseif ($diff < 604800) echo floor($diff / 86400) . 'd ago';
                                    else echo date('M j', $time);
                                    ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <p class="last-message flex-grow-1">
                                    <?php 
                                    if ($conv['sender_id'] == $current_user_id) {
                                        echo '<i class="bi bi-check2-all me-1"></i>';
                                    }
                                    echo htmlspecialchars(substr($conv['last_message'], 0, 60));
                                    if (strlen($conv['last_message']) > 60) echo '...';
                                    ?>
                                </p>
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span class="badge-unread"><?php echo $conv['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const conversations = document.querySelectorAll('.conversation-item');
            
            conversations.forEach(conv => {
                const username = conv.getAttribute('data-username').toLowerCase();
                if (username.includes(searchTerm)) {
                    conv.style.display = 'flex';
                } else {
                    conv.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>