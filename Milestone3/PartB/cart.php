<?php
session_start();

// Check if the user is logged in (optional)
// if (!isset($_SESSION['student_id'])) {
//     header('Location: login.php');
//     exit();
// }

require 'db_connection.php';

// Get cart items
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

$books_in_cart = array();

if (!empty($cart)) {
    // Prepare placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($cart), '?'));

    // Updated SQL query to include the author's full name
    $stmt = $conn->prepare("
        SELECT 
            b.bno,
            b.b_name,
            CONCAT(a.a_firstname, ' ', a.a_lastname) AS author_name,
            b.stock
        FROM 
            Book b
        LEFT JOIN 
            Author a
        ON 
            b.author = a.a_id
        WHERE 
            b.bno IN ($placeholders)
    ");

    // Bind parameters
    $stmt->bind_param(str_repeat('i', count($cart)), ...$cart);

    $stmt->execute();
    $result = $stmt->get_result();

    while ($book = $result->fetch_assoc()) {
        $books_in_cart[] = $book;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Library Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Your Cart</h1>
    </div>

    <div class="container">
        <form action="borrow.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Book Name</th>
                        <th>Author</th>
                        <th>Available Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books_in_cart)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Your cart is empty.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books_in_cart as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['b_name']); ?></td>
                                <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                                <td><?php echo htmlspecialchars($book['stock']); ?></td>
                                <td>
                                    <a href="remove_from_cart.php?bno=<?php echo urlencode($book['bno']); ?>">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($books_in_cart)): ?>
                <button type="submit">Borrow Books</button>
            <?php endif; ?>
        </form>
        
        <!-- Always Display the Go Back Button -->
        <div style="margin-top: 20px;">
            <a href="LMS_Book_Page.php" class="button">Go back to books</a>
        </div>
    </div>
</body>
</html>
