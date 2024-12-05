
<?php
//local hosting server
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cosn';
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
}
?>