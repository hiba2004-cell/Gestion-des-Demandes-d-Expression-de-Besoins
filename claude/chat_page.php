<?php
include '../includes/header.php';


$pdo = getConnection();
$current_user_id = $_SESSION['user_id'] ?? 1;
$chat_user_id = $_GET['user_id'] ?? null;

if (!$chat_user_id) {
    header('Location: list-conversation.php');
    exit;
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $sql = "INSERT INTO Conversations (sender_id, receiver_id, message) VALUES (:sender, :receiver, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'sender' => $current_user_id,
            'receiver' => $chat_user_id,
            'message' => $message
        ]);
        header("Location: chat_page.php?user_id=" . $chat_user_id);
        exit;
    }
}

// Mark messages as read
$sql = "UPDATE Conversations SET is_read = 1 WHERE sender_id = :sender AND receiver_id = :receiver AND is_read = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute(['sender' => $chat_user_id, 'receiver' => $current_user_id]);

// Get chat user info
$sql = "SELECT id, nom FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $chat_user_id]);
$chat_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat_user) {
    header('Location: list-conversation.php');
    exit;
}

// Get all messages between users
$sql = "SELECT c.*, u.nom
        FROM Conversations c
        JOIN users u ON c.sender_id = u.id 
        WHERE (c.sender_id = :user1 AND c.receiver_id = :user2)
        OR (c.sender_id = :user3 AND c.receiver_id = :user4)
        ORDER BY c.sent_time ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'user1' => $current_user_id,
    'user2' => $chat_user_id,
    'user3' => $chat_user_id,
    'user4' => $current_user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($chat_user['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --sent-bg: #6366f1;
            --received-bg: #f3f4f6;
            --border-color: #e5e7eb;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .chat-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 60px);
        }
        
        .chat-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .back-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .chat-user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }
        
        .chat-user-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
            border: 3px solid white;
        }
        
        .chat-user-info h2 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .chat-user-info p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: #f9fafb;
            background-image: 
                linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message.sent {
            justify-content: flex-end;
        }
        
        .message.received {
            justify-content: flex-start;
        }
        
        .message-content {
            max-width: 60%;
            display: flex;
            flex-direction: column;
        }
        
        .message.sent .message-content {
            align-items: flex-end;
        }
        
        .message.received .message-content {
            align-items: flex-start;
        }
        
        .message-bubble {
            padding: 12px 18px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .message.sent .message-bubble {
            background: var(--sent-bg);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message.received .message-bubble {
            background: white;
            color: #1f2937;
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border-color);
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .message-input-container {
            padding: 20px 30px;
            background: white;
            border-top: 2px solid var(--border-color);
        }
        
        .message-input-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .message-input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid var(--border-color);
            border-radius: 25px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .message-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .send-button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .send-button:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }
        
        .send-button:active {
            transform: scale(0.95);
        }
        
        .date-divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .date-divider span {
            background: #e5e7eb;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #9ca3af;
        }
        
        .empty-chat i {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* Scrollbar styling */
        .messages-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .messages-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .messages-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        .messages-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <a href="list-conversation.php" class="back-button">
                <i class="bi bi-arrow-left"></i>
            </a>
            
            <?php if (!empty($chat_user['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($chat_user['avatar']); ?>" 
                     alt="Avatar" class="chat-user-avatar">
            <?php else: ?>
                <div class="chat-user-placeholder">
                    <?php echo strtoupper(substr($chat_user['nom'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            
            <div class="chat-user-info">
                <h2><?php echo htmlspecialchars($chat_user['nom']); ?></h2>
                <p><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> Online</p>
            </div>
        </div>
        
        <div class="messages-container" id="messagesContainer">
            <?php if (empty($messages)): ?>
                <div class="empty-chat">
                    <i class="bi bi-chat-heart"></i>
                    <h4>No messages yet</h4>
                    <p>Start the conversation by sending a message</p>
                </div>
            <?php else: ?>
                <?php 
                $last_date = null;
                foreach ($messages as $msg): 
                    $msg_date = date('Y-m-d', strtotime($msg['sent_time']));
                    $is_sent = $msg['sender_id'] == $current_user_id;
                    
                    if ($msg_date !== $last_date):
                        $last_date = $msg_date;
                        $today = date('Y-m-d');
                        $yesterday = date('Y-m-d', strtotime('-1 day'));
                        
                        if ($msg_date == $today) {
                            $date_label = 'Today';
                        } elseif ($msg_date == $yesterday) {
                            $date_label = 'Yesterday';
                        } else {
                            $date_label = date('F j, Y', strtotime($msg_date));
                        }
                ?>
                    <div class="date-divider">
                        <span><?php echo $date_label; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="message <?php echo $is_sent ? 'sent' : 'received'; ?>">
                    <div class="message-content">
                        <div class="message-bubble">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                        <div class="message-time">
                            <?php echo date('g:i A', strtotime($msg['sent_time'])); ?>
                            <?php if ($is_sent): ?>
                                <i class="bi bi-check2-all"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="message-input-container">
            <form method="POST" class="message-input-form" id="messageForm">
                <input 
                    type="text" 
                    name="message" 
                    class="message-input" 
                    placeholder="Type your message..."
                    autocomplete="off"
                    required
                    id="messageInput">
                <button type="submit" class="send-button">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom on page load
        const messagesContainer = document.getElementById('messagesContainer');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Focus input on load
        document.getElementById('messageInput').focus();
        
        // Smooth scroll animation
        messagesContainer.scroll({
            top: messagesContainer.scrollHeight,
            behavior: 'smooth'
        });
        
        // Optional: Auto-refresh messages every 5 seconds
        // setInterval(() => {
        //     location.reload();
        // }, 5000);
    </script>
</body>
</html>