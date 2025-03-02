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

$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

// Fetch payment data
$sql = "SELECT 
            u.full_name AS name, 
            u.program, 
            u.section, 
            p.payment_method, 
            p.amount,
            p.status,
            p.created_at 
        FROM payments p 
        JOIN users u ON p.user_id = u.id";

$result = $conn->query($sql);

$payments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Payment Statement</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
    <?php include 'scripts/sidebar.php';?>

        <div class="content">
            <div class="header-row">
                <h1>Payment Statement</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search payments..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="button-container">
                <button class="btn-add-event" style="border: none;">Sort by Paid</button>
            </div>

            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars($payment['name']) ?></td>
                                <td><?= htmlspecialchars($payment['program']) ?></td>
                                <td><?= htmlspecialchars($payment['section']) ?></td>
                                <td><?= htmlspecialchars($payment['status']) ?></td>
                                <td><?= htmlspecialchars(date("h:iA M d, Y", strtotime($payment['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No payment records found.</td>
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
