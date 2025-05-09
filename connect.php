<?php
// Database configuration
$host = "localhost";      // Your database host
$username = "root";  // Your database username
$password = "";  // Your database password
$database = "qrcode_db";  // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {

}
?>