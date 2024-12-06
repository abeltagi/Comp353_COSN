<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['friend_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$friend_id = $_GET['friend_id'];

// Get friend's username
$sql_friend = "SELECT username FROM members WHERE id = ?";
$stmt_friend = $conn->prepare($sql_friend);
$stmt_friend->bind_param("i", $friend_id);
$stmt_friend->execute();
$friend_result = $stmt_friend->get_result();
$friend = $friend_result->fetch_assoc();

if (!$friend) {
    echo "Friend not found.";
    exit;
}

// Get messages between the user and the friend
$sql_messages = "
    SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC";
$stmt_messages = $conn->prepare($sql_messages);
$stmt_messages->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
$stmt_messages->execute();
$messages_result = $stmt_messages->get_result();

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);

    $sql_insert_message = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt_insert_message = $conn->prepare($sql_insert_message);
    $stmt_insert_message->bind_param("iis", $user_id, $friend_id, $message);

    if ($stmt_insert_message->execute()) {
        header("Location: chat.php?friend_id=$friend_id");
        exit;
    } else {
        echo "Failed to send the message. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($friend['username']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .message-container {
            height: 400px;
            overflow-y: auto;
            padding: 1rem;
            background-color: darkgray;
            border-radius: 10px;
        }

        .speech-bubble {
            position: relative;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border-radius: 10px;
            max-width: 60%;
            margin-bottom: 10px;
        }

        .speech-bubble.sent {
            background: #007bff;
            align-self: flex-end;
            color: white;
        }

        .speech-bubble.received {
            background: #e9ecef;
            align-self: flex-start;
            color: black;
        }

        .speech-bubble::after {
            content: "";
            position: absolute;
            bottom: 0;
            width: 0;
            height: 0;
            border: 10px solid transparent;
        }

        .speech-bubble.sent::after {
            right: -10px;
            border-left-color: #007bff;
            border-right: 0;
            margin-top: -10px;
        }

        .speech-bubble.received::after {
            left: -10px;
            border-right-color: #e9ecef;
            border-left: 0;
            margin-top: -10px;
        }

        .chat-form {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        .chat-container {
            margin-bottom: 80px;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
<header>
        <!-- Bootstrap Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><strong>COSN</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php"><strong>Home</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><strong>Your Profile</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="friends.php"><strong>Your Friends</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php"><strong>Your Messages</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="events.php"><strong>Your Events</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php"><strong>Your Groups</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gift_registry.php"><strong>Your Gifts/Wishlist</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="posts.php"><strong>Your Posts</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php"><strong>Search</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><strong>Logout</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-5 chat-container">
        <a href="messages.php" class="btn btn-secondary mb-4">Go Back</a>
        <h1>Chat with <?php echo htmlspecialchars($friend['username']); ?></h1>
        <div class="card p-4 mb-4 message-container d-flex flex-column">
            <?php if ($messages_result->num_rows > 0): ?>
                <?php while ($message = $messages_result->fetch_assoc()): ?>
                    <div class="speech-bubble <?php echo ($message['sender_id'] == $user_id) ? 'sent align-self-end' : 'received align-self-start'; ?>">
                        <p class="mb-1"><?php echo htmlspecialchars($message['message']); ?></p>
                        <p class="small">
                            <?php echo htmlspecialchars($message['sent_at']); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center">No messages yet. Start the conversation!</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Message Form -->
    <div class="chat-form">
        <form method="POST" class="d-flex">
            <input type="text" class="form-control" placeholder="Type your message..." name="message" required>
            <button class="btn btn-primary ms-2" type="submit">Send</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>


