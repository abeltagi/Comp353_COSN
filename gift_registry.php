<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch the user's wishlist
$sql_wishlist = "SELECT w.id AS wishlist_id, w.item_name, g.giver_id, m.username AS giver_username
                 FROM wishlists w
                 LEFT JOIN gifts g ON w.id = g.wishlist_id
                 LEFT JOIN members m ON g.giver_id = m.id
                 WHERE w.member_id = ?";
$stmt = $conn->prepare($sql_wishlist);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_wishlist = $stmt->get_result();

// Fetch the user's friends or group members' wishlists
$sql_friends_wishlist = "SELECT w.id AS wishlist_id, w.item_name, w.member_id, m.username AS owner_username, g.giver_id, gm.username AS giver_username
                         FROM wishlists w
                         JOIN members m ON w.member_id = m.id
                         LEFT JOIN gifts g ON w.id = g.wishlist_id
                         LEFT JOIN members gm ON g.giver_id = gm.id
                         WHERE w.member_id != ? 
                         AND (
                             w.member_id IN (SELECT friend_id FROM friends WHERE member_id = ? AND status = 'Accepted')
                             OR w.member_id IN (SELECT member_id FROM group_members WHERE group_id IN (
                                 SELECT group_id FROM group_members WHERE member_id = ?
                             ))
                         )";
$stmt_friends = $conn->prepare($sql_friends_wishlist);
$stmt_friends->bind_param("iii", $user_id, $user_id, $user_id);
$stmt_friends->execute();
$result_friends_wishlist = $stmt_friends->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Gift Registry/Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
                            <a class="nav-link" href="gift_registry.php"><strong>Your Gifts/Wishlist</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="posts.php"><strong>Your Posts</strong></a>
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

    <main class="container mt-4">
        <!-- Wishlist for the Logged-In User -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title">Your Wishlist</h3>
                <form method="POST" action="add_to_wishlist.php" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="item_name" class="form-control" placeholder="Add an item to your wishlist" required>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
                <ul class="list-group">
                    <?php while ($wishlist_item = $result_wishlist->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($wishlist_item['item_name']) ?>
                            <?php if ($wishlist_item['giver_id']): ?>
                                <span class="badge bg-success">Given by <?= htmlspecialchars($wishlist_item['giver_username']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Pending</span>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Wishlist for Friends/Group Members -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Friends' and Group Members' Wishlists</h3>
                <ul class="list-group">
                    <?php while ($friend_item = $result_friends_wishlist->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($friend_item['item_name']) ?> (Owned by <?= htmlspecialchars($friend_item['owner_username']) ?>)
                            <?php if ($friend_item['giver_id']): ?>
                                <span class="badge bg-success">Given by <?= htmlspecialchars($friend_item['giver_username']) ?></span>
                            <?php else: ?>
                                <form method="POST" action="give_gift.php" class="d-inline">
                                    <input type="hidden" name="wishlist_id" value="<?= $friend_item['wishlist_id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Give</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
