<?php
$host = 'opc353.encs.concordia.ca'; // Hostname from the email
$user = 'opc353_2';                 // Username from the email
$password = 'EngineerLikenessLines51'; // Password from the email
$database = 'opc353_2';             // Database name from the email

// Establish a connection to the database
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
}
?>
