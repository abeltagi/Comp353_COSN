<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Login</h1>
        <nav>
            <a class="btn btn-primary" href='index.php' role="button"><strong>About</strong></a> 
            <a class="btn btn-primary" href='register.php' role="button"><strong>Register</strong></a> 
        </nav>
    </header>
    <main>
    <div class="container" style="margin-left: 0; padding: 1rem ">
        <div class="row">
            <div class="col-2">
                <h1>Login</h1>
                <form method="POST">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required><br><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br><br>
                    <button type="submit" value ="Submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
    </main>
    
    <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require 'config/db.php';
            // Get user input and sanitize it
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // Prepare the SQL statement
            $stmt = $conn->prepare("SELECT * FROM members WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            // Fetch the result
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Verify user and password
            if ($user && password_verify($password, $user['password'])) {
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['privilege'] = $user['privilege'];
                
                // Set the user's status to Active
                $update_status = "UPDATE members SET status = 'Active' WHERE id = {$user['id']}";
                if ($conn->query($update_status) === TRUE) {
                    header("Location: home.php"); // Redirect to the dashboard
                } else {
                    echo "Error updating status: " . $conn->error;
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">
                            Error: Invalid email or password.
                      </div>';
            }
            $stmt->close();
        }
    ?>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>