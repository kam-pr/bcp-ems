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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>EMS - Faculty</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <div class="header-row">
                <h1>Registered Faculties</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search events..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="button-container">
                <a href="add-faculty.php" class="btn-add-event">Add Faculty</a>
            </div>

            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT full_name, department, email FROM faculty";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['department']}</td>
                                    <td>{$row['email']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No faculty registered yet.</td></tr>";
                    }

                    // Now close the connection after executing queries
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../admin/scripts/search.js"></script>
</body>
</html>
