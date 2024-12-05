<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Group Creation</title>
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
            <h2 class="text-center mb-4"><strong>Create a Group</strong></h2>
            <form method="POST" class="row g-3">
                <!-- Group Name -->
                <div class="col-md-8">
                    <label for="name" class="form-label">Group Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" class="form-control" style="resize: none;" rows="4" required></textarea>
                </div>

                <!-- Interest -->
                <div class="col-md-6">
                    <label for="interest" class="form-label">Interest: (one word)</label>
                    <input type="text" class="form-control" name="interest" required placeholder="E.g. Cooking, Clubbing, Sports">
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center">
                    <button type="submit" value="Submit" class="btn btn-primary w-100 mt-4">Create Group</button>
                </div>
            </form>
        </div>
    </main>

    <?php

    require 'config/db.php'; // Database connection

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login page if not logged in
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $owner_id = $_SESSION['user_id']; // Assume owner_id is passed securely (e.g., session)
        $interest = $_POST['interest'];

        try {
            // Check if the user has the privilege to create a group
            $privilege_check = $conn->query("SELECT privilege FROM members WHERE id = $owner_id");
            if (!$privilege_check) {
                throw new Exception("Error checking privileges: " . $conn->error);
            }

            $privilege = $privilege_check->fetch_assoc()['privilege'];

            if ($privilege === 'Admin' || $privilege === 'Senior') {
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("INSERT INTO groups (name, description, owner_id, interest) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $name, $description, $owner_id, $interest);
                $stmt->execute();

                // Get the last inserted group ID
                $group_id = $conn->insert_id;

                // Insert the owner into the group_members table
                $member_stmt = $conn->prepare("INSERT INTO group_members (group_id, member_id, role) VALUES (?, ?, ?)");
                $role = 'Owner';
                $member_stmt->bind_param("iis", $group_id, $owner_id, $role);
                $member_stmt->execute();

                echo '<div class="alert alert-success mt-3" role="alert">
                        Group created successfully, and you have been added as the owner!
                      </div>';

            } else {
                echo '<div class="alert alert-warning mt-3" role="alert">
                        You do not have the privilege to create a group. Ask an Administrator to grant you this privilege.
                      </div>';
            }
        } catch (mysqli_sql_exception $e) {
            // Handle duplicate entry errors specifically
            if ($e->getCode() === 1062) { // Error code 1062 is for duplicate entries
                echo '<div class="alert alert-danger mt-3" role="alert">
                        Error: A group with this name already exists. Please choose a different name.
                      </div>';
            } else {
                // Handle other MySQL errors
                echo '<div class="alert alert-danger mt-3" role="alert">
                        Database Error: ' . htmlspecialchars($e->getMessage()) . '
                      </div>';
            }
        } catch (Exception $e) {
            // Handle general errors
            echo '<div class="alert alert-danger mt-3" role="alert">
                    Error: ' . htmlspecialchars($e->getMessage()) . '
                  </div>';
        }
    }
    ?>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>



