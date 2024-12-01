<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require 'config/db.php';

// Check the connection
if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>
