<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
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

        .main-content {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        .user-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .user-card img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
        }

        .user-info {
            flex-grow: 1;
        }

        .btn {
            font-size: 14px;
            border-radius: 50px;
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
<div class="main-content">
    <h2 class="mb-4 text-success">👥 Manage Users</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="user-card">
            <img src="<?php echo $row['profile_pic'] ? $row['profile_pic'] : 'default.png'; ?>" alt="User Image">
            <div class="user-info">
                <h5><?php echo htmlspecialchars($row['username']); ?></h5>
                <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                <p>Status: <strong><?php echo ucfirst($row['status']); ?></strong></p>
            </div>

            <?php if ($row['status'] == 'active'): ?>
                <a href="toggle_user_status.php?id=<?php echo $row['id']; ?>&status=inactive" class="btn btn-warning me-2">Deactivate</a>
            <?php else: ?>
                <a href="toggle_user_status.php?id=<?php echo $row['id']; ?>&status=active" class="btn btn-success me-2">Activate</a>
            <?php endif; ?>

            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
        </div>
    <?php endwhile; ?>

    <?php $conn->close(); ?>
</div>

</body>
</html>
