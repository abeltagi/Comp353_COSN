<?php
session_start();
require 'config/db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_keyword'], $_GET['search_category'])) {
    $keyword = '%' . $_GET['search_keyword'] . '%'; // Use wildcards for partial matching
    $category = $_GET['search_category'];

    $results = [];

    if ($category === 'members') {
        // Search members by profession, region, or any matching field
        $sql = "SELECT username, age, profession, region, status 
                FROM members 
                WHERE profession LIKE ? OR region LIKE ? OR username LIKE ? OR age LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
    } elseif ($category === 'groups') {
        // Search groups by interest or name
        $sql = "SELECT name, description, interest 
                FROM groups 
                WHERE name LIKE ? OR interest LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
    } else {
        echo "<div class='alert alert-danger'>Invalid category selected.</div>";
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }

    // Check if there are any results
    if (empty($results)) {
        echo "<div class='alert alert-warning text-center'>No matches found for your search combination.</div>";
    } else {
        // Display results in a list format
        echo "<ul class='list-group'>";
        if ($category === 'members') {
            foreach ($results as $member) {
                echo "<li class='list-group-item'>
                        <strong>Username:</strong> " . htmlspecialchars($member['username']) . "<br>
                        <strong>Age:</strong> " . htmlspecialchars($member['age']) . "<br>
                        <strong>Profession:</strong> " . htmlspecialchars($member['profession']) . "<br>
                        <strong>Region:</strong> " . htmlspecialchars($member['region']) . "<br>
                        <strong>Status:</strong> " . htmlspecialchars($member['status']) . "
                      </li>";
            }
        } elseif ($category === 'groups') {
            foreach ($results as $group) {
                echo "<li class='list-group-item'>
                        <strong>Group Name:</strong> " . htmlspecialchars($group['name']) . "<br>
                        <strong>Description:</strong> " . htmlspecialchars($group['description']) . "<br>
                        <strong>Interest:</strong> " . htmlspecialchars($group['interest']) . "
                      </li>";
            }
        }
        echo "</ul>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<div class='alert alert-danger'>Invalid request. Please try again.</div>";
}
?>

