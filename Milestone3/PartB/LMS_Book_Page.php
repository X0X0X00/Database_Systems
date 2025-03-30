<?php
// Start the session
session_start();

// Include the database connection file
require 'db_connection.php';

// Fetch books from the database
$sql = "SELECT b.*, a.a_firstname, a.a_middlename, a.a_lastname
        FROM Book b
        LEFT JOIN Author a ON b.author = a.a_id";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library - Books</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Include your existing CSS styles here */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #343a40;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 1.3em;
            font-weight: 700;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .nav {
            display: flex;
            justify-content: space-around;
            background-color: #495057;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 4px;
        }
        .nav a:hover {
            background-color: #007bff;
            transform: scale(1.1);
        }
        .nav a.active {
            background-color: #007bff;
        }
        .container {
            display: flex;
            margin: 20px;
            gap: 20px;
            align-items: flex-start;
        }
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            flex-shrink: 0;
        }
        .content {
            flex-grow: 1;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            min-width: 0;
            box-sizing: border-box;
        }

        /* Updated search box styles */
        .search-box {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            width: 97%;
            padding: 0;
            box-sizing: border-box;
        }
        
        .search-box form {
            width: 100%;
        }

        .search-box input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.1em;
            transition: all 0.3s ease;
        }

        .search-box input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .book-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .book-item {
            display: flex;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            gap: 24px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .book-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .book-cover {
            width: 150px;
            flex-shrink: 0;
        }
        .book-cover img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .book-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .book-title {
            font-size: 1.5em;
            font-weight: 700;
            color: #343a40;
            margin: 0 0 8px 0;
        }
        .book-author {
            font-size: 1.1em;
            color: #666;
            margin: 0 0 16px 0;
        }
        .book-availability {
            display: flex;
            gap: 24px;
            margin-bottom: 16px;
            color: #555;
        }
        .book-abstract {
            color: #666;
            line-height: 1.6;
            margin: 0;
        }
        .sidebar h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #343a40;
        }
        .category-button {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ccc;
            padding: 15px;
            width: 100%;
            margin-bottom: 15px;
            cursor: pointer;
            text-align: left;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .category-button:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }
        .error-message {
            color: #dc3545;
            padding: 15px;
            border: 1px solid #dc3545;
            border-radius: 8px;
            background-color: #f8d7da;
            margin-top: 20px;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        .book-item form button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
            margin-top: 15px;
        }
        .book-item form button:hover {
            background-color: #218838;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
            .content {
                padding: 20px;
            }
            .search-box {
                padding: 0 10px;
            }
            .search-box input[type="text"] {
                padding: 12px 15px;
            }
            .book-item {
                flex-direction: column;
            }
            .book-cover {
                width: 100%;
                max-width: 200px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Library Management System</h1>
    </div>

    <div class="nav">
        <a href="LMS Home page.html">HOME</a>
        <a href="LMS_Book_Page.php" class="active">BOOKS</a>
        <a href="LMS Contact page.html">CONTACT</a>
        <a href="cart.php">CART</a>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>Categories</h3>
            <button class="category-button" data-category="All Books">All Books</button>
            <button class="category-button" data-category="Literature">Literature</button>
            <!-- Add more category buttons as needed -->
            <button class="category-button" data-category="Religion">Religion</button>
            <button class="category-button" data-category="Computer Science">Computer Science</button>
            <button class="category-button" data-category="Philosophy">Philosophy</button>
            <button class="category-button" data-category="Science">Science</button>
            <button class="category-button" data-category="Technology">Technology</button>
            <button class="category-button" data-category="Arts">Arts</button>
            <button class="category-button" data-category="Recreation">Recreation</button>
            <button class="category-button" data-category="History">History</button>
            <button class="category-button" data-category="Geography">Geography</button>
            <button class="category-button" data-category="Business">Business</button>
            <button class="category-button" data-category="Economy">Economy</button>
            <button class="category-button" data-category="Biology">Biology</button>
            <button class="category-button" data-category="Physics">Physics</button>
            <!-- ... -->
        </div>
        
        <div class="content">

            <div id="searchResults" class="book-grid">
                <!-- Display books from the database -->
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($book = $result->fetch_assoc()): ?>
                        <div class="book-item">
                        <div class="book-cover">
                            <img src="/~kxu28/pic/<?php echo rawurlencode($book['bno']); ?>.jpeg" alt="Book Cover">
                        </div>
                            <div class="book-info">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['b_name']); ?></h3>
                                <p class="book-author">
                                    <?php
                                        echo htmlspecialchars($book['a_firstname'] . ' ' . $book['a_middlename'] . ' ' . $book['a_lastname']);
                                    ?>
                                </p>
                                <div class="book-availability">
                                    <span>Total Copies: <?php echo htmlspecialchars($book['total']); ?></span>
                                    <span>Available: <?php echo htmlspecialchars($book['stock']); ?></span>
                                    <span>Location: <?php echo htmlspecialchars($book['location']); ?></span>
                                </div>
                                <p class="book-abstract"><?php echo htmlspecialchars($book['description']); ?></p>
                                
                                <!-- Add to Cart Button -->
                                <form action="add_to_cart.php" method="POST">
                                    <input type="hidden" name="bno" value="<?php echo $book['bno']; ?>">
                                    <button type="submit">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results">No books found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Include your JavaScript code here -->
 
</body>
</html>


<?php
// Check for an AJAX request based on a selected category
if (isset($_GET['category'])) {
    $selected_category = $_GET['category'];

    // Query to fetch books for the selected category
    $query = "SELECT * FROM Book WHERE category = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output the books dynamically
    while ($book = $result->fetch_assoc()) {
        echo "<div class='book-item'>";
        echo "<h3>" . htmlspecialchars($book['title']) . "</h3>";
        echo "<p>" . htmlspecialchars($book['description']) . "</p>";
        echo '<form action="add_to_cart.php" method="POST">';
        echo '<input type="hidden" name="bno" value="' . htmlspecialchars($book['bno']) . '">';
        echo '<button type="submit">Add to Cart</button>';
        echo '</form>';
        echo "</div>";
    }


    exit;
}
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categoryButtons = document.querySelectorAll('.category-button');
        const bookGrid = document.querySelector('.book-grid');

        // Fetch all books by default when the page loads (set default active as "All Books")
        fetchBooksByCategory('all');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function () {
                const category = this.dataset.category;
                categoryButtons.forEach(btn => btn.classList.remove('active')); // Remove active from all buttons
                this.classList.add('active'); // Add active class to the clicked button
                fetchBooksByCategory(category);
            });
        });

        function fetchBooksByCategory(category) {
            fetch(`Category search.php?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    displayBooks(data);
                })
                .catch(error => console.error('Error fetching books:', error));
        }

        function displayBooks(books) {
            const bookGrid = document.querySelector('.book-grid');
            bookGrid.innerHTML = ''; // Clear existing books

            if (books.length === 0) {
                bookGrid.innerHTML = "<p>No books found for this category.</p>";
            } else {
                books.forEach(book => {
                    const bookItem = document.createElement('div');
                    bookItem.classList.add('book-item');
                    bookItem.innerHTML = `
                        <div class="book-cover">
                            <img src="${book.cover_image}" alt="Book Cover">
                        </div>
                        <div class="book-info">
                            <h3 class="book-title">${book.title}</h3>
                            <p class="book-author">${book.author}</p>
                            <div class="book-availability">
                                <span>Total Copies: ${book.total}</span>
                                <span>Available: ${book.available}</span>
                                <span>Location: ${book.location}</span>
                            </div>
                            <p class="book-abstract">${book.description}</p>
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="bno" value=${book.bno}>
                                <button type="submit">Add to Cart</button>
                            </form>
                        </div>
                    `;
                    bookGrid.appendChild(bookItem);
                });
            }
        }
    }); // Some modifications over here value = "111" is a constant but need to be a variable.

</script>

