<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user's ID

// Fetch friends list, only show unique friends
$sql_friends = "
    SELECT DISTINCT m.id, m.username 
    FROM members m
    JOIN friends f ON (f.member_id = m.id OR f.friend_id = m.id)
    WHERE (f.member_id = ? OR f.friend_id = ?) 
    AND m.id != ?
    AND f.status = 'Accepted'";
$stmt_friends = $conn->prepare($sql_friends);
$stmt_friends->bind_param("iii", $user_id, $user_id, $user_id);
$stmt_friends->execute();
$friends_result = $stmt_friends->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - COSN</title>
    <link rel="stylesheet" href="css/style.css">
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
        <!-- Messaging Section -->
        <div class="card p-4 shadow-sm border-0" style="border-radius: 10px;">
            <h2 class="text-center mb-4"><strong>Your Friends List</strong></h2>
            
            <?php if ($friends_result->num_rows > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php while ($friend = $friends_result->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #ffffff; border-radius: 5px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                    <?php echo strtoupper(substr(htmlspecialchars($friend['username']), 0, 1)); ?>
                                </div>
                                <span style="font-size: 1.1em;"><?php echo htmlspecialchars($friend['username']); ?></span>
                            </div>
                            <a href="chat.php?friend_id=<?php echo htmlspecialchars($friend['id']); ?>" class="btn btn-primary">Chat</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted text-center mt-3">You have no friends to message. Add some friends first!</p>
            <?php endif; ?>
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


