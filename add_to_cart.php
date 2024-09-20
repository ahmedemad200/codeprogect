<?php
session_start();

// Check if user is authenticated
if (empty($_SESSION['user'])) {
    $_SESSION['errors'] = ['Please log in first!'];
    header('Location: login.php');
    exit;
}

// Retrieve product details from query parameters
$product_name = $_GET['name'] ?? '';
$product_price = $_GET['price'] ?? '';
$product_image = $_GET['image'] ?? '';
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1; // Default quantity to 1

// Validate input data
if (empty($product_name) || empty($product_price) || empty($product_image) || $quantity <= 0) {
    $_SESSION['errors'] = ['Invalid product data'];
    header('Location: producttt.php');
    exit;
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update the product in the cart
$cart = $_SESSION['cart'];
$found = false;

foreach ($cart as &$item) {
    if ($item['name'] === $product_name) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

if (!$found) {
    $cart[] = [
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image,
        'quantity' => $quantity
    ];
}

$_SESSION['cart'] = $cart;

// Redirect back to the products page with a success message
$_SESSION['done'] = ['Product added to cart successfully!'];
header('Location: producttt.php');
exit;
