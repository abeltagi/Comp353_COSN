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

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch groups the user is in
$sql_in_groups = "SELECT g.group_id, g.name, g.description 
                  FROM groups g
                  JOIN group_members gm ON g.group_id = gm.group_id
                  WHERE gm.member_id = ?";
$stmt_in_groups = $conn->prepare($sql_in_groups);
$stmt_in_groups->bind_param("i", $user_id);
$stmt_in_groups->execute();
$result_in_groups = $stmt_in_groups->get_result();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h3 style="color: #007bff;"><strong>Groups You're In</strong></h3>
            <ul class="list-group">
                <?php if ($result_in_groups->num_rows > 0): ?>
                    <?php while ($group = $result_in_groups->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <b><?php echo htmlspecialchars($group['name']); ?></b>: 
                            <?php echo htmlspecialchars($group['description']); ?>
                            <form method="POST" action="process_leave_group.php" class="mt-2">
                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                <button type="submit" name="action" value="leave" class="btn btn-danger btn-sm">Leave Group</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item">You are not part of any groups.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>