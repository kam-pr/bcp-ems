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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Logistics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="container">
        <?php include 'scripts/sidebar.php'; ?>

        <div class="content">
            <div class="header-row">
                <h1>Logistics</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="logistics-table">
                <?php
                // Fetch logistics data
                $sql = "SELECT * FROM logistics";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Event ID</th><th>Item Name</th><th>Quantity</th><th>Status</th></tr>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["event_id"] . "</td>";
                        echo "<td>" . $row["item_name"] . "</td>";
                        echo "<td>" . $row["quantity"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No logistics data found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script src="scripts/burger.js"></script>
    <script src="scripts/search.js"></script>
</body>
</html>