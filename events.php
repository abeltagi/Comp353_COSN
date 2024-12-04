<?php
// Include the database connection file
include 'config/db.php';

// Check the database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Event Management</h1>
        
        <!-- Future Events Section -->
        <div class="mb-4">
            <h3>Future Events</h3>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Christmas Party</h5>
                    <p class="card-text"><strong>Time:</strong> 2024-12-25 12:00</p>
                    <p class="card-text"><strong>Location:</strong> My House</p>
                    <p class="card-text"><strong>Description:</strong> Merry Christmas</p>
                    <a href="#" class="btn btn-primary">Event Details</a>
                </div>
            </div>
        </div>

        <!-- Create Event Button -->
        <div class="text-center mb-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEventModal">Create Event</button>
        </div>

        <!-- Past Events Section -->
        <div>
            <h3>Past Events</h3>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">My Dog's Wedding</h5>
                    <p class="card-text"><strong>Time:</strong> 2024-11-01 10:30</p>
                    <p class="card-text"><strong>Location:</strong> Local Dog Park</p>
                    <p class="card-text"><strong>Description:</strong> Max and Bella are getting married and having puppies!</p>
                    <a href="#" class="btn btn-danger">Delete</a>
                </div>
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
                    <form>
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="eventName" placeholder="Enter event name">
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="eventDescription" rows="3" placeholder="Enter event description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="eventDate" class="form-label">Date and Time</label>
                            <input type="datetime-local" class="form-control" id="eventDate">
                        </div>
                        <div class="mb-3">
                            <label for="eventLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="eventLocation" placeholder="Enter location">
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
