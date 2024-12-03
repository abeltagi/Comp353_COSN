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
    <h1>Register</h1><br>   
    <form method = "POST" class="row g-3">
        
        <!--First name-->
        <div class="col-6">
            <label for="firstname" class="form-label">First Name:</label>
            <input type="text" class="form-control" name="firstname" required>
        </div>

        <!--Last name-->
        <div class="col-6">
            <label for="lastname" class="form-label">Last Name:</label>
            <input type="text" class="form-control" name="lastname" required>
        </div>
        
        <!--Username-->
        <div class="col-6">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <!--Password-->
        <div class="col-6">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <!--Email-->
        <div class="col-8">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email">
        </div>

        <!--Address-->
        <div class="col-8">
            <label for="address" class="form-label">Address:</label>
            <input type="text" class="form-control" name="address" placeholder="1234 Main St" required>
        </div>
         
        <!--Region-->
        <div class="col-4">
            <label for="region" class="form-label">Region:</label>
            <input type="text" class="form-control" name="region" required>
        </div>
        
        <!--Profession-->
        <div class="col-8">
            <label for="profession" class="form-label">Profession:</label>
            <input type="text" class="form-control" name="profession" placeholder="Baker, Chef, Student, Teacher, etc" required>
        </div>
        
        <!--Age-->
        <div class="col-2">
            <label for="age" class="form-label">Age:</label>
            <input type="number" class="form-control" name="age">
        </div>
         
        <!--Date of Birth-->
        <div class="col-3">
            <label for="dob" class="form-label">Date of Birth:</label>
            <input type="date" class="form-control" name="dob">
        </div>
  
        <div class="col-12">
            <button type="submit" value = "Submit" class="btn btn-primary">Register Now</button>
        </div>
    </form>
    </div>
    
    
    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require 'config/db.php';
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $address = $_POST['address'];
            $profession = $_POST['profession'];
            $region = $_POST['region'];
            $age = $_POST['age'];
            $dob = $_POST['dob'];
    
            $stmt = $conn->prepare("INSERT INTO members (firstname, lastname, username, email, password, address, age, profession, region, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssisss", $firstname, $lastname, $username, $email, $password, $address, $age, $profession, $region, $dob);
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
                                Error: Something went wrong. Please try again later. <br>
                                You must use a Proton email ending in: <br>
                                @proton.me
                                @protonmail.com
                                @pm.me
                                @protonmail.ch
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