<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'] === 'lost' ? 'lost_items' : ($_GET['type'] === 'found' ? 'found_items' : '');

    if ($type) {
        // Optional: delete image file from uploads folder (if needed)
        $getImageQuery = $conn->prepare("SELECT image_path FROM $type WHERE id = ?");
        $getImageQuery->bind_param("i", $id);
        $getImageQuery->execute();
        $result = $getImageQuery->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['image_path']) && file_exists($row['image_path'])) {
                unlink($row['image_path']); // Delete the file
            }
        }

        // Delete from table
        $stmt = $conn->prepare("DELETE FROM $type WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['item_msg'] = "✅ Item deleted successfully.";
        } else {
            $_SESSION['item_msg'] = "❌ Failed to delete item.";
        }

        $stmt->close();
    } else {
        $_SESSION['item_msg'] = "❌ Invalid item type.";
    }
} else {
    $_SESSION['item_msg'] = "❌ Invalid request.";
}

$conn->close();
header("Location: items.php");
exit;
