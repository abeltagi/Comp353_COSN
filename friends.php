<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Friends</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1><strong>Welcome to Your Groups</strong></h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a> 
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='friends.php' role="button"><strong>Your Friends</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>  
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a> 
        </nav>
    </header>
    <main>
    
    <div class="container my-4">
    <!-- Send Friend Request Card -->
    <div class="card p-4 shadow-sm mb-4">
        <h2 class="mb-4"><strong>Send a Friend Request to a Member</strong></h2>
        <form method="POST" action="send_friend_request.php" class="row g-3">
            <!-- Friend Username Input -->
            <div class="col-6">
                <label for="receiver_username" class="form-label">Friend's Username to add:</label>
                <input type="text" id="receiver_username" name="receiver_username" class="form-control" required>
            </div>
            <div class="col-6"></div>
            <!-- Submit Button -->
            <div class="col-4">
                <button type="submit" class="btn btn-primary w-100">Send Friend Request</button>
            </div>
        </form>
    </div>
    </div>
    

    <div class="container my-4">
    <!-- Your Friends Card -->
    <div class="card p-4 shadow-sm mb-4">
    <h3 class="mb-4"><strong>Your Friends</strong></h3>
    <ul class="list-group list-group-flush">
    <?php
        // include database connection
        require 'config/db.php';
        session_start();
        $user_id = $_SESSION['user_id']; // Logged-in user ID
        // Fetch all accepted friends where the current user is either the member or friend
        $sql = "SELECT DISTINCT m.username 
                FROM friends f
                JOIN members m ON f.friend_id = m.id
                WHERE f.member_id = ? AND f.status = 'Accepted'
                UNION
                SELECT DISTINCT m.username
                FROM friends f
                JOIN members m ON f.member_id = m.id
                WHERE f.friend_id = ? AND f.status = 'Accepted'";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($friend = $result->fetch_assoc()) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        " . htmlspecialchars($friend['username']) . "
                        <form method='POST' action='delete_friend.php' class='d-inline'>
                            <input type='hidden' name='friend_username' value='" . htmlspecialchars($friend['username']) . "'>
                            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                        </form>
                    </li>";
            }
        } else {
            echo "<li class='list-group-item'>You have no friends yet.</li>";
        }
    ?>
    </ul>
    </div>
    </div>    


    <div class="container my-4">
    <!-- Pending Friend Requests Card -->
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4"><strong>Members Who Want to Friend You</strong></h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // include database connection
                    require 'config/db.php';

                    $user_id = $_SESSION['user_id']; // Logged-in user ID

                    // Fetch pending friend requests
                    $sql = "SELECT f.id AS request_id, m.username, f.created_at 
                            FROM friends f 
                            JOIN members m ON f.member_id = m.id 
                            WHERE f.friend_id = ? AND f.status = 'Pending'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['username']) . "</td>
                                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                                    <td>
                                        <form method='POST' action='accept_friend_request.php' class='d-inline'>
                                            <input type='hidden' name='request_id' value='" . htmlspecialchars($row['request_id']) . "'>
                                            <button type='submit' class='btn btn-success btn-sm'>Accept</button>
                                        </form>
                                        <form method='POST' action='decline_friend_request.php' class='d-inline ms-2'>
                                            <input type='hidden' name='request_id' value='" . htmlspecialchars($row['request_id']) . "'>
                                            <button type='submit' class='btn btn-danger btn-sm'>Decline</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No members want to friend you currently.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div class="container my-4">
    <!-- Form to Block a Member Card -->
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4"><strong>Block a Member</strong></h3>
        <form method="POST" action="block_member.php" class="row g-3">
            <!-- Username Input -->
            <div class="col-6">
                <label for="blocked_username" class="form-label">Username to block:</label>
                <input type="text" id="blocked_username" name="blocked_username" class="form-control" required>
            </div>
            <div class="col-6"></div>
            <!-- Submit Button -->
            <div class="col-3">
                <button type="submit" class="btn btn-danger w-100">Block Member</button>
            </div>
        </form>
    </div>
    </div>


<?php

require 'config/db.php';

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch blocked members
$sql = "SELECT b.blocked_id, m.username 
        FROM blocks b
        JOIN members m ON b.blocked_id = m.id
        WHERE b.blocker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $blocked_members = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $blocked_members = [];
}
?>
<div class="container my-4">
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4"><strong>Blocked Members</strong></h3>
        <ul class="list-group list-group-flush">
            <?php if (!empty($blocked_members)): ?>
                <?php foreach ($blocked_members as $blocked): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?php echo htmlspecialchars($blocked['username']); ?></span>
                        <form method="POST" action="unblock_member.php" class="d-inline ms-2">
                            <input type="hidden" name="blocked_id" value="<?php echo htmlspecialchars($blocked['blocked_id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Unblock</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-center">You have not blocked any members.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>




    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
