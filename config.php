<?php
$servername = "localhost";  // Database server (usually localhost)
$username = "root";         // Database username (default in XAMPP is 'root')
$password = "";             // Database password (default in XAMPP is an empty string)
$dbname = "lost_and_found_db"; // The name of your database

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
