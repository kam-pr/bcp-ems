<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$full_name = $_SESSION['full_name'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $venue = $_POST['venue'] ?? '';

    if ($title && $start_time && $end_time && $venue) {
        $stmt = $conn->prepare("INSERT INTO events (title, start_time, end_time, venue) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $start_time, $end_time, $venue);

        if ($stmt->execute()) {
            $userQuery = "SELECT email FROM users";
            $userResult = $conn->query($userQuery);

            if ($userResult->num_rows > 0) {
                while ($userRow = $userResult->fetch_assoc()) {
                    $to = $userRow['email'];
                    $subject = "New Event: $title";
                    $message = "
                        <html>
                        <head>
                            <title>New Event Notification</title>
                        </head>
                        <body>
                            <h2>A new event has been added!</h2>
                            <p><strong>Event:</strong> $title</p>
                            <p><strong>Start Time:</strong> $start_time</p>
                            <p><strong>End Time:</strong> $end_time</p>
                            <p><strong>Venue:</strong> $venue</p>
                            <p>Check your dashboard for more details.</p>
                        </body>
                        </html>
                    ";

                    // Set headers
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: dgabay3878qc@student.fatima.edu.ph" . "\r\n"; // Change this to your email

                    // Send email
                    mail($to, $subject, $message, $headers);
                }
            }

            // Redirect after adding the event
            header("Location: event-management.php?success=1");
            exit;
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Add New Event</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/add.css">
</head>
<body>
    <div class="container">
    <?php include 'scripts/sidebar.php';?>

        <div class="content">
            <h1>Add New Event</h1>
            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>

            <form method="POST" action="add-event.php" class="event-form">
                <div class="form-group">
                    <label for="title">Event Name</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="datetime-local" id="start_time" name="start_time" required>
                </div>

                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="datetime-local" id="end_time" name="end_time" required>
                </div>

                <div class="form-group">
                    <label for="venue">Venue</label>
                    <input type="text" id="venue" name="venue" required>
                </div>

                <div>
                    <button type="submit" class="btn-submit">Add Event</button>
                    <a href="event-management.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="scripts/burger.js"></script>
</body>
</html>
