<?php
session_start();
// Include the database connection file
include 'config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted to delete an event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the event ID from the form
    $event_id = $conn->real_escape_string($_POST['event_id']);

    // Verify if the event belongs to the logged-in user
    $sql_verify = "SELECT * FROM events WHERE id = ? AND organizer_username = (SELECT username FROM members WHERE id = ?)";
    $stmt_verify = $conn->prepare($sql_verify);
    $stmt_verify->bind_param("ii", $event_id, $user_id);
    $stmt_verify->execute();
    $result = $stmt_verify->get_result();

    if ($result->num_rows > 0) {
        // Event belongs to the user, proceed to delete
        $sql_delete = "DELETE FROM events WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $event_id);
        
        if ($stmt_delete->execute()) {
            // Redirect back to events.php with a success message
            header('Location: events.php?success=Event deleted successfully!');
        } else {
            // Redirect back to events.php with an error message
            header('Location: events.php?error=Failed to delete event.');
        }
    } else {
        // Redirect back to events.php with an error message if the event does not belong to the user
        header('Location: events.php?error=Unauthorized action.');
    }

    $stmt_verify->close();
    $stmt_delete->close();
    $conn->close();
} else {
    // Redirect back to events.php if the form was not submitted
    header('Location: events.php');
    exit;
}
?>
