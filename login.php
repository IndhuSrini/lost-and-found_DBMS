<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "❌ Email and password are required!";
    } else {
        // First, check in admins table
        $stmtAdmin = $conn->prepare("SELECT id, username, password FROM admins WHERE email = ?");
        $stmtAdmin->bind_param("s", $email);
        $stmtAdmin->execute();
        $resultAdmin = $stmtAdmin->get_result();

        if ($resultAdmin->num_rows > 0) {
            $admin = $resultAdmin->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['profile_pic'] = null; // Admin doesn't have a profile pic
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid password!";
            }
        } else {
            // Then check in users table
            $stmtUser = $conn->prepare("SELECT id, username, password, profile_pic FROM users WHERE email = ?");
            $stmtUser->bind_param("s", $email);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();

            if ($resultUser->num_rows > 0) {
                $user = $resultUser->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['profile_pic'] = $user['profile_pic']; // Save the profile pic in session
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "❌ Invalid password!";
                }
            } else {
                $error = "❌ No account found with that email!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Lost & Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
        }
        .login-container h2 {
            text-align: center;
            color: #1cc88a;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 50px;
        }
        .btn-primary {
            border-radius: 50px;
            background-color: #1cc88a;
            border: none;
        }
        .btn-primary:hover {
            background-color: #17a673;
        }
        .alert {
            border-radius: 10px;
        }
        .footer-link {
            text-align: center;
            margin-top: 20px;
        }
        .footer-link a {
            color: #1cc88a;
            text-decoration: none;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login to Lost & Found</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">📧 Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">🔐 Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="footer-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

</body>
</html>
