<?php
session_start();
include 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if bookId is passed
    if (!isset($data['bookId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Book ID is required.']);
        exit;
    }

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'User not logged in.']);
        exit;
    }

    $bno = $data['bookId'];
    $cno = $_SESSION['user_id'];
    $manager_id = NULL; // Replace as needed
    $location = 'Default Location'; // Replace as needed

    // Debugging: Print received values
    echo "Debugging: bno = $bno, cno = $cno\n";

    // Check if the book is already in the cart
    $checkQuery = "SELECT * FROM Record WHERE cno = ? AND bno = ? AND borrow_date IS NULL AND return_date IS NULL";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        echo json_encode(['error' => 'Error preparing SQL: ' . $conn->error]);
        exit;
    }
    $checkStmt->bind_param('ii', $cno, $bno);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['error' => 'Book is already in the cart.']);
        exit;
    }

    // Insert a new record
    $query = "INSERT INTO Record (cno, bno, borrow_date, return_date, manager_id, location) VALUES (?, ?, NULL, NULL, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing SQL: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iiss', $cno, $bno, $manager_id, $location);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Book added to cart and temporary record created.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
