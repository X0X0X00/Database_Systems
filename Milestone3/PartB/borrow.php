<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit();
}

// Include the database connection file
require 'db_connection.php';

// Get the student's card number (cno) from the session
$cno = $_SESSION['cno'];

// Get cart items
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

if (empty($cart)) {
    echo "Your cart is empty.";
    exit();
}

$errors = array();
$successes = array();

// Start transaction
$conn->begin_transaction();

try {
    foreach ($cart as $bno) {
        // Check if the book is available
        $stmt = $conn->prepare("SELECT stock FROM Book WHERE bno = ?");
        $stmt->bind_param("i", $bno);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        if ($book && $book['stock'] > 0) {
            // Insert into Record table
            $borrow_date = date('Y-m-d');
            $return_date = null; // Assuming return date is null initially
            $manager_id = null; // Assuming no manager approval needed
            $location = null; // You can set this if needed

            $insert_stmt = $conn->prepare("INSERT INTO Record (cno, bno, borrow_date, return_date, manager_id, location) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("iissss", $cno, $bno, $borrow_date, $return_date, $manager_id, $location);

            if ($insert_stmt->execute()) {
                // Decrease the stock by 1
                $update_stmt = $conn->prepare("UPDATE Book SET stock = stock - 1 WHERE bno = ?");
                $update_stmt->bind_param("i", $bno);
                $update_stmt->execute();
                $update_stmt->close();

                $successes[] = "Successfully borrowed book ID: $bno.";
            } else {
                $errors[] = "Failed to borrow book ID: $bno.";
            }

            $insert_stmt->close();
        } else {
            $errors[] = "Book ID: $bno is out of stock or does not exist.";
        }

        $stmt->close();
    }

    // Commit transaction
    $conn->commit();

    // Clear the cart
    unset($_SESSION['cart']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "An error occurred: " . $e->getMessage();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow Result - Library Management System</title>
    <style>
        /* Include any styles you need */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .success-messages, .error-messages {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success-messages {
            background-color: #d4edda;
            color: #155724;
        }
        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Borrowing Result</h1>
        <?php if (!empty($successes)): ?>
            <div class="success-messages">
                <?php foreach ($successes as $message): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="LMS_Book_Page.php">Back to Books</a>
    </div>
</body>
</html>
