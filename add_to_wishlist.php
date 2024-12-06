<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO wishlists (member_id, item_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $item_name);

    if ($stmt->execute()) {
        header("Location: gift_registry.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
