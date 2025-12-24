<?php
// Hashing password 1
$password1 = "123456789";
$hashed_password1 = password_hash($password1, PASSWORD_DEFAULT);
echo "Hashed Password for 123456789: " . $hashed_password1 . "<br>";

// Hashing password 2
$password2 = "admin123";
$hashed_password2 = password_hash($password2, PASSWORD_DEFAULT);
echo "Hashed Password for admin123: " . $hashed_password2."<br>";
?>

