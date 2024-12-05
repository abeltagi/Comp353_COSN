<?php
session_start();

?>
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
        <!-- Bootstrap Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><strong>COSN</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php"><strong>Home</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><strong>Your Profile</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="friends.php"><strong>Your Friends</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php"><strong>Your Messages</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="events.php"><strong>Your Events</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php"><strong>Your Groups</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php"><strong>Search</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><strong>Logout</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
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
                $member_id = $conn->insert_id;

                // Set session variables
                $_SESSION['user_id'] = $member_id;
                $_SESSION['username'] = $username;

                header("Location: home.php"); //Redirect to home.php after successful registration
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            // Check if the error is due to a duplicate entry
            if ($e->getCode() == 1062) { // MySQL error code for duplicate entry
                echo '<div class="alert alert-danger mt-4 text-center" role="alert">
                        Error: This email or username is already registered. Please use a different email or username.
                    </div>';
            } else {
                // For other errors, show a generic message
                echo '<div class="alert alert-danger mt-4 text-center" role="alert">
                        Error: Something went wrong. Please try again later. <br>
                        You must use a Proton email ending in: <br>
                        @proton.me<br>
                        @protonmail.com<br>
                        @pm.me<br>
                        @protonmail.ch
                    </div>';
            }
        }
    }
    ?>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <h2 class="text-center mb-4">Register</h2>
                    <form method="POST" class="row g-3">
                        <!-- First name -->
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">First Name:</label>
                            <input type="text" class="form-control" name="firstname" required>
                        </div>

                        <!-- Last name -->
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Last Name:</label>
                            <input type="text" class="form-control" name="lastname" required>
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-8">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <!-- Address -->
                        <div class="col-md-8">
                            <label for="address" class="form-label">Address:</label>
                            <input type="text" class="form-control" name="address" placeholder="1234 Main St" required>
                        </div>

                        <!-- Region -->
                        <div class="col-md-4">
                            <label for="region" class="form-label">Region:</label>
                            <input type="text" class="form-control" name="region" required>
                        </div>

                        <!-- Profession -->
                        <div class="col-md-8">
                            <label for="profession" class="form-label">Profession:</label>
                            <input type="text" class="form-control" name="profession"
                                placeholder="Baker, Chef, Student, Teacher, etc" required>
                        </div>

                        <!-- Age -->
                        <div class="col-md-4">
                            <label for="age" class="form-label">Age:</label>
                            <input type="number" class="form-control" name="age">
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth:</label>
                            <input type="date" class="form-control" name="dob">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <div class="d-grid">
                                <button type="submit" value="Submit" class="btn btn-primary">Register Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>
