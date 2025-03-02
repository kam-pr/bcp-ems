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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="container">
    <?php include 'sidepanel.php'; ?>
        <div class="content">
            <h1>Welcome, <?= htmlspecialchars($full_name) ?>!</h1>
            <p>Here you can manage events, view attendees, and perform other administrative tasks.</p>
        </div>
    </div>

    <script src="scripts/burger.js"></script>
</body>
</html>
