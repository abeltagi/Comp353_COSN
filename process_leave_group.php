<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Home</title>
    <!--Bootstrap boilerplate -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1>The Community Online Social Network</h1><br>
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $group_id = intval($_POST['group_id']);
            $action = $_POST['action'];
            $member_id = $_SESSION['user_id'];

            if ($action === 'leave') {
                // Check if the user is the owner of the group
                $sql_check_owner = "SELECT role FROM group_members WHERE group_id = ? AND member_id = ?";
                $stmt_check_owner = $conn->prepare($sql_check_owner);
                $stmt_check_owner->bind_param("ii", $group_id, $member_id);
                $stmt_check_owner->execute();
                $stmt_check_owner->bind_result($role);
                $stmt_check_owner->fetch();
                $stmt_check_owner->close();

                if ($role === 'Owner') {
                    echo '<div class="alert alert-danger" role="alert">
                            You cannot leave the group because you are the owner. 
                            Delete the group or contact Administration to help.
                          </div>';
                    exit();
                }

                // If not the owner, proceed to leave the group
                $sql_leave_group = "DELETE FROM group_members WHERE group_id = ? AND member_id = ?";
                $stmt_leave_group = $conn->prepare($sql_leave_group);
                $stmt_leave_group->bind_param("ii", $group_id, $member_id);
                if ($stmt_leave_group->execute()) {
                    echo '<div class="alert alert-success" role="alert">
                            Successfully left the group.
                          </div>';
                } else {
                    echo "Error: " . $stmt_leave_group->error;
                }
                $stmt_leave_group->close();
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