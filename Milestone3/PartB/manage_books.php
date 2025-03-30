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

// Fetch books from the database
$sql = "SELECT b.*, c.c_name, a.a_firstname, a.a_middlename, a.a_lastname
        FROM Book b
        LEFT JOIN Category c ON b.category_id = c.c_id
        LEFT JOIN Author a ON b.author = a.a_id";

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
    <title>Manage Books - Library Management System</title>
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
        <a href="manage_books.php" class="active">MANAGE BOOKS</a>
        <!-- Add other navigation links as needed -->
    </div>

    <!-- Content Section -->
    <div class="manager-container">
        <h1 class="page-title">Manage Books</h1>

        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($manager_name); ?></h2>
            <p>Below is the list of books in the library catalog.</p>
        </div>

        <!-- Display Messages -->
        <?php if ($message): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Books Table -->
        <table>
            <thead>
                <tr>
                    <th>Book No</th>
                    <th>Name</th>
                    <th>ISBN</th>
                    <th>Press</th>
                    <th>Year</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Total</th>
                    <th>Stock</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($book = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['bno']); ?></td>
                            <td><?php echo htmlspecialchars($book['b_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['ISBN']); ?></td>
                            <td><?php echo htmlspecialchars($book['press']); ?></td>
                            <td><?php echo htmlspecialchars($book['year']); ?></td>
                            <td>
                                <?php
                                    echo htmlspecialchars($book['a_firstname'] . ' ' . $book['a_middlename'] . ' ' . $book['a_lastname']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($book['c_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['total']); ?></td>
                            <td><?php echo htmlspecialchars($book['stock']); ?></td>
                            <td><?php echo htmlspecialchars($book['location']); ?></td>
                            <td>
                                <a href="edit_book.php?bno=<?php echo urlencode($book['bno']); ?>">Edit</a> |
                                <a href="delete_book.php?bno=<?php echo urlencode($book['bno']); ?>" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="11">No books found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Link to Add New Book -->
        <div class="link-section">
            <p>To add a new book, click the button below:</p>
            <a href="manage_books.php#addBookForm" class="button">Add New Book</a>
        </div>

        <!-- Add New Book Form -->
        <div class="form-section" id="addBookForm">
            <h2>Add a New Book</h2>
            <form action="add_book.php" method="POST">
                <label for="b_name">Book Name:</label><br>
                <input type="text" id="b_name" name="b_name" required><br><br>

                <label for="ISBN">ISBN:</label><br>
                <input type="text" id="ISBN" name="ISBN" required><br><br>

                <label for="press">Press:</label><br>
                <input type="text" id="press" name="press"><br><br>

                <label for="year">Year:</label><br>
                <input type="number" id="year" name="year"><br><br>

                <label for="author">Author:</label><br>
                <select id="author" name="author" required>
                    <?php
                    // Fetch authors
                    $author_sql = "SELECT a_id, a_firstname, a_middlename, a_lastname FROM Author";
                    $author_result = $conn->query($author_sql);
                    while ($author = $author_result->fetch_assoc()) {
                        $author_name = $author['a_firstname'] . ' ' . $author['a_middlename'] . ' ' . $author['a_lastname'];
                        echo "<option value='" . htmlspecialchars($author['a_id']) . "'>" . htmlspecialchars($author_name) . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="category_id">Category:</label><br>
                <select id="category_id" name="category_id" required>
                    <?php
                    // Fetch categories
                    $category_sql = "SELECT c_id, c_name FROM Category";
                    $category_result = $conn->query($category_sql);
                    while ($category = $category_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($category['c_id']) . "'>" . htmlspecialchars($category['c_name']) . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="total">Total Copies:</label><br>
                <input type="number" id="total" name="total" required><br><br>

                <label for="stock">Stock:</label><br>
                <input type="number" id="stock" name="stock" required><br><br>

                <label for="description">Description:</label><br>
                <textarea id="description" name="description"></textarea><br><br>

                <label for="location">Location:</label><br>
                <input type="text" id="location" name="location"><br><br>

                <button type="submit">Add Book</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
