<?php
session_start();
include('fetch_admin.php');
// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_and_found_db"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin data
$admin_id = $_SESSION['admin_id'];
$admin_sql = "SELECT username, profile_pic FROM admins WHERE id = $admin_id"; // Assuming you have an 'admins' table
$admin_result = $conn->query($admin_sql);
$admin = $admin_result->fetch_assoc();

// Fetch user data
$sql = "SELECT id, username, email, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
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
        .main {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }
        .table-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table-container h3 {
            margin-bottom: 20px;
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

<!-- Main Section (User Report) -->
<div class="main">
    <h2>User Report</h2>

    <!-- User Report Table -->
    <div class="table-container">
        <h3>List of Registered Users</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display user data in table
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
