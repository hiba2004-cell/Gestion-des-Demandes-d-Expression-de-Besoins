<?php
session_start();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Chat</title>

    <style>
    body {
        font-family: Arial;
        background: #f0f2f5;
    }

    .chat-container {
        width: 60%;
        margin: 40px auto;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .messages {
        max-height: 450px;
        overflow-y: auto;
        padding-bottom: 10px;
    }

    .msg {
        margin: 10px 0;
        padding: 10px;
        border-radius: 6px;
        width: fit-content;
        max-width: 80%;
    }

    .admin {
        background: #cce5ff;
        text-align: left;
    }

    .validateur {
        background: #d4edda;
        margin-left: auto;
        text-align: right;
    }

    .send-box {
        display: flex;
        margin-top: 15px;
    }

    .send-box input {
        flex: 1;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #aaa;
    }

    .send-box button {
        margin-left: 10px;
        padding: 10px 20px;
        background: #007bff;
        border: none;
        color: white;
        border-radius: 5px;
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    <div class="chat-container">

        <h2>Chat : Admin ↔ Validateur</h2>

        <div class="messages" id="messagesBox"></div>

        <div class="send-box">
            <input type="text" id="messageInput" placeholder="Écrire un message..." autocomplete="off">
            <button onclick="sendMessage()">Envoyer</button>
        </div>

    </div>


    <script>
    function loadMessages() {
        $.ajax({
            url: "load_messages.php",
            success: function(data) {
                $("#messagesBox").html(data);
                $("#messagesBox").scrollTop($("#messagesBox")[0].scrollHeight);
            }
        });
    }
    funtion sendMessage() {
        var msg = $("#messageInput").val();

        if (msg.trim() === "") return;

        $.post("send_message.php", {
            message: msg
        }, function() {
            $("#messageInput").val("");
            loadMessages();
        });
    }

    // Charger messages toutes les 1.5 secondes
    setInterval(loadMessages, 1500);

    // Charger au début
    loadMessages();
    </script>

</body>

</html>