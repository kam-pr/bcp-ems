<?php
require_once 'connection.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Logic to send a reset link (example for demonstration)
            // You'd normally generate a unique token and email it to the user

            $reset_token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
            $stmt->bind_param("ss", $reset_token, $email);
            $stmt->execute();

            // Assume a fake URL for reset (replace with actual email-sending logic)
            $reset_link = "http://yourdomain.com/reset-password.php?token=$reset_token";

            // Display a success message
            $message = "A password reset link has been sent to your email. <a href='$reset_link'>Reset Password</a>";
        } else {
            $message = "Email not found in the system.";
        }
    } else {
        $message = "Please enter a valid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCP - Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <img src="images/bcp-logo.png" alt="Logo">
        <h3>Reset Password</h3>
        <form method="POST" action="forgot-password.php">
            <input placeholder="Enter your registered email" name="email" type="email" required />
            <button type="submit">Send Reset Link</button>
        </form>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
