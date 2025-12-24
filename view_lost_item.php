<?php
session_start();
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

// Fetch the lost item details
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $sql = "SELECT * FROM lost_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
} else {
    echo "Item not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Lost Item - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            background-color: #f8f9fa;
            padding-top: 30px;
            border-right: 1px solid #dee2e6;
        }
        .sidebar h4 {
            text-align: center;
            font-weight: bold;
            color: #0d6efd;
        }
        .sidebar a {
            display: block;
            color: #000;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .container {
            margin-left: 240px; /* Adjust to leave space for sidebar */
            padding: 30px;
        }
        .item-img {
            max-width: 300px;
            max-height: 300px;
            object-fit: cover;
        }
        .item-details {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #0d6efd;
            color: white;
            border-radius: 5px;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Admin Panel</h4>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="lost_items_admin.php">Lost Items</a>
    <a href="found_items_admin.php">Found Items</a>
    <a href="users.php">Manage Users</a>
    <a href="items.php">Manage Items</a>
    <a href="reports.php">Reports</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="container">
    <h2 class="mb-4">View Lost Item Details</h2>
    <?php if ($item): ?>
        <div class="item-details">
            <h4 class="mb-3"><?= htmlspecialchars($item['item_name']) ?></h4>
            <div class="row">
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" class="item-img" alt="Item Image">
                </div>
                <div class="col-md-8">
                    <p><strong>Last Seen Location:</strong> <?= htmlspecialchars($item['last_seen_location']) ?></p>
                    <p><strong>Last Seen Date & Time:</strong> <?= htmlspecialchars($item['last_seen_datetime']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>
                </div>
            </div>
            <a href="mark_as_found.php?id=<?= $item['id'] ?>" class="btn btn-custom mt-3">Mark as Found</a>
        </div>
    <?php else: ?>
        <p>Item details not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
