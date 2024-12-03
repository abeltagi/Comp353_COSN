
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - About</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1>Manage Members</h1>
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
            $member_id = intval($_POST['member_id']);
            $new_privilege = $_POST['new_privilege'];
            $new_status = $_POST['new_status'];
            $user_id = $_SESSION['user_id'];

            // Ensure the logged-in user is an Admin
            $sql_check_admin = "SELECT privilege FROM members WHERE id = ? AND privilege = 'Admin'";
            $stmt_check_admin = $conn->prepare($sql_check_admin);
            $stmt_check_admin->bind_param("i", $user_id);
            $stmt_check_admin->execute();
            $stmt_check_admin->store_result();

            if ($stmt_check_admin->num_rows === 0) {
                die("Access denied. You do not have Admin privileges.");
            }
            $stmt_check_admin->close();

            // Update the privilege and status fields in the members table
            $sql_update = "UPDATE members SET privilege = ?, status = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $new_privilege, $new_status, $member_id);

            if ($stmt_update->execute()) {
                echo '<div class="alert alert-success" role="alert">
                        Member updated successfully.
                    </div>';
                header("Location: admin_manage_privilege_status.php"); // Redirect to the member management page
                exit();
            } else {
                echo "Error: " . $stmt_update->error;
            }
            $stmt_update->close();
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