<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Process Manage Join Request</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<header>
        <!-- Bootstrap Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><strong>COSN</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php"><strong>Home</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><strong>Your Profile</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="friends.php"><strong>Your Friends</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php"><strong>Your Messages</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="events.php"><strong>Your Events</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php"><strong>Your Groups</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gift_registry.php"><strong>Your Gifts/Wishlist</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="posts.php"><strong>Your Posts</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php"><strong>Search</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><strong>Logout</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
    <?php

        require 'config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = intval($_POST['request_id']);
            $action = $_POST['action'];

            // Fetch the request details to ensure the logged-in user is the group owner
            $sql = "SELECT jr.group_id, g.owner_id, jr.member_id
                    FROM join_requests jr
                    JOIN groupss g ON jr.group_id = g.group_id
                    WHERE jr.request_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->bind_result($group_id, $owner_id, $member_id);
            if (!$stmt->fetch() || $owner_id !== $_SESSION['user_id']) {
                die("You are not authorized to manage this request.");
            }
            $stmt->close();

            if ($action === 'accept') {
                // Add the member to the group
                $sql = "INSERT INTO group_members (group_id, member_id, role) VALUES (?, ?, 'Member')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $group_id, $member_id);
                if ($stmt->execute()) {
                    // Update the request status to 'Accepted'
                    $update_sql = "UPDATE join_requests SET status = 'Accepted' WHERE request_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $request_id);
                    $update_stmt->execute();
                    echo '<div class="alert alert-success" role="alert">
                                Request accepted.
                        </div>';
                    $update_stmt->close();
                }
                $stmt->close();
            } elseif ($action === 'decline') {
                // Update the request status to 'Declined'
                $sql = "UPDATE join_requests SET status = 'Declined' WHERE request_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $request_id);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-warning" role="alert">
                           Request declined.
                          </div>';
                }
                $stmt->close();
            } else {
                echo "Invalid action.";
            }
        }
    ?>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>