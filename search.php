<?php
session_start();
require 'config/db.php'; // Include database connection

// Variables for the search results and messages
$search_results = [];
$message = '';

// Handle the search query when submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_keyword'], $_GET['search_category'])) {
    $keyword = '%' . $_GET['search_keyword'] . '%'; // Use wildcards for partial matching
    $category = $_GET['search_category'];

    if ($category === 'members') {
        // Search members by profession, region, username, or age
        $sql = "SELECT username, age, profession, region, status, firstname, lastname, privilege, dob, address, email 
                FROM members 
                WHERE profession LIKE ? OR region LIKE ? OR username LIKE ? OR age LIKE ? OR status LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR privilege LIKE ? OR dob LIKE ? OR address LIKE ? OR email LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
    } elseif ($category === 'groups') {
        // Search groups by interest or name
        $sql = "SELECT name, description, interest 
                FROM groups 
                WHERE name LIKE ? OR interest LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
    } else {
        $message = 'Invalid category selected.';
    }

    if (!empty($stmt)) {
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch results
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $search_results[] = $row;
            }
        } else {
            $message = 'No matches found for your search combination.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Search</title>
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

    <main class="container mt-5">
        <!-- Search Form -->
        <div class="card p-4 shadow-sm border-0" style="border-radius: 10px;">
            <h2 class="mb-4 text-center"><strong>Search Members or Groups</strong></h2>
            <form method="GET" action="search.php" class="row g-3">
                <div class="col-md-8">
                    <label for="search_keyword" class="form-label">Enter a keyword:</label>
                    <input type="text" id="search_keyword" name="search_keyword" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="search_category" class="form-label">Category:</label>
                    <select id="search_category" name="search_category" class="form-select" required>
                        <option value="members">Members</option>
                        <option value="groups">Groups</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>

        <!-- Display Results -->
        <div class="card p-4 shadow-sm mt-4 border-0" style="border-radius: 10px;">
            <h3 class="mb-4 text-center"><strong>Search Results</strong></h3>
            <?php if (!empty($search_results)): ?>
                <ul class="list-group">
                    <?php if ($_GET['search_category'] === 'members'): ?>
                        <?php foreach ($search_results as $member): ?>
                            <li class="list-group-item mb-3">
                                <strong>Username:</strong> <?php echo htmlspecialchars($member['username']); ?><br>
                                <strong>Age:</strong> <?php echo htmlspecialchars($member['age']); ?><br>
                                <strong>Profession:</strong> <?php echo htmlspecialchars($member['profession']); ?><br>
                                <strong>Region:</strong> <?php echo htmlspecialchars($member['region']); ?><br>
                                <strong>Status:</strong> <?php echo htmlspecialchars($member['status']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php elseif ($_GET['search_category'] === 'groups'): ?>
                        <?php foreach ($search_results as $group): ?>
                            <li class="list-group-item mb-3">
                                <strong>Group Name:</strong> <?php echo htmlspecialchars($group['name']); ?><br>
                                <strong>Description:</strong> <?php echo htmlspecialchars($group['description']); ?><br>
                                <strong>Interest:</strong> <?php echo htmlspecialchars($group['interest']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-warning text-center"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>


