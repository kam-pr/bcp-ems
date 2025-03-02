<?php
require_once 'connection.php';

session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = $_POST['student_no']; 
    $password = $_POST['password']; 

    $sql = "SELECT * FROM users WHERE student_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_no); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];

            switch ($user['role']) {
                case 'Student':
                    header("Location: student/dashboard.php");
                    break;
                case 'Admin Staff':
                    header("Location: admin/dashboard.php");
                    break;
                case 'Admin':
                case 'Super Admin':
                    header("Location: admin.v2/dashboard.php");
                    break;
                default:
                    echo "<script>alert('Invalid role');</script>";
            }
            
            exit;
        } else {
            echo "<script>alert('Invalid password');</script>";
            echo "<script>window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Student number not found');</script>";
        echo "<script>window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
