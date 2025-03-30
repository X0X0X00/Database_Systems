<?php
// Set content type to JSON for the response
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "zzh133"; // Replace with your MySQL username
$password = "rzwzbeFi"; // Replace with your MySQL password
$dbname = "zzh133_1"; // Your database name

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the category parameter from the request
$category = isset($_GET['category']) ? $_GET['category'] : '';

// SQL query to get books filtered by category
if ($category === 'All Books') {
    // Fetch all books and join Author table to get full name
    $sql = "SELECT b.b_name AS title, 
                   CONCAT(a.a_firstname, ' ', a.a_lastname) AS author, 
                   b.total, 
                   b.stock AS available, 
                   b.location, 
                   b.description, 
                   b.bno, 
                   b.year, 
                   b.ISBN
            FROM Book b
            JOIN Author a ON b.author = a.a_id";
} else {
    // Fetch books by specific category and join Author table to get full name
    $sql = "SELECT b.b_name AS title, 
                   CONCAT(a.a_firstname, ' ', a.a_lastname) AS author, 
                   b.total, 
                   b.stock AS available, 
                   b.location, 
                   b.description, 
                   b.bno, 
                   b.year, 
                   b.ISBN, 
                   c.c_name AS category
            FROM Book b
            JOIN Author a ON b.author = a.a_id
            JOIN Category c ON b.category_id = c.c_id
            WHERE c.c_name LIKE ?";
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

if ($category !== 'All Books') {
    $category = "%" . $category . "%"; // Use LIKE for fuzzy matching
    $stmt->bind_param("s", $category);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch the results and store them in an array
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = [
        'title' => $row['title'],
        'author' => $row['author'],
        'total' => $row['total'],
        'available' => $row['available'],
        'location' => $row['location'],
        'description' => $row['description'],
        'cover_image' => 'pic/' . $row['bno'] . '.jpeg' // Adjust path to match where images are stored
    ];
}

// Return the books as a JSON response
echo json_encode($books);

// Close the database connection
$conn->close();
?>
