<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;

    $item_name = $_POST['item_name'];
    $last_seen_location = $_POST['last_seen_location'];
    $last_seen_datetime = $_POST['last_seen_datetime'];
    $description = $_POST['description'];

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "uploads/";
        $unique_name = uniqid() . "_" . basename($_FILES["item_image"]["name"]);
        $image_path = $target_dir . $unique_name;
        move_uploaded_file($_FILES["item_image"]["tmp_name"], $image_path);
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, last_seen_location, last_seen_datetime, description, image_path, reported_by_email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $item_name, $last_seen_location, $last_seen_datetime, $description, $image_path, $email);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=submitted");
        exit;
    } else {
        echo "Database error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Lost Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background-color: #333;
            padding: 15px;
            color: white;
            height: 100vh;
            position: fixed;
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

        .content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input[type="text"], input[type="datetime-local"], input[type="email"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
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
    <a href="settings.php">Settings</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Report Lost Item</h2>
    <div class="form-container">
        <form action="report_lost.php" method="POST" enctype="multipart/form-data">
            <label for="item_name">Item Name:</label>
            <input type="text" name="item_name" id="item_name" required>

            <label for="last_seen_location">Last Seen Location:</label>
            <input type="text" name="last_seen_location" id="last_seen_location" required>

            <label for="last_seen_datetime">Last Seen Date & Time:</label>
            <input type="datetime-local" name="last_seen_datetime" id="last_seen_datetime" required>

            <label for="description">Item Description:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="email">Your Email (for contact):</label>
            <input type="email" name="email" id="email" required value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">

            <label for="item_image">Upload Item Image:</label>
            <input type="file" name="item_image" id="item_image" required>

            <button type="submit">Submit Lost Item</button>
        </form>
    </div>
</div>

</body>
</html>
