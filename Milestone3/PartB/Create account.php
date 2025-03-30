<?php
$servername = "localhost";
$username = "zzh133"; // Replace with your MySQL username
$password = "rzwzbeFi"; // Replace with your MySQL password
$dbname = "zzh133_1"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Input data from form
$username = $_POST['username']; // Username provided by the user
$password = $_POST['password']; // Password provided by the user
$accountType = $_POST['accountType']; // Account type: 'student' or 'faculty'

// Validate input
if (!in_array($accountType, ['student', 'faculty'])) {
    die("Invalid account type.");
}

// Step 1: Insert into respective table
if ($accountType === 'student') {
    $sql_student = "INSERT INTO Student (s_id, s_password) VALUES (?, ?)";
    $stmt_student = $conn->prepare($sql_student);
    $stmt_student->bind_param("ss", $username, $password);

    if ($stmt_student->execute()) {
        // Step 2: Insert into Card table
        $sql_card = "INSERT INTO Card (name, department, type) VALUES (?, NULL, 'Student')";
        $stmt_card = $conn->prepare($sql_card);
        $stmt_card->bind_param("s", $username);

        if ($stmt_card->execute()) {
            echo "Student account and card successfully created.";
            header("Location: Login page.html?error=1");
        } else {
            echo "Error inserting into Card table: " . $stmt_card->error;
            header("Location: Login page.html?error=1");
        }
        $stmt_card->close();
    } else {
        echo "Error inserting into Student table: " . $stmt_student->error;
        header("Location: Login page.html?error=1");
    }
    $stmt_student->close();
} elseif ($accountType === 'faculty') {
    $sql_faculty = "INSERT INTO Faculty (f_id, f_password) VALUES (?, ?)";
    $stmt_faculty = $conn->prepare($sql_faculty);
    $stmt_faculty->bind_param("ss", $username, $password);

    if ($stmt_faculty->execute()) {
        // Step 2: Insert into Card table
        $sql_card = "INSERT INTO Card (name, department, type) VALUES (?, NULL, 'Faculty')";
        $stmt_card = $conn->prepare($sql_card);
        $stmt_card->bind_param("s", $username);

        if ($stmt_card->execute()) {
            echo "Faculty account and card successfully created.";
            header("Location: Login page.html?error=1");
        } else {
            echo "Error inserting into Card table: " . $stmt_card->error;
            header("Location: Login page.html?error=1");
        }
        $stmt_card->close();
    } else {
        echo "Error inserting into Faculty table: " . $stmt_faculty->error;
        header("Location: Login page.html?error=1");
    }
    $stmt_faculty->close();
}

$conn->close();
?>
