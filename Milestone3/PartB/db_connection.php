<?php
// db_connection.php

$servername = "localhost";
$username = "zzh133"; // Replace with your MySQL username
$password = "rzwzbeFi"; // Replace with your MySQL password
$dbname = "zzh133_1"; // Your database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for a connection error and display a user-friendly message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
