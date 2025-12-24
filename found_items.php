<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all found items from database
$sql = "SELECT item_name, last_seen_location, image_path, reported_by_email FROM found_items ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Found Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background-color: #333;
            padding: 15px;
            height: 100vh;
            position: fixed;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }

        h2 {
            text-align: center;
        }

        .item-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .item-card {
            background-color: #fff;
            width: 250px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .item-card:hover {
            transform: scale(1.03);
        }

        .item-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-details {
            padding: 15px;
        }

        .item-details h3 {
            margin: 0 0 10px;
        }

        .item-details p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="lost_items.php">Lost Items</a>
    <a href="found_items.php">Found Items</a>
    <a href="report_lost.php">Report Lost</a>
    <a href="report_found.php">Report Found</a>
    <a href="profile.php">Profile</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h2>Found Items</h2>

    <div class="item-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Item Image">
                    <div class="item-details">
                        <h3><?= htmlspecialchars($row['item_name']) ?></h3>
                        <p><strong>Last Seen Location:</strong><br><?= htmlspecialchars($row['last_seen_location']) ?></p>
                        <p><strong>Reported By:</strong><br><?= htmlspecialchars($row['reported_by_email']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No found items reported yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
