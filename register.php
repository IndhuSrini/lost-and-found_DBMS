<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];
    $profile_pic = "";

    if ($password !== $confirm) {
        $msg = "❌ Passwords do not match.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Profile picture upload
        if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] === 0) {
            $target_dir = "uploads/";
            $ext = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
            $new_name = "user_" . time() . "." . $ext;
            $target_file = $target_dir . $new_name;

            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
            } else {
                $msg = "❌ Error uploading the profile picture.";
            }
        }

        // Insert user
        if (empty($msg)) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_pic) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_pic);
            if ($stmt->execute()) {
                $msg = "✅ Registration successful! <a href='login.php'>Login now</a>";
            } else {
                $msg = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            font-family: 'Segoe UI', sans-serif;
        }
        .register-box {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .register-box h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 30px;
        }
        .btn-register {
            background-color: #0d6efd;
            border: none;
            padding: 10px 30px;
            font-weight: bold;
        }
        .btn-register:hover {
            background-color: #084298;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="register-box">
        <h2>Register for Lost & Found</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">👤 Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">📧 Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">🔐 Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">🔐 Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">🖼️ Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control" accept="image/*">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-register">Register</button>
            </div>
        </form>

        <div class="text-center mt-3">
            Already registered? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

</body>
</html>
