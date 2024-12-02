<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Group Creation</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
    <header>
        <h1>Create a Group</h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a>
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a> 
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a>          
        </nav>
    </header>
    <main>
    <div class="container" style="margin-left: 0; padding: 1rem ">
    <h1>Create a Group</h1><br> 
    <form method="POST" class="row g-3">
        <div class="col-6">
            <label for="name" class="form-label">Group Name:</label><br>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Description:</label><br>
            <textarea name="description" class="form-control" style="width: 45rem; height: 8rem; resize: none;" required></textarea>
        </div>

        <div class="col-6">
            <label for="interest" class="form-label">Interest: (one word)</label><br>
            <input type="text" class="form-control" name="interest" required placeholder="E.g. Cooking, Clubbing, Sports"><br>
        </div>

        <div class="col-12">
            <button type="submit" value = "Submit" class="btn btn-primary">Create Group</button>
        </div>
    </form>
</div> 
</main> 

<?php
session_start();
require 'config/db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $owner_id = $_SESSION['user_id']; // Assume owner_id is passed securely (e.g., session)
    $interest = $_POST['interest'];

    try {
        // Check if the user has the privilege to create a group
        $privilege_check = $conn->query("SELECT privilege FROM members WHERE id = $owner_id");
        if (!$privilege_check) {
            throw new Exception("Error checking privileges: " . $conn->error);
        }

        $privilege = $privilege_check->fetch_assoc()['privilege'];

        if ($privilege === 'Admin' || $privilege === 'Senior') {
            // Use a prepared statement to avoid SQL injection
            $stmt = $conn->prepare("INSERT INTO groups (name, description, owner_id, interest) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $name, $description, $owner_id, $interest);
            $stmt->execute();

            // Get the last inserted group ID
            $group_id = $conn->insert_id;

            // Insert the owner into the group_members table
            $member_stmt = $conn->prepare("INSERT INTO group_members (group_id, member_id, role) VALUES (?, ?, ?)");
            $role = 'Owner';
            $member_stmt->bind_param("iis", $group_id, $owner_id, $role);
            $member_stmt->execute();
 
            echo '<div class="alert alert-success" role="alert">
                     Group created successfully, and you have been added as the owner!
                  </div>';

        } else {
            echo '<div class="alert alert-warning" role="alert">
                      You do not have the privilege to create a group. 
                      Ask an Administrator to grant you this privilege.
                  </div>';
        }
    } catch (mysqli_sql_exception $e) {
        // Handle duplicate entry errors specifically
        if ($e->getCode() === 1062) { // Error code 1062 is for duplicate entries
            echo '<div class="alert alert-danger" role="alert">
                      Error: A group with this name already exists. Please choose a different name.
                  </div>';
        } else {
            // Handle other MySQL errors
            echo '<div class="alert alert-danger" role="alert">
                      Database Error: ' . htmlspecialchars($e->getMessage()) . '
                  </div>';
        }
    } catch (Exception $e) {
        // Handle general errors
        echo '<div class="alert alert-danger" role="alert">
                  Error: ' . htmlspecialchars($e->getMessage()) . '
              </div>';
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


