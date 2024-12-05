<?php
session_start();
require 'config/db.php';

// Ensure the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Destroy the session
    session_unset();
    session_destroy();
    header("Location: index.php"); // Redirect to about page
    exit();
   
} else {
    header("Location: index.php"); // Redirect if no session exists
    exit();
}
?>
