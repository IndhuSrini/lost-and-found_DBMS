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

// Fetch lost items
$sql = "SELECT * FROM lost_items ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost Items - Admin Panel</title>
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
        img.item-img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
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
    <a href="user_report.php">User Report</a>
    <a href="edit_profile.php" class="active">Edit Profile</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="container">
    <h2 class="mb-4">Lost Items Reported by Users</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Last Seen Location</th>
                    <th>Last Seen Date & Time</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_seen_location']) ?></td>
                        <td><?= htmlspecialchars($row['last_seen_datetime']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                        <td>
                            <?php if (!empty($row['image_path'])): ?>
                                <img src="<?= '' . htmlspecialchars($row['image_path']) ?>" class="item-img" alt="Item Image">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_lost_item.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="mark_as_found.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Mark as Found</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No lost items reported yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
