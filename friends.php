<?php
session_start();
?>
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
    
    <div class="container my-4">
    <!-- Send Friend Request Card -->
    <div class="card p-4 border-0 mb-4" style="border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
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
    <div class="card p-4 border-0 mb-4" style="border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
    <h3 class="mb-4"><strong>Your Friends</strong></h3>
    <ul class="list-group list-group-flush">
    <?php
        // include database connection
        require 'config/db.php';
        
        $user_id = $_SESSION['user_id']; // Logged-in user ID
        // Fetch all accepted friends where the current user is either the member or friend
        $sql = "SELECT DISTINCT m.username 
                FROM friends f
                JOIN members m ON f.friend_id = m.id
                WHERE f.member_id = ? AND f.status = 'Accepted' AND m.status = 'Active'
                UNION
                SELECT DISTINCT m.username
                FROM friends f
                JOIN members m ON f.member_id = m.id
                WHERE f.friend_id = ? AND f.status = 'Accepted' AND m.status = 'Active'";

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
            echo "<li class='list-group-item text-muted'>You have no friends yet or they are all inactive.</li>";
        }
    ?>
    </ul>
    </div>
    </div>    

   
    <div class="container my-4">
    <!-- Pending Friend Requests Card -->
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
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
                        echo "<tr><td colspan='3' class='text-center text-muted'>No members want to friend you currently.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div class="container my-4">
    <!-- Form to Block a Member Card -->
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
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
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
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
