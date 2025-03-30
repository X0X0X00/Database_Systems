<?php
session_start();

// Check if the user is logged in (optional but recommended)
// if (!isset($_SESSION['student_id'])) {
//     header('Location: login.php');
//     exit();
// }

// Get the book number from the POST data
if (isset($_POST['bno'])) {
    $bno = intval($_POST['bno']);

    // Initialize cart if not already set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the book to the cart if not already in it
    if (!in_array($bno, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $bno;
    }

    // Redirect back to the book page or cart page
    header('Location: cart.php');
    exit();
} else {
    // If no book number is provided, redirect back
    header('Location: LMS_Book_Page.php');
    exit();
}
?>
