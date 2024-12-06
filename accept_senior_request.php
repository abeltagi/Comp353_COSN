<?php
require 'config/db.php';
session_start();

// Ensure that the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Approve Senior Request</title>
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
        <div class="card p-4 shadow-sm border-0" style="border-radius: 10px;">
            <h2 class="mb-4 text-center"><strong>Approve Senior Privilege Request</strong></h2>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
                $request_id = $_POST['request_id'];

                // Fetch the member ID from the request
                $sql_fetch_member = "SELECT member_id FROM senior_requests WHERE id = ?";
                $stmt = $conn->prepare($sql_fetch_member);
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $stmt->bind_result($member_id);
                $stmt->fetch();
                $stmt->close();

                // Update the member's privilege to Senior
                $sql_update_privilege = "UPDATE members SET privilege = 'Senior' WHERE id = ?";
                $stmt = $conn->prepare($sql_update_privilege);
                $stmt->bind_param("i", $member_id);

                if ($stmt->execute()) {
                    // Delete the request after approval
                    $sql_delete_request = "DELETE FROM senior_requests WHERE id = ?";
                    $stmt = $conn->prepare($sql_delete_request);
                    $stmt->bind_param("i", $request_id);
                    $stmt->execute();

                    echo '<div class="alert alert-success text-center">Request accepted successfully. The member is now a Senior.</div>';
                } else {
                    echo '<div class="alert alert-danger text-center">Error accepting request. Please try again later.</div>';
                }
            } else {
                echo '<div class="alert alert-warning text-center">No request ID provided.</div>';
            }
            ?>
            
            <div class="text-center mt-4">
                <a href="admin_manage_privilege_status.php" class="btn btn-primary">Go Back</a>
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
