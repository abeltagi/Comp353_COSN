<?php
// Include the database connection file
include 'config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $location = $conn->real_escape_string($_POST['location']);
    $organizer = $conn->real_escape_string($_POST['organizer']);

    // Insert the event into the database
    $insertQuery = "
        INSERT INTO events (event_name, description, event_date, location, organizer)
        VALUES ('$event_name', '$description', '$event_date', '$location', '$organizer')
    ";

    if ($conn->query($insertQuery) === TRUE) {
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
