<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Events</title>
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
<main>
<?php
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user's ID

// Get username of the currently logged-in user
$sql_username = "SELECT username FROM members WHERE id = ?";
$stmt_username = $conn->prepare($sql_username);
$stmt_username->bind_param("i", $user_id);
$stmt_username->execute();
$stmt_username->bind_result($username);
$stmt_username->fetch();
$stmt_username->close();

// Set the timezone and get current date and time
date_default_timezone_set('EST'); // Set this to your desired timezone
$currentDate = date('Y-m-d H:i:s');

// Query to get future events organized by the current user or events created by group members where the user is also a member
$sql_future_events = "
    SELECT DISTINCT e.*
    FROM events e
    LEFT JOIN group_members gm ON gm.group_id = e.group_id
    WHERE (e.organizer_username = ? OR gm.member_id = ?) AND e.event_date > ?";
$stmt_future = $conn->prepare($sql_future_events);
$stmt_future->bind_param("sis", $username, $user_id, $currentDate);
$stmt_future->execute();
$future_events = $stmt_future->get_result();

// Query to get past events organized by the current user or events created by group members where the user is also a member
$sql_past_events = "
    SELECT DISTINCT e.*
    FROM events e
    LEFT JOIN group_members gm ON gm.group_id = e.group_id
    WHERE (e.organizer_username = ? OR gm.member_id = ?) AND e.event_date <= ?";
$stmt_past = $conn->prepare($sql_past_events);
$stmt_past->bind_param("sis", $username, $user_id, $currentDate);
$stmt_past->execute();
$past_events = $stmt_past->get_result();

//Fetch groups the user is a part of
$sql_groups = "SELECT g.group_id, g.name FROM groupss g
JOIN group_members gm ON g.group_id = gm.group_id
WHERE gm.member_id = ?";
$stmt_groups = $conn->prepare($sql_groups);
$stmt_groups->bind_param("i", $user_id);
$stmt_groups->execute();
$groups_result = $stmt_groups->get_result();
?>

<h1 class="text-center mb-4"><strong>Event Management</strong></h1>
<div class="container mt-5">
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">

        <!-- Future Events Section -->
        <div class="mb-4">
            <h3>All Future Events of Your Groups</h3>
            <?php if ($future_events->num_rows > 0): ?>
                <?php while ($event = $future_events->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['event_name']); ?></h5>
                            <p class="card-text"><strong>Time:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                            <p class="card-text"><strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer_username']); ?></p>
                            <!-- Only display delete button if the current user is the organizer -->
                            <?php if ($event['organizer_username'] == $username): ?>
                                <form method="POST" action="delete_event.php" class="d-inline">
                                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            <?php endif; ?>
                            <a href="event_suggestions.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn btn-warning">Suggest Alternatives</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No future events found.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="card p-4 border-0 mt-4" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
        <!-- Past Events Section -->
        <div>
            <h3>Your Past Events</h3>
            <?php if ($past_events->num_rows > 0): ?>
                <?php while ($event = $past_events->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['event_name']); ?></h5>
                            <p class="card-text"><strong>Time:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                            <p class="card-text"><strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer_username']); ?></p>
                            <!-- Only display delete button if the current user is the organizer -->
                            <?php if ($event['organizer_username'] == $username): ?>
                                <form method="POST" action="delete_event.php" class="d-inline">
                                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No past events found.</p>
            <?php endif; ?>
        </div>
    </div>            
        <!-- Create Event Button -->
        <div class="mb-4">
            <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#createEventModal">Create Event</button>
        </div>
    </div>
</div>

<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Create Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_create_event.php" method="POST">
                    <div class="mb-3">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="eventName" name="event_name" placeholder="Enter event name" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3" placeholder="Enter event description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date and Time</label>
                        <input type="datetime-local" class="form-control" id="eventDate" name="event_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="eventLocation" name="location" placeholder="Enter location" required>
                    </div>

                    <div class="mb-3">
                        <label for="groupSelect" class="form-label" required>Select Group (optional)</label>
                        <select class="form-select" id="groupSelect" name="group_id">
                            <?php while ($group = $groups_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($group['group_id']); ?>">
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <input type="hidden" name="organizer_username" value="<?php echo htmlspecialchars($username); ?>">
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </form>
            </div>
        </div>
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

