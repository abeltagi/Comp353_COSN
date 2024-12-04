<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blocker_id = $_SESSION['user_id']; // Logged-in user ID
    $blocked_id = intval($_POST['blocked_id']); // ID of the blocked member

    // Verify the blocked relationship exists
    $sql = "SELECT id FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $blocker_id, $blocked_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Delete the block relationship
        $sql = "DELETE FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $blocker_id, $blocked_id);

        if ($stmt->execute()) {
            header("Location: friends.php"); // Redirect to the friends page
            exit;
        } else {
            echo "Error unblocking member: " . $stmt->error;
        }
    } else {
        echo "Invalid unblock request. You have nobody to block";
    }
}
?>

