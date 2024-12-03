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

        $owner_id = $_SESSION['user_id'];

        // Fetch pending join requests for groups owned by the user
        $sql = "SELECT 
                    jr.request_id, 
                    g.name AS group_name, 
                    m.username AS member_username, 
                    jr.status 
                FROM join_requests jr
                JOIN groups g ON jr.group_id = g.group_id
                JOIN members m ON jr.member_id = m.id
                WHERE g.owner_id = ? AND jr.status = 'Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
    ?>
    <div class="container">
        <h3>Pending Join Requests for Your Groups</h3>
        <ul class="list-group">
            <?php while ($request = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <b><?php echo htmlspecialchars($request['member_username']); ?></b> wants to join <b><?php echo htmlspecialchars($request['group_name']); ?></b>
                    <form method="POST" action="process_manage_join_request.php" class="mt-2 d-inline">
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        <button type="submit" name="action" value="accept" class="btn btn-success btn-sm "> Accept </button>
                        <button type="submit" name="action" value="decline" class="btn btn-danger btn-sm "> Decline </button>
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