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

// Get current month and year from query parameters or default to the current date
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Handle month/year overflow/underflow
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// Fetch faculty members
$faculty_query = "SELECT * FROM faculty";
$faculty_result = $conn->query($faculty_query);

// Fetch venues
$venues_query = "SELECT * FROM venues";
$venues_result = $conn->query($venues_query);

// Fetch reservations
$reservations_query = "SELECT * FROM reservations WHERE MONTH(start_time) = $month AND YEAR(start_time) = $year";
$reservations_result = $conn->query($reservations_query);

// Prepare reservations for display
$reservations = [];
if ($reservations_result->num_rows > 0) {
    while ($row = $reservations_result->fetch_assoc()) {
        $date = date("Y-m-d", strtotime($row['start_time']));
        $reservations[$date][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Availability</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="container">
        <?php include 'scripts/sidebar.php';?>

        <div class="content" style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start; padding: 20px; gap: 20px;">
                <h1>Venue Availability</h1>
            <div class="calendar">
                <div class="month-navigation">
                    <a href="?month=<?= $month - 1 ?>&year=<?= $year ?>">&#8592; Previous</a>
                    <h2 style="display: inline;"><?= date("F Y", mktime(0, 0, 0, $month, 1, $year)) ?></h2>
                    <a href="?month=<?= $month + 1 ?>&year=<?= $year ?>">Next &#8594;</a>
                </div>
                <div class="days-row">
                    <div>Monday</div>
                    <div>Tuesday</div>
                    <div>Wednesday</div>
                    <div>Thursday</div>
                    <div>Friday</div>
                    <div>Saturday</div>
                    <div>Sunday</div>
                </div>
                <div class="dates-row">
                    <?php
                    $start_date = strtotime("$year-$month-01");
                    $end_date = strtotime("last day of", $start_date);
                    $today = date("Y-m-d");

                    // Add padding for empty days before the first day of the month
                    $first_day_of_month = date("N", $start_date); // 1 (Monday) - 7 (Sunday)
                    for ($i = 1; $i < $first_day_of_month; $i++) {
                        echo '<div class="empty"></div>';
                    }

                    for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                        $formatted_date = date("Y-m-d", $date);
                        $is_reserved = isset($reservations[$formatted_date]); // Use correct variable
                    
                        echo '<div class="' . ($is_reserved ? 'reserved' : '') . '">';
                        echo '<strong>' . date("j", $date) . '</strong>';
                    
                        if ($is_reserved) {
                            foreach ($reservations[$formatted_date] as $event) {
                                echo '<span> Reserved </span>';
                            }
                        }
                    
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="reservation-form">
                <h2 style="text-align: center;">Event Reservation</h2>
                <form action="process-reservation.php" method="POST">
                    <input type="text" name="event_name" placeholder="Event Name" required>
                    <input type="text" name="venue" placeholder="Venue" required>
                    <select name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        <?php while ($faculty = $faculty_result->fetch_assoc()) : ?>
                            <option value="<?= $faculty['faculty_id'] ?>"><?= htmlspecialchars($faculty['full_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select name="setup" required>
                        <option value="">Setup</option>
                        <option value="Online">Online</option>
                        <option value="Face to Face">Face to Face</option>
                    </select>
                    <label for="start_time" style="font-size: 0.6em;">Start Time & Date</label>
                    <input type="datetime-local" id="start_time" name="start_time" required>
                    <label for="end_time" style="font-size: 0.6em;">End Time & Date</label>
                    <input type="datetime-local" id="end_time" name="end_time" required>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
        <a class="reschedule" href="reschedule.php">Reschedule</a>
    </div>

    <script src="scripts/burger.js"></script>
</body>
</html>
