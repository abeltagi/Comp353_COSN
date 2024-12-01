<?php
include 'config/db.php';

// Query to retrieve posts from the "Photography Club"
$sql = "SELECT PostID, Type, Permissions
        FROM Post
        JOIN Groups ON Post.GroupID = Groups.GroupID
        WHERE Groups.Name = 'Photography Club'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Posts in Photography Club</h1>";
    echo "<table border='1'>
            <tr>
                <th>Post ID</th>
                <th>Type</th>
                <th>Permissions</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['PostID']}</td>
                <td>{$row['Type']}</td>
                <td>{$row['Permissions']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No posts found.";
}

$conn->close();
?>
