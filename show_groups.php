<?php
session_start();
require 'config/db.php'; // Database connection

$member_id = $_SESSION['user_id']; // Replace with session value
$sql = "SELECT g.group_id, g.name, g.description, gm.role 
        FROM groupss g
        JOIN group_members gm ON g.group_id = gm.group_id
        WHERE gm.member_id = $member_id";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<h2>Your Groups:</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "<p><b>" . $row['name'] . "</b>: " . $row['description'] . " (Role: " . $row['role'] . ")</p>";
    }
} else {
    echo "You are not part of any groups.";
}
?>