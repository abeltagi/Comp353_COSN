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
        <h1>Welcome to The Community Online Social Network</h1>
        <nav>
            <a class="btn btn-primary" href='home.php' role="button"><strong>Home</strong></a>
            <a class="btn btn-primary" href='profile.php' role="button"><strong>Your Profile</strong></a>
            <a class="btn btn-primary" href='messages.php' role="button"><strong>Your Messages</strong></a> 
            <a class="btn btn-primary" href='groups.php' role="button"><strong>Your Groups</strong></a>  
            <a class="btn btn-primary" href='logout.php' role="button"><strong>Logout</strong></a>   
        </nav>
    </header>
    <?php
        session_start();                            // Start the session
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
                    <p style="word-break: break-word;"><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstname']); ?></p>
                    <p style="word-break: break-word;"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastname']); ?></p>
                    <p style="word-break: break-word;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p style="word-break: break-word;"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob'] ?? 'N/A'); ?></p>
                    <p style="word-break: break-word;"><strong>Region:</strong> <?php echo htmlspecialchars($user['region'] ?? 'N/A'); ?></p>
                    <p style="word-break: break-word;"><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                    <p style="word-break: break-word;"><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
                    <p style="word-break: break-word;"><strong>Profession:</strong> <?php echo htmlspecialchars($user['profession'] ?? 'N/A'); ?></p>
                    
                    <a href="edit_profile.php" class="btn btn-primary btn-custom">Edit Profile</a>
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