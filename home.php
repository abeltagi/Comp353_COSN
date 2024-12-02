<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Welcome to The Community Online Social Network</h1><br>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile_page.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a>
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
        <?php
            session_start();                            // Start the session
            require 'config/db.php';                    // Include the database connection
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");          // Redirect to login page if not logged in
                exit;
            }
            
            $user_id = $_SESSION['user_id'];            // Retrieve the member ID from the session 
             
            // Prepare the SQL query to get the member's name
            $sql = "SELECT username FROM members WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_name = $row['username'];              // Get the member's username
                echo '<div class="container">
                            <div class="row">
                                <div class="col-3 d-flex justify-content-center">
                                    <!--Empty space-->
                                </div>
                                
                                <div class="col-6 d-flex justify-content-center">
                                    <h1>Welcome, ' . htmlspecialchars($user_name) . '!</h1> <!--Display name-->
                                </div>
                                
                                <div class="col-3 d-flex justify-content-center">
                                    <!--Empty space-->
                                </div>
                            </div>
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

                      echo '<div class="container">
                            <div class="row">
                                <div class="col-3 d-flex justify-content-center">
                                    <div class="card mb-3" style="width: 12rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">Your Groups</h5>
                                            <p class="card-text">-------------------</p>
                                            <p class="card-text">';
                                            
                                            // Display the groups
                                            if ($result_groups->num_rows > 0) {
                                                echo "<ul class='list-group'>";
                                                while ($group = $result_groups->fetch_assoc()) {
                                                    echo "<li class='list-group-item'>
                                                          <b>" . htmlspecialchars($group['name']) . "</b>: " . " 
                                                          <span class='badge bg-info text-dark'>Role: " . htmlspecialchars($group['role']) . "</span>
                                                          </li>"; 
                                            }
                                            echo "</ul>";
                                            } else {
                                                echo "You are not part of any groups.";
                                            }
                                            
                      echo '              </p>
                                            <a href="create_group.php" class="btn btn-primary">Create Group</a>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-6 d-flex justify-content-center">
                                    <h2>This is your Home page</h2>
                                </div>
                
                                <div class="col-3 d-flex justify-content-center">
                                    <!--Empty space-->
                                </div>
                            </div>
                        </div>';
            } else {
                session_destroy();                      // Destroy the session if user doesn't exist
                header("Location: login.php");          // Redirect to login page
                exit();
            }
        ?>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
