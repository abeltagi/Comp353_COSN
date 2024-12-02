<?php
session_start();
require 'config/db.php';

// Ensure the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Set the user's status to Inactive
    $update_status = "UPDATE members SET status = 'Inactive' WHERE id = $user_id";
    if ($conn->query($update_status) === TRUE) {
        // Destroy the session
        session_unset();
        session_destroy();
        header("Location: index.php"); // Redirect to about page
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    header("Location: index.php"); // Redirect if no session exists
    exit();
}
?>
