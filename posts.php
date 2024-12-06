<?php
session_start();
include 'config/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the privilege of the logged-in user
$sql_fetch_privilege = "SELECT privilege FROM members WHERE id = ?";
$stmt_fetch_privilege = $conn->prepare($sql_fetch_privilege);
$stmt_fetch_privilege->bind_param("i", $user_id);
$stmt_fetch_privilege->execute();
$result_fetch_privilege = $stmt_fetch_privilege->get_result();
$user_privilege = $result_fetch_privilege->fetch_assoc()['privilege'];

// Fetch groups the user belongs to
$sql_groups = "SELECT g.group_id, g.name 
               FROM groupss g 
               JOIN group_members gm ON g.group_id = gm.group_id 
               WHERE gm.member_id = ?";
$stmt_groups = $conn->prepare($sql_groups);
$stmt_groups->bind_param("i", $user_id);
$stmt_groups->execute();
$result_groups = $stmt_groups->get_result();
$user_groups = $result_groups->fetch_all(MYSQLI_ASSOC);


// Fetch pending posts for approval (if user is a group owner)
$sql_pending = "SELECT gp.post_id, gp.content, gp.created_at, gp.member_id, g.name AS group_name
                FROM group_posts gp
                JOIN groupss g ON gp.group_id = g.group_id
                WHERE g.owner_id = ? AND gp.status = 'Pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param("i", $user_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();


// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_content'])) {
    $content = $_POST['post_content'];
    $visibility = $_POST['visibility']; // Public, Private, Group Only
    $group_id = $_POST['group_id'] ?? null; // Selected group for Group Only posts
    $status = ($visibility === 'Group' && $group_id) ? 'Pending' : 'Approved'; // Require approval for Group Only posts
    $file_path = null;

    // Handle file upload if a file is attached
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = basename($_FILES['file']['name']);
        $target_file = $upload_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $target_file; // Save the file path for the database
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
        }
    }

    // Insert post into the database
    $sql_insert = "INSERT INTO group_posts (member_id, content, visibility, group_id, file_path, status, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ississ", $user_id, $content, $visibility, $group_id, $file_path, $status);
    if ($stmt_insert->execute()) {
        $message = ($status === 'Pending') ? "Your post is pending approval by the group owner." : "Post submitted successfully!";
        echo "<div class='alert alert-success'>$message</div>";
        header("Location: posts.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error submitting post: {$stmt_insert->error}</div>";
    }
}


// Handle post approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['post_id'])) {
    $action = $_POST['action'];
    $post_id = $_POST['post_id'];
    $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';

    $sql_update_status = "UPDATE group_posts SET status = ? WHERE post_id = ?";
    $stmt_update_status = $conn->prepare($sql_update_status);
    $stmt_update_status->bind_param("si", $new_status, $post_id);
    if ($stmt_update_status->execute()) {
        echo "<div class='alert alert-success'>Post {$action}d successfully!</div>";
        header("Location: posts.php");
    } else {
        echo "<div class='alert alert-danger'>Error updating post status: {$stmt_update_status->error}</div>";
    }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $delete_post_id = $_POST['delete_post_id'];

    // Check if the logged-in user is an Admin
    $sql_check_admin = "SELECT privilege FROM members WHERE id = ?";
    $stmt_check_admin = $conn->prepare($sql_check_admin);
    $stmt_check_admin->bind_param("i", $user_id);
    $stmt_check_admin->execute();
    $result_check_admin = $stmt_check_admin->get_result();
    $user_privilege = $result_check_admin->fetch_assoc()['privilege'];

    // Allow deletion if the user is an Admin
    if ($user_privilege === 'Admin') {
        $sql_delete_post = "DELETE FROM group_posts WHERE post_id = ?";
        $stmt_delete_post = $conn->prepare($sql_delete_post);
        $stmt_delete_post->bind_param("i", $delete_post_id);

        if ($stmt_delete_post->execute()) {
            $_SESSION['message'] = "Post deleted successfully by Admin!";
        } else {
            $_SESSION['error'] = "Error deleting post: {$stmt_delete_post->error}";
        }
    } else {
        // Verify ownership or group ownership for non-Admins
        $sql_verify_post = "SELECT gp.post_id, gp.member_id, gp.group_id, gp.visibility, g.owner_id AS group_owner_id
                            FROM group_posts gp
                            LEFT JOIN groupss g ON gp.group_id = g.group_id
                            WHERE gp.post_id = ?";
        $stmt_verify_post = $conn->prepare($sql_verify_post);
        $stmt_verify_post->bind_param("i", $delete_post_id);
        $stmt_verify_post->execute();
        $result_verify_post = $stmt_verify_post->get_result();

        if ($result_verify_post->num_rows > 0) {
            $post = $result_verify_post->fetch_assoc();

            // Allow deletion if the post owner or group owner (for "Group Only" posts) is the logged-in user
            if ($post['member_id'] === $user_id || ($post['visibility'] === 'Group' && $post['group_owner_id'] === $user_id)) {
                $sql_delete_post = "DELETE FROM group_posts WHERE post_id = ?";
                $stmt_delete_post = $conn->prepare($sql_delete_post);
                $stmt_delete_post->bind_param("i", $delete_post_id);

                if ($stmt_delete_post->execute()) {
                    $_SESSION['message'] = "Post deleted successfully!";
                } else {
                    $_SESSION['error'] = "Error deleting post: {$stmt_delete_post->error}";
                }
            } else {
                $_SESSION['error'] = "You are not authorized to delete this post.";
            }
        } else {
            $_SESSION['error'] = "Post not found.";
        }
    }

    // Redirect back to the same page
    header("Location: posts.php");
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $post_id = $_POST['post_id'];
    $comment_text = $_POST['comment_text'];

    // Insert the comment into the database
    $sql_add_comment = "INSERT INTO comments (post_id, member_id, comment_text) VALUES (?, ?, ?)";
    $stmt_add_comment = $conn->prepare($sql_add_comment);
    $stmt_add_comment->bind_param("iis", $post_id, $user_id, $comment_text);

    if ($stmt_add_comment->execute()) {
        $_SESSION['message'] = "Comment added successfully!";
    } else {
        $_SESSION['error'] = "Error adding comment: {$stmt_add_comment->error}";
    }

    // Redirect back to the same page
    header("Location: posts.php");
    exit;
}



// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];

    // Check if the logged-in user is allowed to delete the comment
    $sql_check_permission = "SELECT c.post_id, c.member_id, gp.member_id AS post_owner_id
                             FROM comments c
                             JOIN group_posts gp ON c.post_id = gp.post_id
                             WHERE c.comment_id = ?";
    $stmt_check_permission = $conn->prepare($sql_check_permission);
    $stmt_check_permission->bind_param("i", $comment_id);
    $stmt_check_permission->execute();
    $result_permission = $stmt_check_permission->get_result();
    $permission = $result_permission->fetch_assoc();

    if ($permission && ($permission['member_id'] == $user_id || $permission['post_owner_id'] == $user_id)) {
        // User is either the comment creator or the post owner; allow deletion
        $sql_delete_comment = "DELETE FROM comments WHERE comment_id = ?";
        $stmt_delete_comment = $conn->prepare($sql_delete_comment);
        $stmt_delete_comment->bind_param("i", $comment_id);

        if ($stmt_delete_comment->execute()) {
            $_SESSION['message'] = "Comment deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting comment: {$stmt_delete_comment->error}";
        }
    } else {
        $_SESSION['error'] = "You are not authorized to delete this comment.";
    }

    // Redirect back to the same page
    header("Location: posts.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Your Posts</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .post-card { box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); border-radius: 10px; background-color: #f4f4f4; }
        .avatar { width: 150px; height: 150px; border-radius: 50%; background-color: #e9ecef; display: block; margin: 0 auto 20px; }
        .post-form .form-label { font-weight: bold; }
    </style>
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

    <main class="container mt-5">
    <!-- Post Form -->
    <div class="card p-4 shadow-sm post-card">
        <h2 class="text-center text-primary mb-4"><strong>Your Posts</strong></h2>
        <div class="avatar" style="background-image: url('avatar-placeholder.png');"></div>
        <form method="POST" action="posts.php" enctype="multipart/form-data" class="post-form">
    <div class="mb-3">
        <label for="postContent" class="form-label">Say something...</label>
        <textarea id="postContent" name="post_content" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Privacy:</label><br>
        <input type="radio" name="visibility" value="Public" required> Public<br>
        <input type="radio" name="visibility" value="Private"> Private (Friends Only)<br>
        <input type="radio" name="visibility" value="Group" id="groupOnlyRadio"> Group Only<br>
    </div>
    <div class="mb-3" id="groupSelection" style="display: none;">
        <label for="group_id" class="form-label">Select Group:</label>
        <select id="group_id" name="group_id" class="form-select">
            <?php foreach ($user_groups as $group): ?>
                <option value="<?php echo $group['group_id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="file" class="form-label">Attach an image or video:</label>
        <input type="file" name="file" id="file" class="form-control" accept="image/*,video/*">
    </div>
    <button type="submit" class="btn btn-primary">Post</button>
</form>

    </div>

<!-- Feed Posts Section -->
<div class="mt-5">
    <h3><strong>Your Feed Posts</strong></h3>

    <div class="feed">
    <?php
    // Fetch approved posts visible to the user
    $sql_feed = "SELECT gp.post_id, gp.content, gp.file_path, gp.created_at, m.username, gp.member_id, gp.group_id, gp.visibility, g.owner_id AS group_owner_id 
                 FROM group_posts gp
                 JOIN members m ON gp.member_id = m.id
                 LEFT JOIN groupss g ON gp.group_id = g.group_id
                 WHERE gp.status = 'Approved' AND (
                     gp.visibility = 'Public' OR
                     (gp.visibility = 'Private' AND EXISTS (
                         SELECT 1 FROM friends f 
                         WHERE f.friend_id = gp.member_id AND f.member_id = ?
                     )) OR
                     (gp.visibility = 'Group' AND gp.group_id IN (
                         SELECT group_id FROM group_members WHERE member_id = ?
                     ))
                 )
                 ORDER BY gp.created_at DESC";
    $stmt_feed = $conn->prepare($sql_feed);
    $stmt_feed->bind_param("ii", $user_id, $user_id);
    $stmt_feed->execute();
    $result_feed = $stmt_feed->get_result();

    while ($post = $result_feed->fetch_assoc()): ?>
        <div class="post card mt-3 shadow-sm post-card p-3">
            <p class="mb-0"><?php echo htmlspecialchars($post['content']); ?></p>
            <small class="text-muted">Posted by: <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></small>

            <!-- Display Uploaded File -->
            <?php if (!empty($post['file_path'])): ?>
                <div class="mt-3">
                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['file_path'])): ?>
                        <img src="<?php echo htmlspecialchars($post['file_path']); ?>" class="img-fluid" alt="Post Image">
                    <?php elseif (preg_match('/\.(mp4|webm|ogg)$/i', $post['file_path'])): ?>
                        <video controls class="w-100">
                            <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Display Comments -->
            <div class="comments mt-3">
                <h6>Comments:</h6>
                <?php
                $sql_comments = "SELECT c.comment_id, c.comment_text, c.created_at, c.member_id, m.username
                                 FROM comments c
                                 JOIN members m ON c.member_id = m.id
                                 WHERE c.post_id = ?
                                 ORDER BY c.created_at ASC";
                $stmt_comments = $conn->prepare($sql_comments);
                $stmt_comments->bind_param("i", $post['post_id']);
                $stmt_comments->execute();
                $result_comments = $stmt_comments->get_result();

                while ($comment = $result_comments->fetch_assoc()): ?>
                    <div class="comment">
                        <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                        <span><?php echo htmlspecialchars($comment['comment_text']); ?></span>
                        <small class="text-muted">on <?php echo $comment['created_at']; ?></small>
                        <!-- Delete Comment Button -->
                        <?php if ($comment['member_id'] == $user_id || $post['member_id'] == $user_id): ?>
                            <form method="POST" action="posts.php" class="d-inline">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                <button type="submit" name="delete_comment" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Add Comment Form -->
            <form method="POST" action="posts.php" class="mt-3">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <textarea name="comment_text" class="form-control mb-2" placeholder="Write a comment..." required></textarea>
                <button type="submit" name="add_comment" class="btn btn-primary btn-sm">Comment</button>
            </form>

            <!-- Allow the post owner to delete their own post -->
            <?php if ($post['member_id'] === $user_id): ?>
                <form method="POST" action="posts.php" class="d-inline">
                    <input type="hidden" name="delete_post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                </form>
            <?php endif; ?>

            <!-- Allow the group owner to delete posts with "Group Only" visibility -->
            <?php if ($post['visibility'] === 'Group' && $post['group_owner_id'] === $user_id): ?>
                <form method="POST" action="posts.php" class="d-inline">
                    <input type="hidden" name="delete_post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm mt-2">Delete (Group Owner)</button>
                </form>
            <?php endif; ?>

            <!-- Allow Admins to delete any post -->
            <?php if ($user_privilege === 'Admin'): ?>
                <form method="POST" action="posts.php" class="d-inline">
                    <input type="hidden" name="delete_post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm mt-2">Delete (Admin)</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    </div>
</div>


    <!-- Group Posts to Approve Section -->
    <?php if ($result_pending->num_rows > 0): ?>
    <div class="card mt-5">
        <h5 class="card-header">Group Posts to Approve</h5>
        <div class="card-body">
            <?php while ($post = $result_pending->fetch_assoc()): ?>
                <p><strong>Group:</strong> <?php echo htmlspecialchars($post['group_name']); ?></p>
                <p><strong>Posted by:</strong> 
                    <?php
                        // Fetch the username of the member who created the post
                        $poster_id = $post['member_id'];
                        $sql_poster = "SELECT username FROM members WHERE id = ?";
                        $stmt_poster = $conn->prepare($sql_poster);
                        $stmt_poster->bind_param("i", $poster_id);
                        $stmt_poster->execute();
                        $result_poster = $stmt_poster->get_result();
                        if ($poster = $result_poster->fetch_assoc()) {
                            echo htmlspecialchars($poster['username']);
                        }
                    ?>
                </p>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <form method="POST" action="posts.php" class="d-inline">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                </form>
                <form method="POST" action="posts.php" class="d-inline">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                </form>
                <hr>
            <?php endwhile; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card mt-5">
        <h5 class="card-header">Group Posts to Approve</h5>
        <div class="card-body">
            <p>No posts to approve at this time.</p>
        </div>
    </div>
<?php endif; ?>

</main>

<script>
    document.getElementById('groupOnlyRadio').addEventListener('change', function () {
        document.getElementById('groupSelection').style.display = 'block';
    });
    document.querySelectorAll('input[name="visibility"]').forEach(radio => {
        if (radio.id !== 'groupOnlyRadio') {
            radio.addEventListener('change', function () {
                document.getElementById('groupSelection').style.display = 'none';
            });
        }
    });
</script>


<!-- Bootstrap boilerplate -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

</body>
</html>



