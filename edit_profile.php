<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Edit Profile</title>
    <!--Bootstrap boilerplate -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1>Edit Your Profile</h1><br>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a>
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>        
    <?php
        session_start();
        require 'config/db.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Fetch existing profile data
        $sql = "SELECT firstname, lastname, email, address, profession, region, age, dob FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        } else {
            echo "User not found.";
            exit;
        }
       
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update profile information
            $firstname = $_POST['firstname']; 
            $lastname = $_POST['lastname']; 
            $address = $_POST['address']; 
            $profession = $_POST['profession']; 
            $region = $_POST['region']; 
            $email = $_POST['email']; 
            $dob = $_POST['dob'];
            $age = $_POST['age'];

            $update_sql = "UPDATE members SET firstname = ?, lastname = ?, email = ?, address = ?, profession = ?, region = ?, age = ?, dob = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssssisi", $firstname, $lastname, $email, $address, $profession, $region, $age, $dob, $user_id);
            if ($update_stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">
                        Profile updated successfully.
                    </div>';
            } else {
                echo "Error updating profile.";
            }
        }
        ?>
        
        
        <h2 class="mb-4">Edit Profile</h2>
        <form method="POST" class="row g-3">
            <!-- First Name -->
            <div class="col-md-6">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
            </div>
            <!-- Last Name -->
            <div class="col-md-6">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
            </div>
            <!-- Email -->
            <div class="col-12">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <!-- Address -->
            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            <!-- Profession -->
            <div class="col-md-6">
                <label for="profession" class="form-label">Profession</label>
                <input type="text" class="form-control" id="profession" name="profession" value="<?php echo htmlspecialchars($user['profession']); ?>">
            </div>
            <!-- Region -->
            <div class="col-md-6">
                <label for="region" class="form-label">Region</label>
                <input type="text" class="form-control" id="region" name="region" value="<?php echo htmlspecialchars($user['region']); ?>">
            </div>
            <!-- Age -->
            <div class="col-md-6">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>">
            </div>
            <!-- Date of Birth -->
            <div class="col-md-6">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
            </div>
            <!-- Submit Button -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
            </div>
        </form>

    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>