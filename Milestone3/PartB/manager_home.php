<?php
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_name'])) {
    header('Location: login.php');
    exit();
}

$manager_name = $_SESSION['manager_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Management System - Manager Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Library Management System</h1>
    </div>

    <!-- Navigation Bar -->
    <div class="nav">
        <a href="manager_home.php" class="active">MANAGER</a>
        <!-- Add other navigation links as needed -->
    </div>

    <div class="manager-container">
        <h1 class="page-title">Manager Dashboard</h1>

        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($manager_name); ?></h2>
            <p>
                Use the dashboard below to manage library resources, view statistics, and handle user accounts.
            </p>
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>Manage Records</h3>
                <p>Add, edit, or remove user records and transactions.</p>
                <a href="manage_records.php">Go to Record Management</a>
            </div>

            <div class="card">
                <h3>Manage Books</h3>
                <p>Add, edit, or remove books from the library catalog.</p>
                <a href="manage_books.php">Go to Books Management</a>
            </div>
            <!-- Add other dashboard cards as needed -->
        </div>
    </div>
</body>
</html>
