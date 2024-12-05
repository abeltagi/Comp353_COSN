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

        require 'config/db.php'; // Include the database connection
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php"); // Redirect to login page if not logged in
            exit;
        }

        $user_id = $_SESSION['user_id']; // Retrieve the member ID from the session

        // Prepare the SQL query to get the member's name
        $sql = "SELECT username FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_name = $row['username']; // Get the member's username

            echo '<div class="container text-center my-4">
                    <h1><strong>Welcome, ' . htmlspecialchars($user_name) . '!</strong></h1>
                  </div>';

            // Prepare the SQL query to get the user's groups
            $sql_groups = "SELECT g.group_id, g.name, g.description, gm.role
                           FROM groups g
                           JOIN group_members gm ON g.group_id = gm.group_id
                           WHERE gm.member_id = ?";
            $stmt_groups = $conn->prepare($sql_groups);
            $stmt_groups->bind_param("i", $user_id);
            $stmt_groups->execute();
            $result_groups = $stmt_groups->get_result();

            echo '<div class="container mt-4">
                    <div class="row g-4 d-flex justify-content-center">';

            // User's Groups Card
            echo '<div class="col-md-4">
                    <div class="card border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                        <div class="card-body text-center">
                            <h5 class="card-title text"><strong>Your Groups</strong></h5>
                            <p class="card-text">';
            // Display the groups
            if ($result_groups->num_rows > 0) {
                echo "<ul class='list-group list-group-flush'>";
                while ($group = $result_groups->fetch_assoc()) {
                    echo "<li class='list-group-item'>
                            <b>" . htmlspecialchars($group['name']) . "</b> - 
                            <span class='badge bg-dark text-light'>Role: " . htmlspecialchars($group['role']) . "</span>
                          </li>";
                }
                echo "</ul>";
            } else {
                echo "You are not part of any groups.";
            }
            echo '          </p>
                            <a href="request_join_group.php" class="btn btn-primary mt-2">Request to Join a Group</a>
                        </div>
                    </div>
                  </div>';

            // Fetch pending join requests
            $sql_pending = "SELECT 
                              g.name AS group_name, 
                              jr.created_at 
                          FROM join_requests jr
                          JOIN groups g ON jr.group_id = g.group_id
                          WHERE jr.member_id = ? AND jr.status = 'Pending'";
            $stmt_pending = $conn->prepare($sql_pending);
            $stmt_pending->bind_param("i", $user_id);
            $stmt_pending->execute();
            $result_pending = $stmt_pending->get_result();

            // Pending Join Requests Card
            echo '<div class="col-md-4">
                    <div class="card border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                        <div class="card-body text-center">
                            <h5 class="card-title"><strong>Pending Group Join Requests</strong></h5>
                            <p class="card-text">';
            // Display pending join requests
            if ($result_pending->num_rows > 0) {
                echo "<ul class='list-group list-group-flush'>";
                while ($request = $result_pending->fetch_assoc()) {
                    echo "<li class='list-group-item'>
                            <b>" . htmlspecialchars($request['group_name']) . "</b><br>
                            <small>Requested on: " . htmlspecialchars($request['created_at']) . "</small>
                          </li>";
                }
                echo "</ul>";
            } else {
                echo "No pending requests.";
            }
            echo '          </p>
                        </div>
                    </div>
                  </div>';

            // Check if the logged-in user is an Admin
            $sql_check_admin = "SELECT privilege FROM members WHERE id = ? AND privilege = 'Admin'";
            $stmt_check_admin = $conn->prepare($sql_check_admin);
            $stmt_check_admin->bind_param("i", $user_id);
            $stmt_check_admin->execute();
            $stmt_check_admin->store_result();

            if ($stmt_check_admin->num_rows > 0) {
                // Manage Member Privileges Card
                echo '<div class="col-md-4">
                        <div class="card border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                            <div class="card-body text-center">
                                <h5 class="card-title"><strong>Manage Member Privileges</strong></h5>
                                <p class="card-text">As you are an Admin, you have the ability to change the privileges and status of <strong>ANY</strong> member.</p>
                                <a href="admin_manage_privilege_status.php" class="btn btn-primary">Change a Member\'s Privilege/Status</a>
                            </div>
                        </div>
                      </div>';
            }

            echo '    </div> <!-- row -->
                  </div> <!-- container -->';

            $stmt_check_admin->close();
        } else {
            session_destroy(); // Destroy the session if user doesn't exist
            header("Location: login.php"); // Redirect to login page
            exit();
        }
        ?>
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


