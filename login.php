<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Login</h1>
        <nav>
            <a href="index.php">Home</a> |
            <a href="register.php">Register</a>
        </nav>
    </header>

    <div class="container" style="margin-left: 0; padding: 1rem ">
        <div class="row">
            <div class="col-2">
                <h1>Login</h1>
                <form method="POST">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br><br>
                    <button type="submit" value ="Submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
    
    
    <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require 'config/db.php';
            $email = $_POST['email'];
            $password = $_POST['password'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                echo ("Invalid email or password.");
            }
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>