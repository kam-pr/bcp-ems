<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$full_name = $_SESSION['full_name'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

// Get the count of events
$sql = "SELECT COUNT(*) AS event_count FROM events";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$event_count = $row['event_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <h1>Announcements & Upcoming Events</h1>

            <div class="announcements">
                <h1><?= $event_count ?></h1>
                <p>Upcoming Events</p>
            </div>

        </div>
    </div>

    <script src="scripts/burger.js"></script>
</body>
</html>