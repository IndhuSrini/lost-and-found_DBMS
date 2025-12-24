<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch lost items
$lost_items_query = "
    SELECT li.*, u.username, u.email 
    FROM lost_items li
    JOIN users u ON li.user_id = u.id
    ORDER BY li.created_at DESC
";
$lost_items = $conn->query($lost_items_query);

// Fetch found items
$found_items_query = "
    SELECT fi.*, u.username, u.email 
    FROM found_items fi
    JOIN users u ON fi.user_id = u.id
    ORDER BY fi.created_at DESC
";
$found_items = $conn->query($found_items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Items - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 220px;
            background-color: #f8f9fa;
            height: 100vh;
            padding: 20px;
            border-right: 1px solid #ddd;
            position: fixed;
        }
        .sidebar h4 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            padding: 10px 15px;
            margin-bottom: 5px;
            color: #000;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        /* Item Card Styles */
        .item-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-info {
            flex-grow: 1;
            padding-left: 10px;
        }

        .item-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .btn-danger {
            margin-left: 10px;
        }

        h4.text-primary {
            margin-top: 30px;
        }
    </style>
</head>
<body>

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
<div class="main-content">
    <h4 class="text-primary">🔍 Lost Items</h4>
    <?php if ($lost_items->num_rows > 0): ?>
        <?php while ($item = $lost_items->fetch_assoc()): ?>
            <div class="item-card">
                <img src="<?php echo $item['image_path'] ? $item['image_path'] : 'item_default.png'; ?>" alt="Item Image">
                <div class="item-info">
                    <h5><?php echo htmlspecialchars($item['item_name']); ?> (Lost)</h5>
                    <p>Location: <?php echo htmlspecialchars($item['last_seen_location']); ?> | Date: <?php echo $item['last_seen_datetime']; ?></p>
                    <p>Reported by: <?php echo htmlspecialchars($item['username']); ?> (<?php echo $item['email']; ?>)</p>
                </div>
                <a href="delete_item.php?id=<?php echo $item['id']; ?>&type=lost" class="btn btn-danger" onclick="return confirm('Delete this lost item?')">🗑️ Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No lost items found.</p>
    <?php endif; ?>

    <hr>

    <h4 class="text-primary">📦 Found Items</h4>
    <?php if ($found_items->num_rows > 0): ?>
        <?php while ($item = $found_items->fetch_assoc()): ?>
            <div class="item-card">
                <img src="<?php echo $item['image_path'] ? $item['image_path'] : 'item_default.png'; ?>" alt="Item Image">
                <div class="item-info">
                    <h5><?php echo htmlspecialchars($item['item_name']); ?> (Found)</h5>
                    <p>Location: <?php echo htmlspecialchars($item['last_seen_location']); ?> | Date: <?php echo $item['found_datetime']; ?></p>
                    <p>Reported by: <?php echo htmlspecialchars($item['username']); ?> (<?php echo $item['email']; ?>)</p>
                </div>
                <a href="delete_item.php?id=<?php echo $item['id']; ?>&type=found" class="btn btn-danger" onclick="return confirm('Delete this found item?')">🗑️ Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No found items submitted yet.</p>
    <?php endif; ?>
</div>

<!-- Optional Bootstrap JS for confirmation prompt, etc. -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
