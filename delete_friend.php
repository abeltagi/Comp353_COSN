<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Delete a Friend</title>
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
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4"><strong>Delete a Friend</strong></h3>
            <?php
           
            require 'config/db.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $friend_username = $_POST['friend_username']; // Friend's username to delete
                $user_id = $_SESSION['user_id']; // Logged-in user ID

                // Fetch the friend's ID based on the username
                $sql = "SELECT id FROM members WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $friend_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $friend = $result->fetch_assoc();
                    $friend_id = $friend['id'];

                    // Delete both records to remove the mutual friendship
                    $sql = "DELETE FROM friends 
                            WHERE (member_id = ? AND friend_id = ?) 
                               OR (member_id = ? AND friend_id = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);

                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Friend removed successfully.
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Error: ' . htmlspecialchars($stmt->error) . '
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">
                            Friend not found.
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
