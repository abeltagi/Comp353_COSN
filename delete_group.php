<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - About</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Delete Group(s)</h1>
        <nav>   
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a>
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a>   
        </nav>
    </header>
    <?php
        session_start();
        require 'config/db.php';

        // Ensure the user is logged in
        if (!isset($_SESSION['user_id'])) {
            die("You must be logged in to perform this action.");
        }

        // Fetch groups owned by the logged-in user
        $owner_id = $_SESSION['user_id'];
        $sql = "SELECT group_id, name FROM groups WHERE owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $groups = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    ?>
    <main>
    <form method="POST" action="process_group_delete.php">
        <label>Select Group to Delete:</label>
        <select name="group_id" required>
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo $group['group_id']; ?>">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No groups available</option>
            <?php endif; ?>
        </select><br>
        
        <button type="submit" class="btn btn-danger">Delete Group</button>
    </form>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>