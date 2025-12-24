<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status']; // Get new status (active or inactive)

    // Validate status value
    if ($status != 'active' && $status != 'inactive') {
        die("Invalid status value.");
    }

    // Update the user's status in the database
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

$conn->close();
header("Location: users.php");
exit;
?>
