<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit;
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $two_factor_auth = isset($_POST['two_factor_auth']) ? 1 : 0;
    $phone_number = $_POST['phone_number'];

    // Password Update Handling
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $password = password_hash($password, PASSWORD_DEFAULT); // Hash password before storing
            $update_sql = "UPDATE users SET password = ?, email_notifications = ?, two_factor_auth = ?, phone_number = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $password, $email_notifications, $two_factor_auth, $phone_number, $user_id);
        } else {
            echo "<p class='alert alert-danger'>Passwords do not match!</p>";
            return;
        }
    } else {
        $update_sql = "UPDATE users SET email_notifications = ?, two_factor_auth = ?, phone_number = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iiis", $email_notifications, $two_factor_auth, $phone_number, $user_id);
    }

    $stmt->execute();
    echo "<p class='alert alert-success'>Settings updated successfully!</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content {
            margin-left: 270px;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .form-group button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            height: 100vh;
            color: white;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar a {
            color: white;
            padding: 12px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar a.active {
            background-color: #575757;
        }

        .logout-btn {
            color: #f44336;
            font-weight: bold;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="lost_items.php">Lost Items</a>
    <a href="found_items.php">Found Items</a>
    <a href="report_lost.php">Report Lost</a>
    <a href="report_found.php">Report Found</a>
    <a href="profile.php">Profile</a>
    <a href="settings.php" class="active">Settings</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Account Settings</h2>
    <form method="POST">
        <!-- Email Display Section -->
        <div class="form-group">
            <label>Email ID</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
        </div>

        <!-- Password Change Section -->
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password (Leave blank if no change)">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password">
        </div>

        <!-- Phone Number Section -->
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>" placeholder="Enter your phone number">
        </div>

        <!-- Email Notification Preference -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="email_notifications" name="email_notifications" <?= isset($user['email_notifications']) && $user['email_notifications'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="email_notifications">Enable Email Notifications</label>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="two_factor_auth" name="two_factor_auth" <?= isset($user['two_factor_auth']) && $user['two_factor_auth'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="two_factor_auth">Enable Two-Factor Authentication</label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Settings</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
