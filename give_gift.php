<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wishlist_id = intval($_POST['wishlist_id']);
    $giver_id = $_SESSION['user_id'];

    $sql = "INSERT INTO gifts (wishlist_id, giver_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $wishlist_id, $giver_id);

    if ($stmt->execute()) {
        header("Location: gift_registry.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
