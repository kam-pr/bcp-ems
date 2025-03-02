<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="side-panel" id="sidePanel">
        <div class="spnl-header">
            <img src="../images/bcp-logo.png" alt="Logo">
            <h2 class="title-spnl">BCP - EMS</h2>
        </div>

        <div class="profile">
            <img src="../images/profile-placeholder.jpg" alt="Profile Picture">
            <p class="role"><?= htmlspecialchars($role) ?></p> <!-- Display user role -->
            <p class="email"><?= htmlspecialchars($email) ?></p> 
        </div>

        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="approval.php">Approval</a>
            <a href="faculty.php">Faculty</a>
            <a class="logout" href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>