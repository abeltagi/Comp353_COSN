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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $group_id = intval($_POST['group_id']);
            $member_id = $_SESSION['user_id'];

            // Check if the user has already requested to join this group
            $sql = "SELECT * FROM join_requests WHERE group_id = ? AND member_id = ? AND status = 'Pending'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $group_id, $member_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                echo '<div class="alert alert-warning" role="alert">
                            You have already requested to join this group.
                    </div>';
                exit();
            }
            $stmt->close();

            // Insert the join request
            $sql = "INSERT INTO join_requests (group_id, member_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $group_id, $member_id);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">
                            Request sent successfully.
                      </div>';
            } else {
                echo "Error: " . $stmt->error;
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