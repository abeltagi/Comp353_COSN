<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Home</title>
    <!-- Bootstrap boilerplate -->
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
        <?php
        require 'config/db.php';

        $user_id = $_SESSION['user_id']; // Logged-in user's ID

        // Fetch groups the user is in
        $sql_in_groups = "SELECT g.group_id, g.name, g.description 
                        FROM groupss g
                        JOIN group_members gm ON g.group_id = gm.group_id
                        WHERE gm.member_id = ?";
        $stmt_in_groups = $conn->prepare($sql_in_groups);
        $stmt_in_groups->bind_param("i", $user_id);
        $stmt_in_groups->execute();
        $result_in_groups = $stmt_in_groups->get_result();
        ?>

        <div class="card shadow-sm p-4">
            <h2 class="text-center mb-4 text-primary"><strong>Groups You're In</strong></h2>
            <ul class="list-group list-group-flush">
                <?php if ($result_in_groups->num_rows > 0): ?>
                    <?php while ($group = $result_in_groups->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1"><strong><?php echo htmlspecialchars($group['name']); ?></strong></h5>
                                    <p class="mb-1"><?php echo htmlspecialchars($group['description']); ?></p>
                                </div>
                                <form method="POST" action="process_leave_group.php" class="d-inline">
                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                    <button type="submit" name="action" value="leave" class="btn btn-danger btn-sm">Leave Group</button>
                                </form>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">You are not part of any groups.</li>
                <?php endif; ?>
            </ul>
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
