<?php
session_start();

// Include the database connection file
require 'db_connection.php';

// Initialize variables
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $m_id = $conn->real_escape_string($_POST['m_id']);
    $m_password = $_POST['m_password'];

    // Fetch manager details from the database
    $stmt = $conn->prepare("SELECT m_id, m_password, m_firstname, m_lastname FROM Library_Manager WHERE m_id = ?");
    $stmt->bind_param("s", $m_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $manager = $result->fetch_assoc();

        // Verify the password (assuming passwords are stored in plain text; consider using hashing)
        if ($m_password === $manager['m_password']) {
            // Set session variables
            $_SESSION['manager_name'] = $manager['m_firstname'] . ' ' . $manager['m_lastname'];
            $_SESSION['manager_id'] = $manager['m_id'];

            // Redirect to the manager home page
            header('Location: manager_home.php');
            exit();
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'Manager ID not found.';
    }

    $stmt->close();
}

$conn->close();
