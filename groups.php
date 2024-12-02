<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - About</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Welcome to Your Groups</h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
            
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-2 d-flex justify-content-center">
                    <!-- Optional Sidebar or Empty Space -->
                </div>

                <div class="col-8 d-flex justify-content-center">
                    <div class="card" style="width: 64rem;">
                        <div class="card-body">
                            <h5 class="card-title">Groups You're In</h5>
                            <p class="card-text">
                                <?php
                                // Start session and include database connection
                                session_start();
                                require 'config/db.php'; // Adjust the path to your config file

                                $member_id = $_SESSION['user_id']; // Retrieve logged-in user's ID

                                // Query to fetch groups the user belongs to
                                $sql = "SELECT g.group_id, g.name, g.description, gm.role , g.interest
                                        FROM groups g
                                        JOIN group_members gm ON g.group_id = gm.group_id
                                        WHERE gm.member_id = $member_id";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    echo "<ul class='list-group'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<li class='list-group-item'>
                                                <b>" . htmlspecialchars($row['name']) . "</b>: " .
                                                "<br>" . htmlspecialchars($row['description']) . "<br><strong>Interest: </strong>".htmlspecialchars($row['interest']) .
                                                "<br>" . 
                                                "<span class='badge bg-info text-dark'>Role: " . 
                                                htmlspecialchars($row['role']) . 
                                                "</span>
                                            </li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "You are not part of any groups.";
                                }
                                ?>
                            </p>
                            
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="create_group.php" class="btn btn-primary mb-2">Create a Group</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="add_remove_member_in_group.php" class="btn btn-secondary mb-2">Add/Remove a Member from a Group</a>
                                    </div>    
                                    <div>
                                        <a href="join_group.php" class="btn btn-success mb-2">Join a Group</a>
                                        <a href="leave_group.php" class="btn btn-danger mb-2">Leave a Group</a>
                                        <a href="delete_group.php" class="btn btn-danger mb-2">Delete a Group</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-2 d-flex justify-content-center">
                    <!-- Optional Sidebar or Empty Space -->
                </div>
            </div>
        </div>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>