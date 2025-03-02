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

// Fetch registration records joined with events table
$sql = "
    SELECT 
        r.event_id, 
        r.payment_receipt, 
        r.status, 
        r.created_at, 
        e.title AS event_name
    FROM 
        registration r
    JOIN 
        events e
    ON 
        r.event_id = e.id
    WHERE 
        r.full_name = ?
    ORDER BY 
        r.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $full_name);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Records</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <div class="menu-icon" id="menuIcon"><span>&#9776;</span></div>
            <h1>Event Records</h1>
            
            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['event_name']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars(date("F j, Y @ g:i a", strtotime($row['created_at']))) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="scripts/burger.js"></script>
</body>
</html>
