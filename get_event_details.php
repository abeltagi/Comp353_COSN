<?php
include 'config/db.php';

$event_id = $_GET['event_id'];

// Get event details
$sql_event = "SELECT * FROM events WHERE id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$result_event = $stmt_event->get_result();

if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();

    // Get suggestions for the event
    $sql_suggestions = "SELECT * FROM event_suggestions WHERE event_id = ?";
    $stmt_suggestions = $conn->prepare($sql_suggestions);
    $stmt_suggestions->bind_param("i", $event_id);
    $stmt_suggestions->execute();
    $result_suggestions = $stmt_suggestions->get_result();

    $suggestions = [];
    while ($row = $result_suggestions->fetch_assoc()) {
        $suggestions[] = $row;
    }

    echo json_encode(['success' => true, 'event' => $event, 'suggestions' => $suggestions]);
} else {
    echo json_encode(['success' => false]);
}
?>
