<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Accept Friend Request</title>
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
    <main>
        <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
            <h3 class="mb-4"><strong>Accept Friend Request</strong></h3>
            <?php
           
            require 'config/db.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $request_id = intval($_POST['request_id']); // Request ID of the friend request
                $user_id = $_SESSION['user_id']; // Current logged-in user ID

                // Fetch the member_id (sender) and friend_id (receiver)
                $sql = "SELECT member_id, friend_id FROM friends WHERE id = ? AND status = 'Pending'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $member_id = $row['member_id'];
                    $friend_id = $row['friend_id'];

                    if ($friend_id !== $user_id) {
                        echo '<div class="alert alert-danger" role="alert">
                                You are not authorized to accept this request.
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Update the original request status to 'Accepted'
                    $sql = "UPDATE friends SET status = 'Accepted' WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $request_id);
                    if (!$stmt->execute()) {
                        echo '<div class="alert alert-danger" role="alert">
                                Error accepting friend request: ' . htmlspecialchars($stmt->error) . '
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Insert the reverse record to make the friendship mutual
                    $sql = "INSERT INTO friends (member_id, friend_id, status) VALUES (?, ?, 'Accepted')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $friend_id, $member_id);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Friend request accepted successfully.
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Error creating mutual friendship: ' . htmlspecialchars($stmt->error) . '
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">
                            Friend request not found.
                          </div>';
                    echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                }
            }
            ?>
        </div>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
