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

// Fetch found items
$sql = "SELECT id, item_name, last_seen_location, found_datetime, description, image_path, COALESCE(status, 'pending') as status FROM found_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Found Items - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
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
            margin-bottom: 30px;
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
        .main {
            margin-left: 220px;
            padding: 30px;
            background-color: #f1f3f5;
            min-height: 100vh;
        }
        .table-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        img.item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Admin Dashboard</h4>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="lost_items_admin.php">Lost Items</a>
    <a href="found_items_admin.php">Found Items</a>
    <a href="users.php">Manage Users</a>
    <a href="items.php">Manage Items</a>
    <a href="reports.php"><strong>Reports</strong></a>
    <a href="user_report.php">User Report</a>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="table-container">
        <h2>Found Items</h2>
        <table class="table table-hover table-bordered mt-4">
            <thead class="table-primary">
                <tr>
                    <th>Item Name</th>
                    <th>Last Seen Location</th>
                    <th>Found Date/Time</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_seen_location']); ?></td>
                            <td><?php echo htmlspecialchars($row['found_datetime']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <?php if (!empty($row['image_path']) && file_exists($row['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Item Image" class="item-img">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $status = $row['status'] ?? 'pending'; // If NULL, treat as pending
                                    if ($status === 'pending') : ?>
                                    <form action="update_found_status.php" method="post">
                                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="resolve" class="btn btn-warning btn-sm">Pending</button>
                                    </form>
                                <?php elseif ($status === 'resolved') : ?>
                                    <span class="badge bg-success">Resolved</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Unknown</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No found items available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
