
<?php
include 'includes/header.php';
?>
    <div class="chat-container mx-auto mt-5 p-4 border rounded" style="max-width: 600px; background-color: #f9f9f9;">
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