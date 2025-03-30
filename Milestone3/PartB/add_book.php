<?php
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_name'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
require 'db_connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $b_name = $conn->real_escape_string($_POST['b_name']);
    $ISBN = $conn->real_escape_string($_POST['ISBN']);
    $press = $conn->real_escape_string($_POST['press']);
    $year = intval($_POST['year']);
    $author_id = $conn->real_escape_string($_POST['author']);
    $category_id = intval($_POST['category_id']);
    $total = intval($_POST['total']);
    $stock = intval($_POST['stock']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);

    // Check if the author exists
    $author_check = "SELECT a_id FROM Author WHERE a_id = '$author_id'";
    $author_result = $conn->query($author_check);

    if ($author_result->num_rows == 0) {
        // If author doesn't exist, redirect with an error
        header('Location: manage_books.php?message=Author not found. Please add the author first.');
        exit();
    }

    // Check if the category exists
    $category_check = "SELECT c_id FROM Category WHERE c_id = $category_id";
    $category_result = $conn->query($category_check);

    if ($category_result->num_rows == 0) {
        // If category doesn't exist, redirect with an error
        header('Location: manage_books.php?message=Category not found. Please add the category first.');
        exit();
    }

    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO Book (b_name, ISBN, press, year, author, category_id, total, stock, description, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisiisss", $b_name, $ISBN, $press, $year, $author_id, $category_id, $total, $stock, $description, $location);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect with a success message
        header('Location: manage_books.php?message=Book added successfully');
    } else {
        // Redirect with an error message
        header('Location: manage_books.php?message=Error adding book: ' . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the manage books page if accessed directly
    header('Location: manage_books.php');
}
?>
