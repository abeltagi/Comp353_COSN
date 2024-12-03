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

        $user_id = $_SESSION['user_id']; // Logged-in user's ID

        // Check if the logged-in user is an Admin
        $sql_check_admin = "SELECT privilege FROM members WHERE id = ? AND privilege = 'Admin'";
        $stmt_check_admin = $conn->prepare($sql_check_admin);
        $stmt_check_admin->bind_param("i", $user_id);
        $stmt_check_admin->execute();
        $stmt_check_admin->store_result();

        if ($stmt_check_admin->num_rows === 0) {
            die("Access denied. You do not have Admin privileges.");
        }
        $stmt_check_admin->close();

        // Fetch all members
        $sql_members = "SELECT id, username, privilege, status FROM members";
        $result_members = $conn->query($sql_members);
    ?>

        <div class="container mt-4">
            <h3>Manage Members</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Privilege</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $result_members->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                            <td><?php echo htmlspecialchars($member['privilege']); ?></td>
                            <td><?php echo htmlspecialchars($member['status']); ?></td>
                            <td>
                                <!-- Update Privilege and Status Form -->
                                <form method="POST" action="admin_update_member.php" class="d-inline">
                                    <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                    <div class="d-flex gap-2">
                                        <!-- Privilege Dropdown -->
                                        <select name="new_privilege" class="form-select d-inline w-auto">
                                            <option value="Member" <?php echo $member['privilege'] === 'Member' ? 'selected' : ''; ?>>Member</option>
                                            <option value="Senior" <?php echo $member['privilege'] === 'Senior' ? 'selected' : ''; ?>>Senior</option>
                                            <option value="Admin" <?php echo $member['privilege'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <!-- Status Dropdown -->
                                        <select name="new_status" class="form-select d-inline w-auto">
                                            <option value="Active" <?php echo $member['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Suspended" <?php echo $member['status'] === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                                            <option value="Inactive" <?php echo $member['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                        <!-- Update Button -->
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        
                                    </div>
                                </form>
                                <!-- Delete Member Button -->
                                <form method="POST" action="admin_delete_member.php" class="d-inline">
                                            <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
   
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>