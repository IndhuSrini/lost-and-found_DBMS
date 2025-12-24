
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get current status
    $check = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $new_status = $user['status'] === 'suspended' ? 'active' : 'suspended';

        // Update status
        $update = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $update->bind_param("si", $new_status, $id);
        $update->execute();
    }
}

$conn->close();
header("Location: users.php");
exit;
