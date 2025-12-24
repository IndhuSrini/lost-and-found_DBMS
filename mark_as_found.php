<?php
session_start();
include('fetch_admin.php');
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_and_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if item ID is passed
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    
    // Update the item status to 'found'
    $sql = "UPDATE lost_items SET status = 'Found' WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $item_id);
        if ($stmt->execute()) {
            header("Location: lost_items_admin.php?status=found");
            exit;
        } else {
            echo "Error updating item status.";
        }
        $stmt->close();
    } else {
        echo "Error preparing SQL statement.";
    }
} else {
    echo "Item ID not provided.";
}

$conn->close();
?>
