<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $logged_in_user = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $logged_in_user, $receiver_id, $message);
        if ($stmt->execute()) {
            header("Location: messages.php?user_id=$receiver_id");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Message or receiver is empty.";
    }
} else {
    echo "Unauthorized access.";
}
?>
