<?php
session_start();

// Include the database connection file
require 'db_connection.php';

// Initialize variables
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $f_id = $conn->real_escape_string($_POST['f_id']);
    $f_password = $_POST['f_password'];

    // Fetch faculty details from the database
    $stmt = $conn->prepare("SELECT f_id, f_password, f_firstname, f_lastname, cno FROM Faculty WHERE f_id = ?");
    $stmt->bind_param("s", $f_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $faculty = $result->fetch_assoc();

        // Verify the password (assuming passwords are stored in plain text; consider using hashing)
        if ($f_password === $faculty['f_password']) {
            // Set session variables
            $_SESSION['faculty_name'] = $faculty['f_firstname'] . ' ' . $faculty['f_lastname'];
            $_SESSION['faculty_id'] = $faculty['f_id'];
            $_SESSION['cno'] = $faculty['cno']; // Store the card number

            // Redirect to the faculty's book page or dashboard
            header('Location: LMS Home page.html');
            exit();
        } else {
            header('Location: Faculty Login page.html');
            $error = 'Invalid password.';
        }
    } else {
        header('Location: Faculty Login page.html');
        $error = 'Faculty ID not found.';
    }

    $stmt->close();
}

$conn->close();
?>