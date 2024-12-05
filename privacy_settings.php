<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN - Privacy Settings</title>
    <!--Bootstrap boilerplate -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
        <?php
        require 'config/db.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Handle form submission to update privacy settings
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hide_firstname = isset($_POST['hide_firstname']) ? 1 : 0;
            $hide_lastname = isset($_POST['hide_lastname']) ? 1 : 0;
            $hide_email = isset($_POST['hide_email']) ? 1 : 0;
            $hide_address = isset($_POST['hide_address']) ? 1 : 0;
            $hide_region = isset($_POST['hide_region']) ? 1 : 0;
            $hide_dob = isset($_POST['hide_dob']) ? 1 : 0;
            $hide_age = isset($_POST['hide_age']) ? 1 : 0;
            $hide_profession = isset($_POST['hide_profession']) ? 1 : 0;

            $update_privacy_sql = "INSERT INTO member_privacy (member_id, hide_firstname, hide_lastname, hide_email, hide_address, hide_region, hide_dob, hide_age, hide_profession)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE hide_firstname = VALUES(hide_firstname), hide_lastname = VALUES(hide_lastname), hide_email = VALUES(hide_email), hide_address = VALUES(hide_address), hide_region = VALUES(hide_region), hide_dob = VALUES(hide_dob), hide_age = VALUES(hide_age), hide_profession = VALUES(hide_profession)";
            
            $stmt = $conn->prepare($update_privacy_sql);
            $stmt->bind_param("iiiiiiiii", $user_id, $hide_firstname, $hide_lastname, $hide_email, $hide_address, $hide_region, $hide_dob, $hide_age, $hide_profession);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">Privacy settings updated successfully.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error updating privacy settings.</div>';
            }
        }

        // Fetch current privacy settings
        $sql_privacy = "SELECT hide_firstname, hide_lastname, hide_email, hide_address, hide_region, hide_dob, hide_age, hide_profession FROM member_privacy WHERE member_id = ?";
        $stmt_privacy = $conn->prepare($sql_privacy);
        $stmt_privacy->bind_param("i", $user_id);
        $stmt_privacy->execute();
        $privacy_result = $stmt_privacy->get_result();
        $privacy_settings = $privacy_result->fetch_assoc();
        ?>

        <div class="container mt-5">
            <div class="card p-4 shadow-sm">
                <h2 class="mb-4">Privacy Settings</h2>
                <form method="POST">
                    <!-- Hide Firstname -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_firstname" name="hide_firstname" <?php if (!empty($privacy_settings['hide_firstname'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_firstname">
                            Hide First Name
                        </label>
                    </div>

                    <!-- Hide Lastname -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_lastname" name="hide_lastname" <?php if (!empty($privacy_settings['hide_lastname'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_lastname">
                            Hide Last Name
                        </label>
                    </div>

                    <!-- Hide Email -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_email" name="hide_email" <?php if (!empty($privacy_settings['hide_email'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_email">
                            Hide Email
                        </label>
                    </div>

                    <!-- Hide Address -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_address" name="hide_address" <?php if (!empty($privacy_settings['hide_address'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_address">
                            Hide Address
                        </label>
                    </div>

                    <!-- Hide Region -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_region" name="hide_region" <?php if (!empty($privacy_settings['hide_region'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_region">
                            Hide Region
                        </label>
                    </div>

                    <!-- Hide Date of Birth -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_dob" name="hide_dob" <?php if (!empty($privacy_settings['hide_dob'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_dob">
                            Hide Date of Birth
                        </label>
                    </div>

                    <!-- Hide Age -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_age" name="hide_age" <?php if (!empty($privacy_settings['hide_age'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_age">
                            Hide Age
                        </label>
                    </div>

                    <!-- Hide Profession -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hide_profession" name="hide_profession" <?php if (!empty($privacy_settings['hide_profession'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="hide_profession">
                            Hide Profession
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-3 w-100">Save Settings</button>
                </form>

                <!-- Go Back Button -->
                <a href="profile.php" class="btn btn-secondary mt-4 w-100">Go Back</a>
            </div>
        </div>
    </main>

    <!--Bootstrap boilerplate -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
