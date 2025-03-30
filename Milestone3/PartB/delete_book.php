<?php
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_name'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
require 'db_connection.php';

// Get the book number from the URL
$bno = isset($_GET['bno']) ? intval($_GET['bno']) : 0;

// Delete the book
$stmt = $conn->prepare("DELETE FROM Book WHERE bno = ?");
$stmt->bind_param("i", $bno);

if ($stmt->execute()) {
    header('Location: manage_books.php?message=Book deleted successfully');
} else {
    header('Location: manage_books.php?message=Error deleting book: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>
