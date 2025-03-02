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


$sql = "SELECT id, title, start_time, end_time, venue FROM events ORDER BY start_time DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>Event Management</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
    <?php include 'scripts/sidebar.php';?>

        <div class="content">
            <div class="header-row">
                <h1>Event Schedules</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search events..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="button-container">
                <a href="add-event.php" class="btn-add-event">Add New Event</a>
            </div>

            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Venue</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['venue']) ?></td>
                                <td><?= htmlspecialchars(date("h:iA M d, Y", strtotime($row['start_time']))) ?></td>
                                <td><?= htmlspecialchars(date("h:iA M d, Y", strtotime($row['end_time']))) ?></td>
                                <td>
                                    <a href="edit-event.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="delete-event.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No events found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <script src="scripts/burger.js"></script>
    <script src="scripts/search.js"></script>
</body>
</html>
