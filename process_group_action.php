<?php
// This file is related to add_remove_member_group.php
session_start();
require 'config/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to perform this action.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = intval($_POST['group_id']); // Group selected from the form
    $username = trim($_POST['username']);  // Username entered in the form
    $action = $_POST['action'];            // Action (add/remove) selected from the form
    $owner_id = $_SESSION['user_id'];      // Logged-in user ID from session

    // Verify if the current user is the owner of the group
    $sql = "SELECT owner_id FROM groups WHERE group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($db_owner_id);
    if (!$stmt->fetch() || $db_owner_id !== $owner_id) {
        die("Only the group owner can manage members.");
    }
    $stmt->close();

    // Fetch the member ID based on the provided username
    $sql = "SELECT id FROM members WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($member_id);
    if ($stmt->fetch()) {
        $stmt->close();

        // Perform the specified action
        if ($action === 'add') {
            // Add the member to the group
            $sql = "INSERT INTO group_members (group_id, member_id, role) VALUES (?, ?, 'Member')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $group_id, $member_id);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">
                        Member added successfully.
                     </div>';
            } else {
                echo "Error: Could not add member. " . $stmt->error;
            }
            $stmt->close();
        } elseif ($action === 'remove') {
            // Remove the member from the group
            $sql = "DELETE FROM group_members WHERE group_id = ? AND member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $group_id, $member_id);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">
                        Member removed successfully.
                     </div>';
            } else {
                echo "Error: Could not remove member. " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Invalid action specified.";
        }
    } else {
        echo "Error: Username not found.";
    }
}
?>


