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
$program = $_SESSION['program'] ?? '';
$section = $_SESSION['section'] ?? '';

$sql = "SELECT id, title, start_time, end_time, venue FROM events ORDER BY start_time ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>EMS - Register</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/student.css">

</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <div class="header-row">
                <h1>Event Schedules</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search events..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>
            
            <div class="card-container">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars($row['title']) ?></h3>
                            <p><strong>Venue:</strong> <?= htmlspecialchars($row['venue']) ?></p>
                            <p><strong>Start Time:</strong> <?= htmlspecialchars(date("M d, Y @ h:iA", strtotime($row['start_time']))) ?></p>
                            <p><strong>End Time:</strong> <?= htmlspecialchars(date("M d, Y @ h:iA", strtotime($row['end_time']))) ?></p>
                            <div class="actions">
                                <a href="registration.php?event_id=<?= $row['id'] ?>" class="btn">Register</a>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No events found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.getElementById("searchButton").addEventListener("click", function () {
        const filter = document.getElementById("searchInput").value.toLowerCase();
        const cards = document.querySelectorAll(".card-container .card");

        cards.forEach(card => {
            const title = card.querySelector("h3").innerText.toLowerCase();
            card.style.display = title.includes(filter) ? "" : "none";
        });
    });

    document.getElementById("searchInput").addEventListener("keyup", function () {
        document.getElementById("searchButton").click();
    });
    </script>
</body>
</html>
