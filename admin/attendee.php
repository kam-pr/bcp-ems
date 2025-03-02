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

// Fetch attendance count, event name, and program
$sql = "
    SELECT e.title, r.program, COUNT(*) AS count 
    FROM registration r
    JOIN events e ON r.event_id = e.id
    GROUP BY e.title, r.program
";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[$row['title']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>Event Analytics</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
    <style>
        .event-container {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
        }
        .event-title {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            color: #333;
            margin-bottom: 10px;
        }
        .program-row {
            display: flex;
            justify-content: lefts;
            gap: 10px;
            flex-wrap: wrap;
        }
        .program-box {
            text-align: center;
            min-width: 60px;
            background:rgb(221, 238, 255);
            padding: 5px;
            border-radius: 4px;
        }
        .program-name {
            font-size: 14px;
            font-weight: bold;
            color: #2E3538;
        }
        .attendee-count {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'scripts/sidebar.php';?>

        <div class="content">
            <div class="header-row">
                <h1>Event Analytics</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search events..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <?php foreach ($events as $event_name => $programs): ?>
                <div class="event-container">
                    <div class="event-title"><?php echo htmlspecialchars($event_name); ?></div>
                    
                    <div class="program-row">
                        <?php foreach ($programs as $program): ?>
                            <div class="program-box">
                                <div class="program-name"><?php echo htmlspecialchars($program['program']); ?></div>
                                <div class="attendee-count"><?php echo $program['count']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <script src="scripts/burger.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const eventContainers = document.querySelectorAll(".event-container");

        searchInput.addEventListener("keyup", function () {
            let searchQuery = searchInput.value.toLowerCase().trim();

            eventContainers.forEach(event => {
                const eventTitle = event.querySelector(".event-title").textContent.toLowerCase();
                
                if (eventTitle.includes(searchQuery)) {
                    event.style.display = "block";
                } else {
                    event.style.display = "none";
                }
            });
        });
    });
    </script>
</body>
</html>
