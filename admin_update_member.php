
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

   
   