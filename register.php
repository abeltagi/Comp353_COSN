<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Register</h1>
        <nav>
            <a href="index.php">Home</a> |
            <a href="login.php">Login</a>
        </nav>
    </header>

    <div class="container" style="margin-left: 0; padding: 1rem ">
        <div class="row">
            <div class="col-2">
                <h1>Register</h1>
                <form method="POST">
                    <label for="name">Name:</label>
                    <input type="text" name="name" required><br><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br><br>
                    <button type="submit" value = "Submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
    
    
    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require 'config/db.php';
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);
            if ($stmt->execute()) {
                echo ("<p>Registration successful!</p> <a href='login.php'>Login here</a>");
            } else {
                echo "Error: " . $stmt->error;
                //ACCOUNT FOR: duplicate registers(Name, Email) and output appropriate response
            }
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>