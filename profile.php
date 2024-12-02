<?php
require 'config/db.php';

// Check if an ID is provided in the URL, otherwise default to a specific user
if (!isset($_GET['id'])) {
    // Default to user with ID = 1 if no ID is provided
    $user_id = 1;
} else {
    $user_id = $_GET['id'];
}

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Fetch user data
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMP353 Project - Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
        }
        .profile-header a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        .profile-header a:hover {
            text-decoration: underline;
        }
        .profile-card, .interests-card, .groups-card, .contacts-card, .gifts-card, .posts-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }
        .btn-custom {
            width: 100%;
        }
        .privacy-options {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="profile-header d-flex justify-content-between align-items-center">
        <h3>COMP353 PROJECT - COSN</h3>
        <nav>
            <a href="index.php">Home</a>
            <a href="messages.php">Messages</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3">
                <!-- Profile Information -->
                <div class="profile-card">
                    <h4>Profile</h4>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Birthday:</strong> <?php echo htmlspecialchars($user['birthday'] ?? 'N/A'); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location'] ?? 'N/A'); ?></p>
                    <p><strong>Profession:</strong> <?php echo htmlspecialchars($user['profession'] ?? 'N/A'); ?></p>
                    <a href="edit_profile.php" class="btn btn-primary btn-custom">Edit Profile</a>
                    <a href="delete_account.php" class="btn btn-danger btn-custom mt-2">Delete Account</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-6">
                <!-- User Info -->
                <div class="profile-card text-center">
                    <img src="https://via.placeholder.com/150" alt="Profile Picture" class="rounded-circle mb-3">
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p>Username: <?php echo htmlspecialchars($user['username'] ?? $user['name']); ?></p>
                    <textarea placeholder="Say something..." class="form-control mb-3"></textarea>
                    <div class="privacy-options">
                        <div>
                            <label><input type="radio" name="privacy" value="private" checked> Private</label>
                        </div>
                        <div>
                            <label><input type="radio" name="privacy" value="group"> Group Only</label>
                        </div>
                        <div>
                            <label><input type="radio" name="privacy" value="public"> Public</label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-custom mt-3">Post</button>
                </div>

                <!-- Posts -->
                <div class="posts-card">
                    <h4>Posts</h4>
                    <p>No posts yet!</p>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-3">
                <!-- Groups -->
                <div class="groups-card">
                    <h4>Groups</h4>
                    <p>No groups yet!</p>
                </div>

                <!-- Contacts -->
                <div class="contacts-card">
                    <h4>Contacts</h4>
                    <p>No contacts yet!</p>
                </div>

                <!-- Gift Registry -->
                <div class="gifts-card">
                    <h4>Gift Registry</h4>
                    <ul>
                        <li>Wheel of Cheese [<span class="text-success">received</span>] [<a href="#">remove</a>]</li>
                        <li>Magic Sword [<span class="text-success">received</span>] [<a href="#">remove</a>]</li>
                    </ul>
                    <form action="add_gift.php" method="POST">
                        <input type="text" name="new_gift" placeholder="Add a new gift" class="form-control" required>
                        <button type="submit" class="btn btn-success btn-custom mt-2">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
