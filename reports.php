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

// Count data
$totalLost = $conn->query("SELECT COUNT(*) AS count FROM lost_items")->fetch_assoc()['count'];
$totalFound = $conn->query("SELECT COUNT(*) AS count FROM found_items")->fetch_assoc()['count'];
$totalPending = $conn->query("SELECT COUNT(*) AS count FROM found_items WHERE status = 'Pending'")->fetch_assoc()['count'];
$totalResolved = $conn->query("SELECT COUNT(*) AS count FROM found_items WHERE status = 'Resolved'")->fetch_assoc()['count'];

// Fetch all item details
$itemsData = $conn->query("
    SELECT 'Lost' AS type, id, item_name, last_seen_location, last_seen_datetime AS datetime, description, image_path, status 
    FROM lost_items
    UNION
    SELECT 'Found' AS type, id, item_name, last_seen_location, found_datetime AS datetime, description, image_path, status 
    FROM found_items
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
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
        .main {
            margin-left: 220px;
            padding: 30px;
            width: 100%;
        }
        .chart-container {
            max-width: 700px;
            margin: auto;
        }
        .table-container {
            margin-top: 40px;
        }
        table img {
            width: 80px;
            height: auto;
            border-radius: 6px;
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

<div class="main">
    <h2>Reports Overview</h2>
    <div class="chart-container">
        <canvas id="statusChart" height="200"></canvas>
    </div>

    <div class="table-container">
        <h4 class="mt-5">Items Details</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>Item Name</th>
                        <th>Location</th>
                        <th>Date/Time</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $itemsData->fetch_assoc()): ?>
                    <tr>
                        <td><?= $item['type'] ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['last_seen_location']) ?></td>
                        <td><?= htmlspecialchars($item['datetime']) ?></td>
                        <td><?= htmlspecialchars($item['description']) ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                        <td>
                            <?php if ($item['image_path']): ?>
                                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Lost Items', 'Found Items', 'Pending', 'Resolved'],
            datasets: [{
                label: 'Item Counts',
                data: [<?= $totalLost ?>, <?= $totalFound ?>, <?= $totalPending ?>, <?= $totalResolved ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54,162,235,1)',
                    'rgba(255,206,86,1)',
                    'rgba(75,192,192,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>

</body>
</html>
