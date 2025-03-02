<?php
require_once 'connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = $_POST['student_no'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (student_no, full_name, email, password, phone, role)
            VALUES ('$student_no', '$full_name', '$email', '$hashed_password', '$phone', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Account created successfully! Redirecting to homepage...');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    } else {
        if (strpos($conn->error, 'Duplicate entry') !== false) {
            $error_message = "This email is already registered.";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <img src="images/bcp-logo.png" alt="Logo">
        <form method="POST" action="">
            <input id="student-no" name="student_no" type="text" placeholder="Username" required />
            <input id="full-name" name="full_name" type="text" placeholder="Full Name" required />
            <input id="email" name="email" type="email" placeholder="Email Address" required />
            <input id="password" name="password" type="password" placeholder="Password" required />
            <input id="phone" name="phone" type="tel" placeholder="Phone" required />
            <select id="role" name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="Student">Student</option>
                <option value="Admin">Admin</option>
                <option value="Admin Staff">Admin Staff</option>
                <option value="Super Admin">Super Admin</option>
            </select>
            <button type="submit">Create Account</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
