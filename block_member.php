<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Block a Member</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
<main class="container mt-5">
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
        <h2 class="mb-4 text-center"><strong>Manage Friends</strong></h2>
        <?php
        require 'config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $blocker_id = $_SESSION['user_id']; // Logged-in user ID
            $blocked_username = $_POST['blocked_username']; // Username of the member to block

            try {
                // Fetch the ID of the user to be blocked
                $sql = "SELECT id FROM members WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $blocked_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $blocked_member = $result->fetch_assoc();
                    $blocked_id = $blocked_member['id'];

                    // Prevent the user from blocking themselves
                    if ($blocker_id === $blocked_id) {
                        echo '<div class="alert alert-danger" role="alert">
                                You cannot block yourself.
                              </div>';
                        echo '<div class="text-center mt-3"><a href="friends.php" class="btn btn-secondary">Go Back</a></div>';
                        exit;
                    }

                    // Check if the block entry already exists
                    $sql = "SELECT * FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $blocker_id, $blocked_id);
                    $stmt->execute();
                    $block_exists = $stmt->get_result()->num_rows > 0;

                    if ($block_exists) {
                        echo '<div class="alert alert-warning" role="alert">
                                This member is already blocked.
                              </div>';
                        echo '<div class="text-center mt-3"><a href="friends.php" class="btn btn-secondary">Go Back</a></div>';
                        exit;
                    }

                    // Check if the user is a friend
                    $sql = "SELECT * FROM friends WHERE 
                            (member_id = ? AND friend_id = ? AND status = 'Accepted') OR 
                            (member_id = ? AND friend_id = ? AND status = 'Accepted')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiii", $blocker_id, $blocked_id, $blocked_id, $blocker_id);
                    $stmt->execute();
                    $friend_result = $stmt->get_result();

                    if ($friend_result->num_rows > 0) {
                        // Remove the friendship
                        $sql = "DELETE FROM friends WHERE 
                                (member_id = ? AND friend_id = ?) OR 
                                (member_id = ? AND friend_id = ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiii", $blocker_id, $blocked_id, $blocked_id, $blocker_id);
                        $stmt->execute();

                        echo '<div class="alert alert-info" role="alert">
                                The user has been removed from your friends list as they are now blocked.
                              </div>';
                    }

                    // Insert the block into the database
                    $sql = "INSERT INTO blocks (blocker_id, blocked_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $blocker_id, $blocked_id);

                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Member blocked successfully.
                              </div>';
                        echo '<div class="text-center mt-3"><a href="friends.php" class="btn btn-secondary">Go Back</a></div>';
                        exit;
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">
                            User not found.
                          </div>';
                    echo '<div class="text-center mt-3"><a href="friends.php" class="btn btn-secondary w-100">Go Back</a></div>';
                    exit;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<div class="alert alert-danger" role="alert">
                        Database Error: ' . htmlspecialchars($e->getMessage()) . '
                      </div>';
                echo '<div class="text-center mt-3"><a href="friends.php" class="btn btn-secondary">Go Back</a></div>';
                exit;
            }
        }
        ?>
    </div>
</main>

<!-- Bootstrap boilerplate -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
</body>
</html>








