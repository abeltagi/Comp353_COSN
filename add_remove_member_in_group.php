<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Group Member Addition/Removal</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
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

    <main class="container mt-5">
        <?php

        require 'config/db.php';

        // Ensure the user is logged in
        if (!isset($_SESSION['user_id'])) {
            die('<div class="alert alert-danger" role="alert">You must be logged in to perform this action.</div>');
        }

        // Retrieve groups managed by the logged-in user
        $owner_id = $_SESSION['user_id'];
        $sql = "SELECT group_id, name FROM groupss WHERE owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $groups = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            echo '<div class="alert alert-warning" role="alert">You do not own any groups!</div>';
        }
        $stmt->close();
        ?>

        <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
            <h2 class="mb-4">Add or Remove Members</h2>
            <form method="POST" action="process_group_action.php" class="row g-3">
                <!-- Group Selection -->
                <div class="col-12">
                    <label for="group_id" class="form-label">Select Group:</label>
                    <select id="group_id" name="group_id" required class="form-select">
                        <?php if (!empty($groups)) : ?>
                            <?php foreach ($groups as $group) : ?>
                                <option value="<?php echo $group['group_id']; ?>">
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value="" disabled>No groups available</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-12">
                    <!-- First name -->
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" id="firstname" class="form-control" name="firstname" required>
                    
                    <!-- Date of birth -->
                    <label for="dob" class="form-label">Date of Birth:</label>
                    <input type="date" id="dob" class="form-control" name="dob" required>
                    <!-- Email Address -->
                    <label for="email" class="form-label">Email Address:</label>
                    <input type="email" id="username" class="form-control" name="email" required>
                </div>

                <!-- Action Selection -->
                <div class="col-12">
                    <label for="action" class="form-label">Action:</label>
                    <select id="action" name="action" class="form-select" required>
                        <option value="add">Add</option>
                        <option value="remove">Remove</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                    <button type="submit" value="Submit" class="btn btn-primary w-100 mt-4">Proceed with action</button>
                </div>
            </form>

            <!-- Go Back Button -->
            <a href="groups.php" class="btn btn-secondary mt-4 w-100">Go Back</a>
        </div>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>

