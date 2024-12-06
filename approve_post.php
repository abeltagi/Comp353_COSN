<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config/db.php';
    session_start();

    $post_id = $_POST['post_id'];
    $action = $_POST['action']; // approve or reject
    $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Verify group ownership
    $sql = "SELECT g.owner_id 
            FROM group_posts gp 
            JOIN groupss g ON gp.group_id = g.group_id 
            WHERE gp.post_id = ? AND g.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $sql_update = "UPDATE group_posts SET status = ? WHERE post_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $post_id);
        if ($stmt_update->execute()) {
            echo '<div class="alert alert-success">Post ' . htmlspecialchars($action) . ' successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Error updating post status.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">You are not authorized to perform this action.</div>';
    }
}


?>