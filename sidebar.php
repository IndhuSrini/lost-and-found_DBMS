<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Layout</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php 
    // Example: load admin details from session or db
    // Dummy placeholder:
    $admin = ['username' => 'Admin', 'profile_pic' => 'uploads/admin1.jpg']; 
    ?>

    <!-- Sidebar Start -->
    <div class="sidebar">
        <h4 class="text-center">Admin Dashboard</h4>
        <div class="text-center mb-3">
            <?php
            $profilePic = (!empty($admin['profile_pic']) && file_exists($admin['profile_pic'])) ? $admin['profile_pic'] : 'default-profile.png';
            ?>
            <img src="<?php echo $profilePic; ?>" alt="Admin Profile" class="profile-img">
            <p class="mt-2"><strong><?php echo htmlspecialchars($admin['username']); ?></strong></p>
        </div>
        <ul class="nav flex-column">
            <li><a href="admin_dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="lost_items_admin.php" class="nav-link">Lost Items</a></li>
            <li><a href="found_items_admin.php" class="nav-link">Found Items</a></li>
            <li><a href="users.php" class="nav-link">Manage Users</a></li>
            <li><a href="items.php" class="nav-link">Manage Items</a></li>
            <li><a href="reports.php" class="nav-link">Reports</a></li>
            <li><a href="user_report.php" class="nav-link">User Report</a></li>
            <li><a href="edit_profile.php" class="nav-link">Edit Profile</a></li>
            <li><a href="logout.php" class="nav-link text-danger">Logout</a></li>
        </ul>
    </div>
    <!-- Sidebar End -->

    <!-- Main Content -->
    <main class="main-content">
        <h2>Welcome, Admin</h2>
        <p>This is your main content area.</p>
        <!-- Replace this with your actual dashboard/page content -->
    </main>
</body>
</html>
