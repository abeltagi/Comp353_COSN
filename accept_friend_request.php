<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Accept Friend Request</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap boilerplate -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <header>
        <h1><strong>Accept Friend Request</strong></h1>
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
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4"><strong>Accept Friend Request</strong></h3>
            <?php
            session_start();
            require 'config/db.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $request_id = intval($_POST['request_id']); // Request ID of the friend request
                $user_id = $_SESSION['user_id']; // Current logged-in user ID

                // Fetch the member_id (sender) and friend_id (receiver)
                $sql = "SELECT member_id, friend_id FROM friends WHERE id = ? AND status = 'Pending'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $member_id = $row['member_id'];
                    $friend_id = $row['friend_id'];

                    if ($friend_id !== $user_id) {
                        echo '<div class="alert alert-danger" role="alert">
                                You are not authorized to accept this request.
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Update the original request status to 'Accepted'
                    $sql = "UPDATE friends SET status = 'Accepted' WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $request_id);
                    if (!$stmt->execute()) {
                        echo '<div class="alert alert-danger" role="alert">
                                Error accepting friend request: ' . htmlspecialchars($stmt->error) . '
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                        exit;
                    }

                    // Insert the reverse record to make the friendship mutual
                    $sql = "INSERT INTO friends (member_id, friend_id, status) VALUES (?, ?, 'Accepted')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $friend_id, $member_id);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success" role="alert">
                                Friend request accepted successfully.
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Error creating mutual friendship: ' . htmlspecialchars($stmt->error) . '
                              </div>';
                        echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">
                            Friend request not found.
                          </div>';
                    echo '<a href="friends.php" class="btn btn-secondary mt-3">Go Back</a>';
                }
            }
            ?>
        </div>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
