<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

$full_name = $_SESSION['full_name'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registration_id'], $_POST['status'])) {
    $registration_id = intval($_POST['registration_id']);
    $status = in_array($_POST['status'], ['Pending', 'Approved', 'Rejected']) ? $_POST['status'] : 'Pending';

    $stmt = $conn->prepare("UPDATE registration SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $registration_id);
    $stmt->execute();
    $stmt->close();

    header("Location: approval.php");
    exit();
}

$query = "
    SELECT r.id, r.full_name, CONCAT(r.program, ' ', r.section) AS program_section, 
           r.payment_receipt, r.status, r.created_at, e.title
    FROM registration r
    LEFT JOIN events e ON r.event_id = e.id
    ORDER BY r.created_at DESC
";

$result = $conn->query($query);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>EMS - Records</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <div class="header-row">
                <h1>Approval</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search ..." />
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
            </div>
            
            <table class="event-table" id="eventTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Program & Section</th>
                        <th>Event Name</th>
                        <!--<th>Receipt</th>-->
                        <th>Status</th>
                        <th>Date of Registration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['program_section']) ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <!--<td>
                                    <a href="/bcp-ems/student/uploads/<?= htmlspecialchars($row['payment_receipt']) ?>" target="_blank">
                                        View Receipt
                                    </a>
                                </td>-->
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="registration_id" value="<?= $row['id'] ?>">
                                        <select name="status" style="padding: 4px; width: 100%;" onchange="this.form.submit()">
                                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $row['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                        <noscript><button type="submit">Update</button></noscript>
                                    </form>
                                </td>
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

    <script src="../admin/scripts/search.js"></script>
</body>
</html>
