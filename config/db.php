<?php
$host = '127.0.0.1'; // Use 127.0.0.1 instead of localhost for better compatibility
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (leave blank)
$database = 'opc353_2'; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Debugging: Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
}
?>
