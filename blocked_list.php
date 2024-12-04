<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch blocked members
$sql = "SELECT b.blocked_id, m.username 
        FROM blocks b
        JOIN members m ON b.blocked_id = m.id
        WHERE b.blocker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $blocked_members = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $blocked_members = [];
}
?>
