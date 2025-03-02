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
    <title>EMS - Instructions</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/student.css">
    <style>
        .instructions-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            text-align: center;
        }
        .instructions-container h2 {
            color: #333;
        }
        .instructions-container p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
        }
        .register-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2E3538;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }
        .register-btn:hover {
            background-color:#2E3538;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <div class="instructions-container">
                <h2>Instructions</h2>
                <p><strong>Step 1:</strong> Fill up the form</p>
                <p><strong>Step 2:</strong> Wait for the confirmation</p>
                <p><strong>Step 3:</strong> The QR code will appear in your email account used to fill up the form</p>
                <p><strong>Step 4:</strong> Use the QR code at events</p>
                <p><strong>Note:</strong> This is a one-time registration for the whole semester. Please take care of your QR code to monitor the analytics of attendees. Thank you!</p>
            </div>
        </div>
    </div>

    <a href="registration.php" class="register-btn">Register</a>

    <script src="scripts/burger.js"></script>
</body>
</html>