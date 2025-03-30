<?php
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_name'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
require 'db_connection.php';

// Get the book number from the URL
$bno = isset($_GET['bno']) ? intval($_GET['bno']) : 0;

// Fetch the book details
$stmt = $conn->prepare("SELECT * FROM Book WHERE bno = ?");
$stmt->bind_param("i", $bno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // No book found with that ID
    header('Location: manage_books.php?message=Book not found');
    exit();
}

$book = $result->fetch_assoc();

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

    // Update the book in the database
    $update_stmt = $conn->prepare("UPDATE Book SET b_name = ?, ISBN = ?, press = ?, year = ?, author = ?, category_id = ?, total = ?, stock = ?, description = ?, location = ? WHERE bno = ?");
    $update_stmt->bind_param("sssisiisssi", $b_name, $ISBN, $press, $year, $author_id, $category_id, $total, $stock, $description, $location, $bno);

    if ($update_stmt->execute()) {
        // Redirect with a success message
        header('Location: manage_books.php?message=Book updated successfully');
    } else {
        // Redirect with an error message
        header('Location: manage_books.php?message=Error updating book: ' . $update_stmt->error);
    }

    $update_stmt->close();
    $conn->close();
    exit();
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book - Library Management System</title>
    <!-- Link to CSS file -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header and Navigation same as before -->
    <div class="header">
        <h1>Library Management System</h1>
    </div>
    <div class="nav">
        <a href="manager_home.php">MANAGER</a>
        <a href="manage_books.php" class="active">MANAGE BOOKS</a>
    </div>

    <!-- Edit Book Form -->
    <div class="manager-container">
        <h1 class="page-title">Edit Book</h1>
        <form action="edit_book.php?bno=<?php echo $bno; ?>" method="POST">
            <label for="b_name">Book Name:</label><br>
            <input type="text" id="b_name" name="b_name" value="<?php echo htmlspecialchars($book['b_name']); ?>" required><br><br>

            <label for="ISBN">ISBN:</label><br>
            <input type="text" id="ISBN" name="ISBN" value="<?php echo htmlspecialchars($book['ISBN']); ?>" required><br><br>

            <label for="press">Press:</label><br>
            <input type="text" id="press" name="press" value="<?php echo htmlspecialchars($book['press']); ?>"><br><br>

            <label for="year">Year:</label><br>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($book['year']); ?>"><br><br>

            <label for="author">Author:</label><br>
            <select id="author" name="author" required>
                <?php
                // Fetch authors
                $author_sql = "SELECT a_id, a_firstname, a_middlename, a_lastname FROM Author";
                $author_result = $conn->query($author_sql);
                while ($author = $author_result->fetch_assoc()) {
                    $author_name = $author['a_firstname'] . ' ' . $author['a_middlename'] . ' ' . $author['a_lastname'];
                    $selected = ($author['a_id'] == $book['author']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($author['a_id']) . "' $selected>" . htmlspecialchars($author_name) . "</option>";
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
                    $selected = ($category['c_id'] == $book['category_id']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($category['c_id']) . "' $selected>" . htmlspecialchars($category['c_name']) . "</option>";
                }
                ?>
            </select><br><br>

            <label for="total">Total Copies:</label><br>
            <input type="number" id="total" name="total" value="<?php echo htmlspecialchars($book['total']); ?>" required><br><br>

            <label for="stock">Stock:</label><br>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description"><?php echo htmlspecialchars($book['description']); ?></textarea><br><br>

            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($book['location']); ?>"><br><br>

            <button type="submit">Update Book</button>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>
