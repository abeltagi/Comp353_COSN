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
<body>
    <header>
        <h1>Create a Group</h1>
        <nav>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
            <a href="bootstraptest.php">BootStrap Test</a> 
            
        </nav>
    </header>
    
    <div class="container" style="margin-left: 0; padding: 1rem ">
        <div class="row">
            <div class="col">
                <h1>Create A Group</h1>
                <form method="POST">
                    <label>Group Name:</label><br>
                    <input type="text" name="name" required><br>
                    <label>Description:</label><br>
                    <textarea name="description"></textarea><br>
                    <button type="submit" value = "Submit" class="btn btn-primary">Create Group</button>
                </form>
            </div>
        </div>
    </div>   
    
    <?php
    session_start();
    require 'config/db.php'; // Database connection
    // Example: Assume user ID is stored in the session as 'user_id'
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");  // Redirect to login page if not logged in
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $owner_id = $_SESSION['user_id']; // Assume owner_id is passed securely (e.g., session)

        // Check if the user has the privilege to create a group
        $privilege_check = $conn->query("SELECT privilege FROM members WHERE id = $owner_id");
        $privilege = $privilege_check->fetch_assoc()['privilege'];

        if ($privilege === 'Admin' || $privilege === 'Senior') {
            $sql = "INSERT INTO groups (name, description, owner_id) VALUES ('$name', '$description', $owner_id)";
            if ($conn->query($sql) === TRUE) {
                echo '<div class="alert alert-success" role="alert">
                           Group created successfully!
                      </div>';
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo '<div class="alert alert-warning" role="alert">
                        You do not have the privilege to create a group or the group name already exists.
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


