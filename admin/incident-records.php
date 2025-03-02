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

$query = "SELECT u.full_name, u.program, u.section, i.incident_description, i.status, i.date_reported
          FROM incidents i
          JOIN users u ON i.user_id = u.id";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Incident Report</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
    <?php include 'scripts/sidebar.php';?>

        <div class="content">
                <div class="header-row">
                    <h1>Incident Report</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search reports..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="button-container">
                <button class="btn-add-event" style="border: none;">Sort by</button>
            </div>

            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Section</th>
                        <th>Incident</th>
                        <th>Status</th>
                        <th>Date Reported</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['program']) ?></td>
                                <td><?= htmlspecialchars($row['section']) ?></td>
                                <td><?= htmlspecialchars($row['incident_description']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars(date("h:iA M d, Y", strtotime($row['date_reported']))) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No incidents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="scripts/burger.js"></script>
    <script src="scripts/search.js"></script>
</body>
</html>
