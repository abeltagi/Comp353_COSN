<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Delete Members</title>
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

    <main>
        <div class="container mt-5">
            <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                <h2 class="mb-4 text-center text-primary"><strong>Delete Member</strong></h2>
                <?php
                require 'config/db.php';

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $member_id = intval($_POST['member_id']);

                    // Ensure the logged-in user is an Admin
                    $admin_id = $_SESSION['user_id'];
                    $sql_check_admin = "SELECT privilege FROM members WHERE id = ? AND privilege = 'Admin'";
                    $stmt_check_admin = $conn->prepare($sql_check_admin);
                    $stmt_check_admin->bind_param("i", $admin_id);
                    $stmt_check_admin->execute();
                    $stmt_check_admin->store_result();

                    if ($stmt_check_admin->num_rows === 0) {
                        echo '<div class="alert alert-danger" role="alert">
                                Access denied. You do not have Admin privileges.
                              </div>';
                        exit();
                    }
                    $stmt_check_admin->close();

                    // Prevent the logged-in Admin from deleting themselves
                    if ($member_id === $admin_id) {
                        echo '<div class="alert alert-danger" role="alert">
                                You cannot delete your own account.
                              </div>';
                        exit();
                    }

                    // Delete the member from the members table
                    $sql_delete_member = "DELETE FROM members WHERE id = ?";
                    $stmt_delete_member = $conn->prepare($sql_delete_member);
                    $stmt_delete_member->bind_param("i", $member_id);

                    if ($stmt_delete_member->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Member deleted successfully.
                              </div>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Error deleting member: ' . htmlspecialchars($stmt_delete_member->error) . '
                              </div>';
                    }
                    $stmt_delete_member->close();
                }
                ?>
            </div>
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
