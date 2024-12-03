<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Welcome to The Community Online Social Network</h1><br>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile_page.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a>
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
        <?php
            session_start();
            require 'config/db.php';

            // Ensure the user is logged in
            if (!isset($_SESSION['user_id'])) {
                die("You must be logged in to perform this action.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $group_id = intval($_POST['group_id']); // Group ID selected from the form
                $owner_id = $_SESSION['user_id'];      // Logged-in user's ID

                // Verify that the current user is the owner of the group
                $sql = "SELECT owner_id FROM groups WHERE group_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $stmt->bind_result($db_owner_id);
                if (!$stmt->fetch() || $db_owner_id !== $owner_id) {
                    die("You are not authorized to delete this group.");
                }
                $stmt->close();

                // Delete the group (and cascade to its members)
                $sql = "DELETE FROM groups WHERE group_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $group_id);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success" role="alert">
                            Group deleted successfully.
                          </div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                    Error deleting group: ' . $stmt->error . 
                    '</div>';
                }
                $stmt->close();
            }
        ?>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>