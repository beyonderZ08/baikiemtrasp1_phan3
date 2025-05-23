<?php
session_start();

if (isset($_GET['id']) && isset($_SESSION['cart']) && array_key_exists($_GET['id'], $_SESSION['cart'])) {
    unset($_SESSION['cart'][$_GET['id']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Đánh lại index
}

header("Location: cart.php");
exit;