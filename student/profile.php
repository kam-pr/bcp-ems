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

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

// Fetch current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program = $_POST['program'];
    $section = $_POST['section'];
    $profile_picture = $user['profile_picture']; // Existing profile picture path

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/'; // Use the correct relative path
        $file_name = basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $file_name;
    
        // Move uploaded file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            // Save the relative path to the database for use in the HTML
            $profile_picture = 'uploads/' . $file_name;
        }
    }
    

    // Update user data in the database
    $update_sql = "UPDATE users SET program = ?, section = ?, profile_picture = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $program, $section, $profile_picture, $user_id);
    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
        // Refresh the page to reflect changes
        header("Location: profile.php");
        exit;
    } else {
        echo "<script>alert('Failed to update profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <div class="container">
        <?php include 'sidepanel.php'; ?>

        <div class="content">
            <h1>Edit Profile</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <?php
                /* 
                <div>
                    <label for="profile_picture">Profile Picture</label>
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" width="100">
                    <?php endif; ?>
                    <input type="file" id="profile_picture" name="profile_picture">
                </div>
                */
                ?>
                <div>
                    <label for="student_no">Username</label><p id="student_no"><?= htmlspecialchars($user['student_no']) ?></p>
                </div>
                <div>
                    <label for="full_name">Full Name</label><p id="full_name"><?= htmlspecialchars($user['full_name']) ?></p>
                </div>
                <div>
                    <label for="email">Email</label><p id="email"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div>
                    <label for="email">Role</label><p id="email"><?= htmlspecialchars($user['role']) ?></p>
                </div>
                <div>
                    <label for="program">Program</label>
                    <select id="program" name="program" style="padding: 8px; width: 100%; margin: 0 0 5px 0; border: 1px solid #ccc; border-radius: 4px;" required>
                        <option value="" <?= empty($user['program']) ? 'selected' : '' ?>>Select program</option>
                        <option value="BSBA" <?= $user['program'] == 'BSBA' ? 'selected' : '' ?>>BSBA</option>
                        <option value="BSCrim" <?= $user['program'] == 'BSCrim' ? 'selected' : '' ?>>BSCrim</option>
                        <option value="BEED" <?= $user['program'] == 'BEED' ? 'selected' : '' ?>>BEED</option>
                        <option value="BSED" <?= $user['program'] == 'BSED' ? 'selected' : '' ?>>BSED</option>
                        <option value="BSHRM" <?= $user['program'] == 'BSHRM' ? 'selected' : '' ?>>BSHRM</option>
                        <option value="BSIT" <?= $user['program'] == 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                        <option value="BSOA" <?= $user['program'] == 'BSOA' ? 'selected' : '' ?>>BSOA</option>
                        <option value="BSCpE" <?= $user['program'] == 'BSCpE' ? 'selected' : '' ?>>BSCpE</option>
                    </select>
                </div>
                <div>
                    <label for="section">Section</label>
                    <input type="text" id="section" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="scripts/burger.js"></script>
</body>
</html>
