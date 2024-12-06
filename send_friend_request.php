<?php
session_start();
require 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Friend Request</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
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
    <div class="container my-4">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4"><strong>Send Friend Request</strong></h3>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_SESSION['user_id']; // Logged-in user ID
                $friend_username = $_POST['receiver_username']; // Username of the member to send the friend request to

                // Fetch the ID of the user to send the friend request to
                $sql = "SELECT id FROM members WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $friend_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $friend = $result->fetch_assoc();
                    $friend_id = $friend['id'];

                    // Prevent the user from sending a friend request to themselves
                    if ($user_id === $friend_id) {
                        echo '<div class="alert alert-danger" role="alert">
                                You cannot send a friend request to yourself.
                            </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Check if the recipient has blocked the sender
                    $sql = "SELECT id FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $friend_id, $user_id); // Check if recipient blocked the sender
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        echo '<div class="alert alert-danger" role="alert">
                                You cannot send a friend request to this user because they have blocked you.
                            </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Check if there's a pending friend request in either direction
                    $sql = "SELECT id FROM friends 
                            WHERE (member_id = ? AND friend_id = ? AND status = 'Pending')
                               OR (member_id = ? AND friend_id = ? AND status = 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        echo '<div class="alert alert-warning" role="alert">
                                You cannot send a friend request to this user because there is already a pending request.
                            </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Insert the friend request
                    $sql = "INSERT INTO friends (member_id, friend_id, status) VALUES (?, ?, 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $user_id, $friend_id);

                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Friend request sent successfully.
                            </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Error sending friend request: ' . htmlspecialchars($stmt->error) . '
                            </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">
                            User not found.
                        </div>';
                    echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>


