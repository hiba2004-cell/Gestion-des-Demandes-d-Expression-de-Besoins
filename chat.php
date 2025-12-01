
<?php
// CREATE TABLE Conversations (
//     id INT PRIMARY KEY AUTO_INCREMENT,
//     sender_id INT NOT NULL,
//     receiver_id INT NOT NULL,
//     message TEXT NOT NULL,
//     sent_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     is_read TINYINT(1) DEFAULT 0,
//     CONSTRAINT fk_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
//     CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
// );
include 'includes/header.php';
?>


    <div class="chat-container mx-auto mt-5 p-4 border rounded" style="max-width: 600px;">
        <h2>Chat : <?php echo $_SESSION['user_role'] === "Validateur" ? "Administrateur" : "Validateur"; ?></h2>

        <div class="messages" id="messagesBox"></div>

        <div class="send-box">
            <input type="text" id="messageInput" placeholder="Écrire un message..." autocomplete="off">
            <button onclick="sendMessage()">Envoyer</button>
        </div>
    </div>

    <script>
    let currentRole = "<?php echo $_SESSION['user_role'] ?>";

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