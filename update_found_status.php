<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_and_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['resolve'])) {
    $item_id = intval($_POST['item_id']);

    $update = $conn->prepare("UPDATE found_items SET status = 'resolved' WHERE id = ?");
    $update->bind_param("i", $item_id);

    if ($update->execute()) {
        header("Location: found_items_admin.php?msg=updated");
    } else {
        echo "Error updating status.";
    }
}
?>
