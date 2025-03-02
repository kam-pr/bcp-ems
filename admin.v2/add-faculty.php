<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $department = $conn->real_escape_string($_POST['department']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "INSERT INTO faculty (full_name, department, email) VALUES ('$full_name', '$department', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Faculty added successfully!'); window.location.href='faculty.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>EMS - Faculty</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Add Faculty</h2>
        <form method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" required>
            
            <label for="department">Department:</label>
            <input type="text" name="department" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <button type="submit">Add Faculty</button>
        </form>
    </div>
</body>
</html>
