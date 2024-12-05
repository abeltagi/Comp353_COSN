<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Manage Members</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
<header>
        <!-- Bootstrap Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><strong>COSN</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php"><strong>Home</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><strong>Your Profile</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="friends.php"><strong>Your Friends</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php"><strong>Your Messages</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="events.php"><strong>Your Events</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php"><strong>Your Groups</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php"><strong>Search</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><strong>Logout</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?php
        require 'config/db.php';

        $user_id = $_SESSION['user_id']; // Logged-in user's ID

        // Check if the logged-in user is an Admin
        $sql_check_admin = "SELECT privilege FROM members WHERE id = ? AND privilege = 'Admin'";
        $stmt_check_admin = $conn->prepare($sql_check_admin);
        $stmt_check_admin->bind_param("i", $user_id);
        $stmt_check_admin->execute();
        $stmt_check_admin->store_result();

        if ($stmt_check_admin->num_rows === 0) {
            die('<div class="alert alert-danger" role="alert">Access denied. You do not have Admin privileges.</div>');
        }
        $stmt_check_admin->close();

        // Fetch all members
        $sql_members = "SELECT id, username, privilege, status FROM members";
        $result_members = $conn->query($sql_members);
        ?>

        <div class="container mt-5">
            <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                <h2 class="mb-4 text-center"><strong>Manage Members</strong></h2>
                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Username</th>
                                <th class="text-center">Privilege</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($member = $result_members->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle">
                                    <?php echo htmlspecialchars($member['username']); ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php echo htmlspecialchars($member['privilege']); ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php echo htmlspecialchars($member['status']); ?>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <!-- Update Privilege and Status Form -->
                                        <form method="POST" action="admin_update_member.php" class="d-inline">
                                            <input type="hidden" name="member_id"
                                                value="<?php echo htmlspecialchars($member['id']); ?>">
                                            <div class="d-flex gap-2">
                                                <!-- Privilege Dropdown -->
                                                <select name="new_privilege" class="form-select form-select-sm">
                                                    <option value="Junior"
                                                        <?php echo $member['privilege'] === 'Junior' ? 'selected' : ''; ?>>
                                                        Member/Junior</option>
                                                    <option value="Senior"
                                                        <?php echo $member['privilege'] === 'Senior' ? 'selected' : ''; ?>>
                                                        Senior</option>
                                                    <option value="Admin"
                                                        <?php echo $member['privilege'] === 'Admin' ? 'selected' : ''; ?>>
                                                        Admin</option>
                                                </select>
                                                <!-- Status Dropdown -->
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="Active"
                                                        <?php echo $member['status'] === 'Active' ? 'selected' : ''; ?>>
                                                        Active</option>
                                                    <option value="Suspended"
                                                        <?php echo $member['status'] === 'Suspended' ? 'selected' : ''; ?>>
                                                        Suspended</option>
                                                    <option value="Inactive"
                                                        <?php echo $member['status'] === 'Inactive' ? 'selected' : ''; ?>>
                                                        Inactive</option>
                                                </select>
                                                <!-- Update Button -->
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </div>
                                        </form>
                                        <!-- Delete Member Button -->
                                        <form method="POST" action="admin_delete_member.php" class="d-inline ms-2">
                                            <input type="hidden" name="member_id"
                                                value="<?php echo htmlspecialchars($member['id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>

