<?php
session_start();
// Include the database connection file
include 'config/db.php';

$user_id = $_SESSION['user_id']; // Retrieve logged-in user's ID

// Fetch the username of the logged-in user
$sql = "SELECT username FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $organizer_username = $user['username'];
} else {
    // Redirect to login if user not found
    header('Location: login.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $location = $conn->real_escape_string($_POST['location']);
    $group_id = !empty($_POST['group_id']) ? intval($_POST['group_id']) : null;

    // Insert the event into the database
    if ($group_id) {
        $insertQuery = "
            INSERT INTO events (event_name, description, event_date, location, organizer_username, group_id)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssssi", $event_name, $description, $event_date, $location, $organizer_username, $group_id);
    } else {
        $insertQuery = "
            INSERT INTO events (event_name, description, event_date, location, organizer_username)
            VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssss", $event_name, $description, $event_date, $location, $organizer_username);
    }

    if ($stmt->execute()) {
        // Redirect back to events.php with a success message
        header('Location: events.php?success=Event created successfully!');
        exit;
    } else {
        // Redirect back to events.php with an error message
        header('Location: events.php?error=Failed to create event.');
        exit;
    }
} else {
    // Redirect back to events.php if the form was not submitted
    header('Location: events.php');
    exit;
}



?>