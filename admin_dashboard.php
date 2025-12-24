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

// Admin user info
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username, profile_pic FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch monthly lost item counts
$lost_data = [];
$lost_sql = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM lost_items GROUP BY MONTH(created_at)";
$result = $conn->query($lost_sql);
while ($row = $result->fetch_assoc()) {
    $lost_data[(int)$row['month']] = $row['count'];
}

// Fetch monthly found item counts
$found_data = [];
$found_sql = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM found_items GROUP BY MONTH(created_at)";
$result = $conn->query($found_sql);
while ($row = $result->fetch_assoc()) {
    $found_data[(int)$row['month']] = $row['count'];
}

// Month labels and final arrays
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$lost_counts = [];
$found_counts = [];
for ($i = 1; $i <= 12; $i++) {
    $lost_counts[] = $lost_data[$i] ?? 0;
    $found_counts[] = $found_data[$i] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
    width: 220px;
    background-color: #f8f9fa;
    height: 100vh;
    padding: 20px;
    border-right: 1px solid #ddd;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
}
.sidebar h4 {
    color: #0d6efd;
}
.sidebar .profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ccc;
}
.sidebar .nav-link {
    display: block;
    padding: 10px 15px;
    color: #000;
    text-decoration: none;
    transition: background 0.2s ease;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background-color: #e9ecef;
    border-radius: 4px;
}

        .main {
            margin-left: 220px;
            padding: 30px;
            background-image: url('dashboard_home.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            color: white;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 20px;
            max-width: 900px;
            margin: auto;
        }
        .charts-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Admin Dashboard</h4>
    <div class="text-center mb-3">
        <?php
        // Ensure there's a default fallback if image isn't available
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


<!-- Main Section -->
<div class="main">
    <div class="overlay">
        <h2>Welcome, Admin</h2>
        <p>Here is a monthly report of lost and found items:</p>

        <!-- Bar Chart Container -->
        <div class="charts-container">
            <h3>Items Report</h3>
            <canvas id="itemsChart" width="600" height="300"></canvas>
        </div>

        <!-- Contact Info -->
        <hr class="text-white">
        <h4>Contact Us</h4>
        <p>Email: <a href="mailto:indhumathi.23cse@sonatech.ac.in" class="text-info">indhumathi.23cse@sonatech.ac.in</a></p>
        <p>Phone: <a href="tel:9042129429" class="text-info">9042129429</a></p>

        <!-- About -->
        <hr class="text-white">
        <h4>About Us</h4>
        <p>Developed by: INDHUMATHI and KRISHNAKUMAR</p>
    </div>
</div>

<script>
    const ctx = document.getElementById('itemsChart').getContext('2d');
    const itemsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [
                {
                    label: 'Lost Items',
                    data: <?php echo json_encode($lost_counts); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Found Items',
                    data: <?php echo json_encode($found_counts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'white'
                    }
                },
                x: {
                    ticks: {
                        color: 'white'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
