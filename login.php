<?php
session_start();
require 'config/db.php';
?>
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
        <!-- Bootstrap Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><strong>COSN</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><strong>Login</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><strong>Register</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <h2 class="text-center mb-4"><strong>Login</strong></h2>
                    <form method="POST">
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" value="Submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require 'config/db.php';
        // Get user input and sanitize it
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("SELECT id, username, privilege, password, status FROM members WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        try {
            // Check if the user is suspended
            if (!isset($user['status'])) {
                throw new Exception();
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger mt-4 text-center" role="alert">
                    This account does not exist. Please register to login.
                </div>';
            exit();
        }

        if ($user['status'] === 'Suspended') {
            echo '<div class="alert alert-danger mt-4 text-center" role="alert">
                    Your account is suspended. Please contact an administrator.
                </div>';
            exit();
        }

        // Verify user and password
        elseif ($user && password_verify($password, $user['password'])) {

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['privilege'] = $user['privilege'];

            header("Location: home.php"); // Redirect to the home
            exit();
        } else {
            echo '<div class="alert alert-danger mt-4 text-center" role="alert">
                    Error: Invalid username or password.
                </div>';
        }
        $stmt->close();
    }
    ?>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>
