<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - About</title>
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

    <main>
        <div class="container mt-4">
            <div class="row g-4">
                <!-- Groups Info Card -->
                <div class="col-lg-8">
                    <div class="card rounded-lg border-0 shadow-sm" style="width: 100%;">
                        <div class="card-body">
                            <h2 class="card-title text-center"><strong>Groups You're In</strong></h2>
                            <?php
                            require 'config/db.php';

                            // Get the logged-in user's ID
                            $member_id = $_SESSION['user_id'];

                            // Query to fetch groups the user belongs to and their members
                            $sql = "SELECT 
                                        g.group_id, 
                                        g.name AS group_name, 
                                        g.description AS group_description, 
                                        g.interest, 
                                        m.username AS member_username, 
                                        gm.role AS member_role
                                    FROM groups g
                                    JOIN group_members gm ON g.group_id = gm.group_id
                                    JOIN members m ON gm.member_id = m.id
                                    WHERE g.group_id IN (
                                        SELECT group_id 
                                        FROM group_members 
                                        WHERE member_id = ?
                                    )
                                    ORDER BY g.group_id, gm.role";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $member_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Organize the data by groups
                            $groups = [];
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $group_id = $row['group_id'];
                                    if (!isset($groups[$group_id])) {
                                        $groups[$group_id] = [
                                            'name' => $row['group_name'],
                                            'description' => $row['group_description'],
                                            'interest' => $row['interest'],
                                            'members' => []
                                        ];
                                    }
                                    $groups[$group_id]['members'][] = [
                                        'username' => $row['member_username'],
                                        'role' => $row['member_role']
                                    ];
                                }
                            }
                            $stmt->close();

                            // Display groups and their members
                            if (!empty($groups)) {
                                foreach ($groups as $group_id => $group) {
                                    echo "<div class='mb-4'>";
                                    echo "<h6 class='text-primary'><strong>" . htmlspecialchars($group['name']) . "</strong></h6>";
                                    echo "<p><strong>Description:</strong> " . htmlspecialchars($group['description']) . "</p>";
                                    echo "<p><strong>Interest:</strong> " . htmlspecialchars($group['interest']) . "</p>";
                                    echo "<h6><strong>Members:</strong></h6>";
                                    if (!empty($group['members'])) {
                                        echo "<ul class='list-group'>";
                                        foreach ($group['members'] as $member) {
                                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                    <strong>" . htmlspecialchars($member['username']) . "</strong>
                                                    <span class='badge bg-dark text-light custom-shadow'>" . htmlspecialchars($member['role']) . "</span>
                                                  </li>";
                                        }
                                        echo "</ul>";
                                    } else {
                                        echo "<p>No members in this group.</p>";
                                    }
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>You are not part of any groups.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Actions Card -->
                <div class="col-lg-4 d-flex justify-content-center">
                    <div class="card rounded-lg border-0 shadow-sm" style="width: 100%;">
                        <div class="card-body">
                            <h2 class="card-title text-center"><strong>Group Actions</strong></h2>
                            <div class="list-group">
                                <a href="create_group.php" class="list-group-item list-group-item-action text-success"><strong>Create a Group</strong></a>
                                <a href="manage_join_request.php" class="list-group-item list-group-item-action"><strong>Manage Group Requests</strong></a>
                                <a href="add_remove_member_in_group.php" class="list-group-item list-group-item-action"><strong>Add/Remove Member</strong></a>
                                <a href="request_join_group.php" class="list-group-item list-group-item-action"><strong>Request to Join a Group</strong></a>
                                <a href="leave_group.php" class="list-group-item list-group-item-action text-danger"><strong>Leave a Group</strong></a>
                                <a href="delete_group.php" class="list-group-item list-group-item-action text-danger"><strong>Delete a Group</strong></a>
                            </div>
                        </div>
                    </div>
                </div>
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

