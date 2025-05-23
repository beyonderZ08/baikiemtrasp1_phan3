<?php
session_start();
include 'db_connect.php';

// Check if user is logged in (matching cart.php logic)
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to map product names to image filenames
function getProductImage($productName) {
    $imageMap = [
        'iPhone 11' => 'iphone11-den.png',
        'iPhone 16' => 'iphone-16.pnh',
        'iPhone 15' => 'iphone15.jpg',
        // Add more products as needed
    ];
    $productName = trim($productName);
    return isset($imageMap[$productName]) ? $imageMap[$productName] : 'default_image.png';
}

// Handle adding item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve product details from form submission
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Validate input
    if (empty($name) || $price <= 0 || $quantity < 1) {
        // Redirect back with an error message (you can customize this)
        header("Location: products.php?error=Invalid product details");
        exit;
    }

    // Get the image filename based on product name
    $image = getProductImage($name);

    // Create product array
    $product = [
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $image
    ];

    // Add or update product in cart
    $found = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['name'] === $name) {
            // Product already in cart, update quantity
            $_SESSION['cart'][$index]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        // Add new product to cart
        $_SESSION['cart'][] = $product;
    }

    // Redirect to cart page
    header("Location: cart.php");
    exit;
} else {
    // If accessed directly without POST, redirect to products page
    header("Location: products.php");
    exit;
}
?>