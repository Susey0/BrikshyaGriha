<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brikshya_griha";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin credentials

$admin_email = "admin@gmail.com"; // Replace with actual admin username
$admin_password = "admin@123"; // Replace with actual admin password

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

// SQL query to insert admin credentials
$sql = "INSERT INTO users (email, password, role) VALUES ('$admin_email', '$hashed_password', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
