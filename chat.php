<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Chat - Admin ↔ Validateur</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .chat-container {
        width: 100%;
        max-width: 700px;
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .role-selector {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .role-selector button {
        padding: 8px 16px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .role-selector button.active {
        background: #667eea;
        color: white;
    }

    .role-selector button:hover {
        transform: scale(1.05);
    }

    .messages {
        max-height: 500px;
        overflow-y: auto;
        padding: 20px 0;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .msg {
        padding: 12px 16px;
        border-radius: 12px;
        max-width: 75%;
        animation: messageIn 0.4s ease-out;
        word-wrap: break-word;
    }

    @keyframes messageIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .msg:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .admin {
        background: #e3f2fd;
        color: #1976d2;
        align-self: flex-start;
        border-left: 4px solid #1976d2;
    }

    .validateur {
        background: #c8e6c9;
        color: #388e3c;
        align-self: flex-end;
        border-right: 4px solid #388e3c;
    }

    .msg strong {
        display: block;
        font-size: 0.9em;
        margin-bottom: 5px;
        opacity: 0.8;
    }

    .msg small {
        display: block;
        font-size: 0.75em;
        margin-top: 8px;
        opacity: 0.6;
    }

    .send-box {
        display: flex;
        gap: 10px;
    }

    .send-box input {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #ddd;
        border-radius: 25px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .send-box input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .send-box button {
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .send-box button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .send-box button:active {
        transform: scale(0.95);
    }

    /* Scrollbar personnalisé */
    .messages::-webkit-scrollbar {
        width: 8px;
    }

    .messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .messages::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }

    .messages::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="chat-container">
        <h2>Chat : Admin ↔ Validateur</h2>

        <!-- Sélecteur de rôle pour tester -->
        <div class="role-selector">
            <button id="roleAdmin" class="role-btn active" data-role="admin">Admin</button>
            <button id="roleValidateur" class="role-btn" data-role="validateur">Validateur</button>
        </div>

        <div class="messages" id="messagesBox"></div>

        <div class="send-box">
            <input type="text" id="messageInput" placeholder="Écrire un message..." autocomplete="off">
            <button onclick="sendMessage()">Envoyer</button>
        </div>
    </div>

    <script>
    let currentRole = 'admin';

    $('.role-btn').click(function() {
        currentRole = $(this).data('role');
        $('.role-btn').removeClass('active');
        $(this).addClass('active');
        loadMessages();
    });

    function loadMessages() {
        $.ajax({
            url: "load_messages.php",
            success: function(data) {
                $("#messagesBox").html(data);
                $("#messagesBox").scrollTop($("#messagesBox")[0].scrollHeight);
            }
        });
    }

    function sendMessage() {
        var msg = $("#messageInput").val();
        if (msg.trim() === "") return;

        $.post("send_message.php", {
            message: msg,
            role: currentRole
        }, function() {
            $("#messageInput").val("");
            loadMessages();
        });
    }

    $("#messageInput").keypress(function(e) {
        if (e.which == 13) {
            sendMessage();
            return false;
        }
    });

    // Charger les messages au début
    loadMessages();

    // Charger les messages toutes les 1.5 secondes
    setInterval(loadMessages, 1500);
    </script>
</body>

</html>