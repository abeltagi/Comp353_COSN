<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Get event_id from the GET request
if (!isset($_GET['event_id'])) {
    echo "Invalid event ID.";
    exit;
}

$event_id = $_GET['event_id'];

// Fetch event details to display
$sql_event = "SELECT * FROM events WHERE id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();

if (!$event) {
    echo "Event not found.";
    exit;
}

// Fetch existing suggestions for this event
$sql_suggestions = "SELECT es.*, m.username AS suggested_by_username
                    FROM event_suggestions es
                    JOIN members m ON es.suggested_by = m.id
                    WHERE es.event_id = ?";
$stmt_suggestions = $conn->prepare($sql_suggestions);
$stmt_suggestions->bind_param("i", $event_id);
$stmt_suggestions->execute();
$suggestions_result = $stmt_suggestions->get_result();

// Handle form submission for new suggestions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_suggestion'])) {
    $suggested_date = !empty($_POST['suggested_date']) ? $_POST['suggested_date'] : null;
    $suggested_location = !empty($_POST['suggested_location']) ? $_POST['suggested_location'] : null;

    if ($suggested_date || $suggested_location) {
        $sql_insert_suggestion = "INSERT INTO event_suggestions (event_id, suggested_by, suggested_date, suggested_location)
                                  VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert_suggestion);
        $stmt_insert->bind_param("iiss", $event_id, $user_id, $suggested_date, $suggested_location);

        if ($stmt_insert->execute()) {
            $success_message = "Your suggestion has been submitted successfully!";
        } else {
            $error_message = "Failed to submit your suggestion. Please try again.";
        }
    } else {
        $error_message = "Please provide at least one suggestion (date or location).";
    }
}

// Handle voting for suggestions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_suggestion_id'])) {
    $suggestion_id = $_POST['vote_suggestion_id'];

    // Insert the vote if the user hasn't already voted for this suggestion
    $sql_vote = "INSERT IGNORE INTO suggestion_votes (suggestion_id, voted_by) VALUES (?, ?)";
    $stmt_vote = $conn->prepare($sql_vote);
    $stmt_vote->bind_param("ii", $suggestion_id, $user_id);

    if ($stmt_vote->execute()) {
        $success_message = "Your vote has been submitted successfully!";
    } else {
        $error_message = "Failed to submit your vote. Please try again.";
    }
}

// Fetch suggestions again to get updated vote counts and voters
$stmt_suggestions->execute();
$suggestions_result = $stmt_suggestions->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggest Alternatives - <?php echo htmlspecialchars($event['event_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
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

<main class="container mt-5">
    <div class="card p-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
        <h1 class="text-center mb-4"><strong>Suggest Alternatives for: <?php echo htmlspecialchars($event['event_name']); ?></strong></h1>

        <!-- Informative Message -->
        <div class="alert alert-info">
            <strong>Note:</strong> The most voted suggested location and event time will be adopted as the new official event details if the organizer agrees.
        </div>

        <!-- Display Event Details -->
        <div class="card mb-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);"">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($event['event_name']); ?></h5>
                <p class="card-text"><strong>Current Time:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                <p class="card-text"><strong>Current Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            </div>
        </div>

        <!-- Display Existing Suggestions -->
        <div class="mb-4">
            <h3>Existing Suggestions</h3>
            <?php if ($suggestions_result->num_rows > 0): ?>
                <?php while ($suggestion = $suggestions_result->fetch_assoc()): ?>
                    <div class="card mb-3 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                        <div class="card-body">
                            <p><strong>Suggested By:</strong> <?php echo htmlspecialchars($suggestion['suggested_by_username']); ?></p>
                            <?php if ($suggestion['suggested_date']): ?>
                                <p><strong>Suggested Date:</strong> <?php echo htmlspecialchars($suggestion['suggested_date']); ?></p>
                            <?php endif; ?>
                            <?php if ($suggestion['suggested_location']): ?>
                                <p><strong>Suggested Location:</strong> <?php echo htmlspecialchars($suggestion['suggested_location']); ?></p>
                            <?php endif; ?>

                            <!-- Vote Button -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="vote_suggestion_id" value="<?php echo htmlspecialchars($suggestion['id']); ?>">
                                <button type="submit" class="btn btn-success">Vote</button>
                            </form>

                            <!-- Display List of Users Who Voted for This Suggestion -->
                            <div class="mt-3">
                                <strong>Votes:</strong>
                                <?php
                                // Fetch votes for this suggestion
                                $sql_votes = "SELECT m.username FROM suggestion_votes sv
                                              JOIN members m ON sv.voted_by = m.id
                                              WHERE sv.suggestion_id = ?";
                                $stmt_votes = $conn->prepare($sql_votes);
                                $stmt_votes->bind_param("i", $suggestion['id']);
                                $stmt_votes->execute();
                                $votes_result = $stmt_votes->get_result();

                                if ($votes_result->num_rows > 0): ?>
                                    <ul class="list-group mt-2">
                                        <?php while ($vote = $votes_result->fetch_assoc()): ?>
                                            <li class="list-group-item"><?php echo htmlspecialchars($vote['username']); ?> voted for this suggestion</li>
                                        <?php endwhile; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">No votes yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No suggestions available for this event.</p>
            <?php endif; ?>
        </div>

        <!-- Suggest Alternative Form -->
        <div class="card p-4 mb-4 border-0" style="border-radius: 10px;box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
            <h3>Suggest an Alternative</h3>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="submit_suggestion" value="1">
                <div class="mb-3">
                    <label for="suggestedDate" class="form-label">Suggest a New Date and Time</label>
                    <input type="datetime-local" class="form-control" id="suggestedDate" name="suggested_date">
                </div>
                <div class="mb-3">
                    <label for="suggestedLocation" class="form-label">Suggest a New Location</label>
                    <input type="text" class="form-control" id="suggestedLocation" name="suggested_location" placeholder="Enter new location">
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Suggestion</button>
            </form>
        </div>

        <a href="events.php" class="btn btn-secondary mt-3">Back to Events</a>
    </div>
</main>

<!-- Bootstrap boilerplate -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" 
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" 
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>




