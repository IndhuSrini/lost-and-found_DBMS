<?php
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

$result = $conn->query("SELECT item_name, last_seen_location, last_seen_datetime, description, image_path, reported_by_email FROM lost_items ORDER BY last_seen_datetime DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lost Items</title>
    <style>
        body {
            font-family: Arial;
            background-color: #eef;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background-color: #333;
            color: white;
            height: 100vh;
            padding: 15px;
            position: fixed;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 8px #ccc;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
        }

        .card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }

        .card-details {
            flex: 1;
        }

        .card-details h3 {
            margin-top: 0;
        }

        .card-details p {
            margin: 5px 0;
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
    <h2>Lost Items Reported</h2>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Lost Item Image">
            <div class="card-details">
                <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                <p><strong>Last Seen:</strong> <?php echo htmlspecialchars($row['last_seen_location']); ?></p>
                <p><strong>Date & Time:</strong> <?php echo htmlspecialchars($row['last_seen_datetime']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Reported By:</strong> <?php echo htmlspecialchars($row['reported_by_email']); ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
