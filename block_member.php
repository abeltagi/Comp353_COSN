<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Friends</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1><strong>Welcome to Your Groups</strong></h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='friends.php' role="button"><strong>Your Friends</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>  
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
    <?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blocker_id = $_SESSION['user_id']; // Logged-in user ID
    $blocked_username = $_POST['blocked_username']; // Username of the member to block

    // Fetch the ID of the user to be blocked
    $sql = "SELECT id FROM members WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $blocked_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $blocked_member = $result->fetch_assoc();
        $blocked_id = $blocked_member['id'];

        // Prevent the user from blocking themselves
        if ($blocker_id === $blocked_id) {
            echo '<div class="alert alert-danger" role="alert">
                    You cannot block yourself.
                 </div>';
            echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';     
            exit;
        }

        // Insert the block into the database
        $sql = "INSERT INTO blocks (blocker_id, blocked_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $blocker_id, $blocked_id);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">
                    Member blocked successfully.
                 </div>';
            echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
            exit;     
        } else {
            echo "Error blocking member: " . $stmt->error;
            echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
            exit;
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">
                    User not found.
                 </div>';
        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
        exit;         
    }
}
?>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>





