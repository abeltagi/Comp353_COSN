<?php
require 'config/db.php';

// Set a default logged-in user ID for testing purposes (e.g., ID 1)
$logged_in_user = 1;

// Fetch list of users for the sidebar
$users_stmt = $conn->prepare("SELECT id, name FROM users WHERE id != ?");
$users_stmt->bind_param("i", $logged_in_user);
$users_stmt->execute();
$users_result = $users_stmt->get_result();

// Fetch chat messages with a specific user or set a default user ID for the chat
$chat_user_id = $_GET['user_id'] ?? 2; // Default to chat with user ID 2
$messages = [];
if ($chat_user_id) {
    $messages_stmt = $conn->prepare("
        SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC
    ");
    $messages_stmt->bind_param("iiii", $logged_in_user, $chat_user_id, $chat_user_id, $logged_in_user);
    $messages_stmt->execute();
    $messages = $messages_stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Chat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .sidebar {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        .chat-section {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        .chat-bubble {
            padding: 10px 15px;
            border-radius: 20px;
            margin-bottom: 10px;
            max-width: 75%;
            word-wrap: break-word;
        }
        .chat-bubble.sender {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
        }
        .chat-bubble.receiver {
            background-color: #e9ecef;
            align-self: flex-start;
        }
        .chat-input {
            border-radius: 20px;
            padding: 10px;
        }
        .send-button {
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">COMP353 PROJECT - COSN</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="messages.php">Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <h5>Messages</h5>
                    <div class="list-group">
                        <?php while ($user = $users_result->fetch_assoc()) { ?>
                            <a href="messages.php?user_id=<?= $user['id'] ?>" class="list-group-item list-group-item-action <?= ($chat_user_id == $user['id']) ? 'active' : '' ?>">
                                <?= htmlspecialchars($user['name']) ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="col-md-9">
                <div class="chat-section d-flex flex-column">
                    <h5 class="mb-4">
                        Chat with <?= htmlspecialchars($chat_user_id); ?>
                    </h5>

                    <div class="d-flex flex-column flex-grow-1 overflow-auto mb-3">
                        <?php if ($messages->num_rows > 0) { ?>
                            <?php while ($message = $messages->fetch_assoc()) { ?>
                                <div class="chat-bubble <?= $message['sender_id'] == $logged_in_user ? 'sender' : 'receiver' ?>">
                                    <?= htmlspecialchars($message['message']) ?><br>
                                    <small><?= $message['timestamp'] ?></small>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p>No messages yet. Start a conversation!</p>
                        <?php } ?>
                    </div>

                    <form method="POST" action="send_message.php" class="d-flex">
                        <input type="hidden" name="receiver_id" value="<?= $chat_user_id ?>">
                        <input type="text" name="message" class="form-control chat-input me-2" placeholder="Type your message" required>
                        <button type="submit" class="btn btn-primary send-button">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
