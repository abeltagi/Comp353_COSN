<?php
session_start();
require 'config/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to perform this action.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = intval($_POST['group_id']); // Group ID selected from the form
    $owner_id = $_SESSION['user_id'];      // Logged-in user's ID

    // Verify that the current user is the owner of the group
    $sql = "SELECT owner_id FROM groups WHERE group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($db_owner_id);
    if (!$stmt->fetch() || $db_owner_id !== $owner_id) {
        die("You are not authorized to delete this group.");
    }
    $stmt->close();

    // Delete the group (and cascade to its members)
    $sql = "DELETE FROM groups WHERE group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    if ($stmt->execute()) {
        echo "Group deleted successfully.";
    } else {
        echo "Error deleting group: " . $stmt->error;
    }
    $stmt->close();
}
?>