<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCP - Events Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <img src="images/bcp-logo.png" alt="Logo">
        <h3>Event Management System</h3>
        <form method="POST" action="login.php">
            <input placeholder="Username" name="student_no" type="text" required />
            <input placeholder="Password" name="password" type="password" required />
            <button type="submit">Sign In</button>
            <div class="links">
                <a href="create-account.php">Create Account</a> | <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>
