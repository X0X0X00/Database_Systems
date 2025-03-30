<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cno = $_SESSION['user_id']; // Assuming user ID is stored in session

    $query = "SELECT bno FROM Record WHERE cno = ? AND borrow_date IS NULL AND return_date IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $cno);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }

    echo json_encode($cart);

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
