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
        <h1>Welcome to Your Groups</h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>  
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-4 d-flex justify-content-center">
                <div class="card border-0" style="width: 16rem; height: 28rem;border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                    <div class="card-body">
                    <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="create_group.php" class="btn btn-primary mb-2">Create a Group</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="manage_join_request.php" class="btn btn-primary mb-2">Manage Group Requests</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="add_remove_member_in_group.php" class="btn btn-secondary mb-2">Add/Remove a Member from a Group</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="request_join_group.php" class="btn btn-success mb-2">Request to Join a Group</a> 
                                    </div>     
                                    <div>
                                        <a href="leave_group.php" class="btn btn-danger mb-2">Leave a Group</a>
                                        <a href="delete_group.php" class="btn btn-danger mb-2">Delete a Group</a>
                                    </div>
                                </div>
                </div>
                    </div>
                </div>
                
            </div>

                <div class="col-6 d-flex justify-content-center">
                    <div class="card border-0" style="width: 64rem;border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #007bff;"><strong>Groups You're In</strong></h5>
                            <?php
                            // Start session and include database connection
                            session_start();
                            require 'config/db.php';

                            $member_id = $_SESSION['user_id']; // Retrieve logged-in user's ID

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
                                    echo "<h6><strong>" . htmlspecialchars($group['name']) . "</strong></h6>";
                                    echo "<p><strong>Description:</strong> " . htmlspecialchars($group['description']) . "</p>";
                                    echo "<p><strong>Interest:</strong> " . htmlspecialchars($group['interest']) . "</p>";
                                    echo "<h6><strong>Members:</strong></h6>";
                                    if (!empty($group['members'])) {
                                        echo "<ul class='list-group'>";
                                        foreach ($group['members'] as $member) {
                                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'> <strong>" . 
                                                    htmlspecialchars($member['username']) . 
                                                    "</strong><span class='badge bg-dark text-light custom-shadow'>" . htmlspecialchars($member['role']) . "</span>
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

                <div class="col-2 d-flex justify-content-center">
                    <!-- Optional Sidebar or Empty Space -->
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
