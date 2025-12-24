<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete->bind_param("i", $id);

    if ($delete->execute()) {
        if ($delete->affected_rows > 0) {
            $_SESSION['msg'] = "✅ User deleted successfully.";
        } else {
            $_SESSION['msg'] = "❌ No user found with the given ID.";
        }
    } else {
        $_SESSION['msg'] = "❌ Error: " . $delete->error;
    }

    $delete->close();
}

$conn->close();
header("Location: users.php");
exit;
?>
