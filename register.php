<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Register</h1>
        <nav>
            <a class="btn btn-primary" href='index.php' role="button"><strong>About</strong></a>
            <a class="btn btn-primary" href='login.php' role="button"><strong>Login</strong></a> 
        </nav>
    </header>

    <div class="container" style="margin-left: 0; padding: 1rem ">
        <div class="row">
            <div class="col-2">
                <h1>Register</h1>
                <form method="POST">
                    <!--First name-->
                    <label for="firstname">First Name:</label>
                    <input type="text" name="firstname" required><br><br>
                    
                    <!--Last name-->
                    <label for="lastname">Last Name:</label>
                    <input type="text" name="lastname" required><br><br>

                    <!--User name-->
                    <label for="username">Username:</label>
                    <input type="text" name="username" required><br><br>

                    <!--Location-->
                    <label for="location">Location:</label>
                    <input type="text" name="location" required><br><br>
                    
                    <!--Email-->
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br><br>
                    
                    <!--Password-->
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
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $username = $_POST['username'];
            $location = $_POST['location'];

            //$name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            //$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            //$stmt->bind_param("sss", $name, $email, $password);
            $stmt = $conn->prepare("INSERT INTO members (firstname, lastname, username, email, password, location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $password, $location);
            try {
                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Redirect to home.php after successful registration
                    header("Location: home.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                // Check if the error is due to a duplicate entry
                if ($e->getCode() == 1062) { // MySQL error code for duplicate entry
                    echo '<div class="alert alert-danger" role="alert">
                                Error: This email or username is already registered. Please use a different email or username.
                          </div>';
                } else {
                    // For other errors, show a generic message
                    echo '<div class="alert alert-danger" role="alert">
                                Error: Something went wrong. Please try again later.
                          </div>';
                }
            }
            
            
        }
    ?>
    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>