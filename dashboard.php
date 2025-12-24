<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update session variables for username and profile_pic if needed
$_SESSION['username'] = $user['username'];
$_SESSION['profile_pic'] = $user['profile_pic'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .developer {
            text-align: center;
        }
        .developer img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px auto;
            border: 3px solid white;
        }
        .developer p {
            margin-top: 5px;
            font-size: 14px;
        }
        .sidebar img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        #success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: opacity 1s ease-out;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'submitted'): ?>
    <div id="success-message">
        ✅ Your form is submitted successfully.
    </div>

    <script>
        // Auto fade-out after 4 seconds
        setTimeout(() => {
            const msg = document.getElementById('success-message');
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 1000); // Remove after fade
        }, 4000);
    </script>
<?php endif; ?>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Lost & Found</h4>
    <div class="text-center mb-4">
        <img src="<?php echo $user['profile_pic'] ? $user['profile_pic'] : 'default.jpg'; ?>" alt="Profile">
        <p><?php echo htmlspecialchars($user['username']); ?></p>
    </div>
    <a href="lost_items.php">Lost Items</a>
    <a href="found_items.php">Found Items</a>
    <a href="report_lost.php">Report Lost</a>
    <a href="report_found.php">Report Found</a>
    <a href="profile.php">Profile</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Section -->
<div class="main d-flex align-items-center justify-content-center">
    <div class="overlay text-center">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?> 👋</h2>
        <p>Select an option from the sidebar to get started.</p>

        <hr class="text-white">

        <h4>Contact Us</h4>
        <p>II Year CSE Department</p>
        <p>Email ID: <a href="mailto:indhumathi.23cse@sonatech.ac.in" class="text-info">indhumathi.23cse@sonatech.ac.in</a></p>
        <p>Mobile No: <a href="tel:6382922667" class="text-info">6382922667</a></p>

        <hr class="text-white">

        <h4>About Us</h4>
        <p>Developed by:</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div class="developer">
                <img src="indhu.jpg" alt="Indhumathi">
                <p>Indhumathi<br>II Year CSE</p>
            </div>
            <div class="developer">
                <img src="krishna.jpg" alt="Krishnakumar">
                <p>Krishnakumar<br>II Year CSE</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
