<?php
// Database Configuration
$host = 'localhost';
$db = 'bakery';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
