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
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
            <a class="btn btn-primary" href='profile_page.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a>
        </nav>
    </header>
    <main>
        <?php
            session_start();                            // Start the session
            require 'config/db.php';                    // Include the database connection
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");  // Redirect to login page if not logged in
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
                                    col1
                                </div>
                                
                                <div class="col-6 d-flex justify-content-center">
                                    <h1>Welcome, ' . htmlspecialchars($user_name) . '!</h1> <!--Display name-->
                                </div>
                                
                                <div class="col-3 d-flex justify-content-center">
                                    col3
                                </div>
                            </div>
                     </div>';
            } else {
                session_destroy();                      // Destroy the session if user doesn't exist
                header("Location: login.php");  // Redirect to login page
                exit();
            }
        ?>
        <div class="container">
            <div class="row">
                <div class="col-3 d-flex justify-content-center">
                    <div class="card text-center mb-3" style="width: 12rem;">
                        <div class="card-body">
                            <h5 class="card-title">Special title treatment</h5>
                            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                            <a href="create_group.php" class="btn btn-primary">Create Group</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 d-flex justify-content-center">
                    <h2>This is your Home page</h2>
                </div>
                
                <div class="col-3 d-flex justify-content-center">
                    col3
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