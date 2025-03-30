<?php
session_start();

// Include the database connection file
require 'db_connection.php';

// Initialize variables
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $s_id = $conn->real_escape_string($_POST['s_id']);
    $s_password = $_POST['s_password'];

    // Fetch student details from the database
    $stmt = $conn->prepare("SELECT s_id, s_password, s_firstname, s_lastname, cno FROM Student WHERE s_id = ?");
    $stmt->bind_param("s", $s_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();

        // Verify the password (assuming passwords are stored in plain text; consider using hashing)
        if ($s_password === $student['s_password']) {
            // Set session variables
            $_SESSION['student_name'] = $student['s_firstname'] . ' ' . $student['s_lastname'];
            $_SESSION['student_id'] = $student['s_id'];
            $_SESSION['cno'] = $student['cno']; // Store the card number

            // Redirect to the student's book page or dashboard
            header('Location: LMS Home page.html');
            exit();
        } else {
            header('Location: Student Login page.html');
            $error = 'Invalid password.';
        }
    } else {
        header('Location: Student Login page.html');
        $error = 'Student ID not found.';
    }

    $stmt->close();
}

$conn->close();
?>