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

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);

    // Delete related records first
    $conn->query("DELETE FROM attendance WHERE registration_id IN (SELECT id FROM registration WHERE event_id = $event_id)");
    $conn->query("DELETE FROM registration WHERE event_id = $event_id");

    // Delete the event
    $deleteEventQuery = "DELETE FROM events WHERE id = $event_id";
    
    if ($conn->query($deleteEventQuery) === TRUE) {
        echo "<script>alert('Event and related records successfully deleted.'); window.location.href='event-managenent.php';</script>";
    } else {
        echo "<script>alert('Error deleting event: " . $conn->error . "'); window.location.href='event-management.php';</script>";
    }
}

$conn->close();
?>
