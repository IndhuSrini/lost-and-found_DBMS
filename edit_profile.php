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
$dbname = "lost_and_found_db"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user info
$admin_id = $_SESSION['admin_id'];

// Fetch admin info
$stmt = $conn->prepare("SELECT username, profile_pic, email FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form values
  $new_username = $_POST['username'];
  $new_email = $_POST['email'];
  $new_profile_pic = $_FILES['profile_pic']['name'];
  $profile_pic_tmp = $_FILES['profile_pic']['tmp_name'];

  // If a new profile pic is uploaded
  if (!empty($new_profile_pic)) {
      move_uploaded_file($profile_pic_tmp, 'uploads/' . $new_profile_pic);
      // Update admin details including profile pic
      $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, profile_pic = ? WHERE id = ?");
      $stmt->bind_param("sssi", $new_username, $new_email, $new_profile_pic, $admin_id);
  } else {
      // If no profile pic is uploaded, exclude it from the query
      $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
      $stmt->bind_param("ssi", $new_username, $new_email, $admin_id);
  }

  // Execute the query
  $stmt->execute();

  // Update session with the new values after successful profile update
  $_SESSION['username'] = $new_username;
  if (!empty($new_profile_pic)) {
      $_SESSION['profile_pic'] = $new_profile_pic;
  }

  // Redirect to refresh the page and show updated profile
  header("Location: edit_profile.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .form-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container .btn-primary {
            width: 100%;
            font-size: 16px;
            padding: 12px;
        }
        .profile-image {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Admin Dashboard</h4>
    <div class="text-center mb-4">
        <img src="<?php echo isset($_SESSION['profile_pic']) && $_SESSION['profile_pic'] ? 'uploads/' . $_SESSION['profile_pic'] : 'default.jpg'; ?>" 
             alt="Admin Profile" 
             class="rounded-circle" 
             style="width: 60px; height: 60px;">
        <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>
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

<!-- Main Section -->
<div class="main">
    <div class="overlay">
        <h2>Edit Profile</h2>

        <!-- Edit Profile Form -->
        <div class="form-container">
            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label text-white">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="profile_pic" class="form-label text-white">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic">
                    <img src="<?php echo isset($_SESSION['profile_pic']) && $_SESSION['profile_pic'] ? 'uploads/' . $_SESSION['profile_pic'] : 'default.jpg'; ?>" 
                         alt="Current Profile Picture" 
                         class="profile-image mt-3">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
