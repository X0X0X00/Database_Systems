<?php
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_name'])) {
    header('Location: login.php');
    exit();
}

$manager_name = $_SESSION['manager_name'];

// Include the database connection file
require 'db_connection.php';

// Fetch Records from the database
$sql = "SELECT record_id, cno, bno, borrow_date, return_date, manager_id, location FROM Record";
$result = $conn->query($sql);

// Check for messages
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Records - Library Management System</title>
    <!-- Link to CSS file -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Library Management System</h1>
    </div>

    <!-- Navigation Bar -->
    <div class="nav">
        <a href="manager_home.php">MANAGER</a>
        <a href="manage_records.php" class="active">MANAGE Records</a>
        <!-- Add other navigation links as needed -->
    </div>

    <!-- Content Section -->
    <div class="manager-container">
        <h1 class="page-title">Manage Records</h1>

        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($manager_name); ?></h2>
            <p>Below is the list of records in the library catalog.</p>
        </div>

        <!-- Display Messages -->
        <?php if ($message): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Records Table -->
        <table>
            <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Card Number</th>
                    <th>Book Number</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Manager ID</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($record = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['cno']); ?></td>
                            <td><?php echo htmlspecialchars($record['bno']); ?></td>
                            <td><?php echo htmlspecialchars($record['borrow_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['return_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['manager_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['location']); ?></td>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="11">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
