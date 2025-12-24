<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $profile_pic = $user['profile_pic']; // Keep current if not changed

    // Handle new picture
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] === 0) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
        $new_name = "user_" . time() . "." . $ext;
        $target_file = $target_dir . $new_name;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        } else {
            $msg = "❌ Error uploading profile picture.";
        }
    }

    if (empty($msg)) {
        $update = $conn->prepare("UPDATE users SET username=?, email=?, profile_pic=? WHERE id=?");
        $update->bind_param("sssi", $username, $email, $profile_pic, $user_id);
        if ($update->execute()) {
            $msg = "✅ Profile updated successfully!";
            $_SESSION['username'] = $username;
            $_SESSION['profile_pic'] = $profile_pic;
        } else {
            $msg = "❌ Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            font-family: 'Segoe UI', sans-serif;
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
        .main-content {
            margin-left: 240px;
            padding: 40px;
        }
        .profile-box {
            max-width: 600px;
            margin: auto;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .profile-box h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 30px;
        }
        .btn-update {
            background-color: #0d6efd;
            border: none;
            padding: 10px 30px;
            font-weight: bold;
        }
        .btn-update:hover {
            background-color: #084298;
        }
        .form-label {
            font-weight: 500;
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

<div class="main-content">
    <div class="profile-box">
        <h2>Update Profile</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">👤 Username</label>
                <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($user['username']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">📧 Email</label>
                <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">🖼️ Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control" accept="image/*">
                <?php if ($user['profile_pic']): ?>
                    <img src="<?php echo $user['profile_pic']; ?>" alt="Profile Pic" width="100" class="mt-2 rounded">
                <?php endif; ?>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-update">Update Profile</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
