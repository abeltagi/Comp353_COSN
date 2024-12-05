<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Your Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <!--Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
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
    <?php
                                   
        require 'config/db.php';                    // Include the database connection
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");          // Redirect to login page if not logged in
            exit;
        }
        $user_id = $_SESSION['user_id'];            // Retrieve the member ID from the session
        
        // Prepare the SQL query to get the member's name
        $sql = "SELECT firstname, lastname, username, email, address, profession, region, age, dob FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
        // Fetch privacy settings for the user
        $sql_privacy = "SELECT hide_firstname, hide_lastname, hide_email, hide_address, hide_region, hide_dob, hide_age, hide_profession FROM member_privacy WHERE member_id = ?";
        $stmt_privacy = $conn->prepare($sql_privacy);
        $stmt_privacy->bind_param("i", $user_id);
        $stmt_privacy->execute();
        $privacy_result = $stmt_privacy->get_result();

        if ($privacy_result->num_rows > 0) {
            $privacy_settings = $privacy_result->fetch_assoc();
        }
        
    ?>    

    <!-- Header -->
    <div class="container mt-4">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3">
                <!-- Profile Information -->
                <div class="profile-card">
                    <h4>Profile</h4>
                    <p style="word-break: break-word;"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    
                    <?php if (empty($privacy_settings['hide_firstname'])): ?>
                        <p style="word-break: break-word;"><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstname']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_lastname'])): ?>
                        <p style="word-break: break-word;"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastname']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_email'])): ?>
                        <p style="word-break: break-word;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_dob'])): ?>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob'] ?? 'N/A'); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_region'])): ?>
                        <p><strong>Region:</strong> <?php echo htmlspecialchars($user['region'] ?? 'N/A'); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_address'])): ?>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (empty($privacy_settings['hide_age'])): ?>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
                    <?php endif; ?>  
                    
                    <?php if (empty($privacy_settings['hide_profession'])): ?>
                        <p><strong>Profession:</strong> <?php echo htmlspecialchars($user['profession']); ?></p>
                    <?php endif; ?>
                    
                    <a href="edit_profile.php" class="btn btn-primary btn-custom">Edit Profile</a>
                    <a href="privacy_settings.php" class="btn btn-secondary btn-custom mt-2">Privacy Settings</a>
                    <a href="delete_account.php" class="btn btn-danger btn-custom mt-2">Delete Account</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-6">
                <!-- User Info -->
                <div class="profile-card text-center">
                    <img src="https://via.placeholder.com/150" alt="Profile Picture" class="rounded-circle mb-3">
                    <h2><?php echo htmlspecialchars($user['firstname']); ?></h2>
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

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>