<?php
session_start();

if (isset($_GET['bno'])) {
    $bno = intval($_GET['bno']);

    if (isset($_SESSION['cart'])) {
        $key = array_search($bno, $_SESSION['cart']);
        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
        }
    }
}

// Redirect back to the cart page
header('Location: cart.php');
exit();
?>
