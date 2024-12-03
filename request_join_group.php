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

        // Get the logged-in user's ID
        $member_id = $_SESSION['user_id'];

        // Fetch groups the user is not a part of
        $sql = "SELECT g.group_id, g.name, g.description
                FROM groups g
                WHERE g.group_id NOT IN (
                    SELECT group_id FROM group_members WHERE member_id = ?
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        <div class="container">
            <h3>Groups You Can Request to Join</h3>
            <ul class="list-group">
                <?php while ($group = $result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <b><?php echo htmlspecialchars($group['name']); ?></b>: <?php echo htmlspecialchars($group['description']); ?>
                        <form method="POST" action="process_request_join_group.php" class="mt-2">
                            <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Request to Join</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php
        $stmt->close();
    ?>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
