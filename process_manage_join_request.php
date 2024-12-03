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
            $request_id = intval($_POST['request_id']);
            $action = $_POST['action'];

            // Fetch the request details to ensure the logged-in user is the group owner
            $sql = "SELECT jr.group_id, g.owner_id, jr.member_id
                    FROM join_requests jr
                    JOIN groups g ON jr.group_id = g.group_id
                    WHERE jr.request_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->bind_result($group_id, $owner_id, $member_id);
            if (!$stmt->fetch() || $owner_id !== $_SESSION['user_id']) {
                die("You are not authorized to manage this request.");
            }
            $stmt->close();

            if ($action === 'accept') {
                // Add the member to the group
                $sql = "INSERT INTO group_members (group_id, member_id, role) VALUES (?, ?, 'Member')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $group_id, $member_id);
                if ($stmt->execute()) {
                    // Update the request status to 'Accepted'
                    $update_sql = "UPDATE join_requests SET status = 'Accepted' WHERE request_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $request_id);
                    $update_stmt->execute();
                    echo '<div class="alert alert-success" role="alert">
                                Request accepted.
                        </div>';
                    $update_stmt->close();
                }
                $stmt->close();
            } elseif ($action === 'decline') {
                // Update the request status to 'Declined'
                $sql = "UPDATE join_requests SET status = 'Declined' WHERE request_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $request_id);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-warning" role="alert">
                           Request declined.
                          </div>';
                }
                $stmt->close();
            } else {
                echo "Invalid action.";
            }
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