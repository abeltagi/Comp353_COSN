<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Delete Account</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1><strong>Delete Account</strong></h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a>
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a> 
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>  
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
    <div class="card p-4 shadow-sm">
        <h2 class="text-danger mb-4">Delete Account</h2>
            <p class="mb-4">Are you sure you want to delete your account? This action cannot be undone.</p>
            <form method="POST" class="d-flex flex-column gap-3">
                <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
            </form>
    </div>
    <?php
        session_start();
        require 'config/db.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Delete user account
            $delete_sql = "DELETE FROM members WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);
            if ($delete_stmt->execute()) {
                // Destroy session and redirect to login page
                session_destroy();
                header("Location: login.php");
                exit;
            } else {
                echo "Error deleting account.";
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