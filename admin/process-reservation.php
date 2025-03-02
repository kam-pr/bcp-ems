<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $faculty_id = (int)$_POST['faculty_id'];
    $venue = $conn->real_escape_string($_POST['venue']); // Changed from venue_id
    $setup = $conn->real_escape_string($_POST['setup']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Adjusted query to insert venue_name instead of venue_id
    $query = "INSERT INTO reservations (event_name, faculty_id, venue, setup, start_time, end_time) 
              VALUES ('$event_name', $faculty_id, '$venue', '$setup', '$start_time', '$end_time')";

    if ($conn->query($query) === TRUE) {
        header("Location: venue-availability.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
