<?php

require "DBManger.php";
session_start();

// Check if user is authenticated
$is_authenticated = !empty($_SESSION['user']);
if (!$is_authenticated) {
    $_SESSION['errors'] = ['Please log in first!'];
    header('Location: login.php');
    exit;
}

// Handle POST request for submitting a product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    $name = $_POST['name'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    // Validate form inputs (ensure none are empty)
    if (empty($name) || empty($quantity) || empty($price) || empty($description) || !$image || $image['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['errors'] = ['All fields are required and image must be uploaded.'];
        header('Location: products.php');
        exit;
    }

    // Handle image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image type (optional, can be extended)
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $valid_extensions)) {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG & GIF files are allowed.'];
        header('Location: products.php');
        exit;
    }

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        try {
            // Initialize database connection
            $pdo = new DBManager();

            // Insert product into the database
            $sql = "INSERT INTO `products` (`name`, `quantity`, `price`, `image`, `description`) VALUES (?, ?, ?, ?, ?)";
            $args = [$name, $quantity, $price, $target_file, $description];
            $pdo->query($sql, ...$args);

            // Success message
            $_SESSION['done'] = ['Product submitted successfully!'];
            header('Location: products.php');
            exit;

        } catch (\PDOException $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            header("Location: products.php");
            die;
        }
    } else {
        $_SESSION['errors'] = ['Failed to upload image.'];
        header('Location: products.php');
        exit;
    }
}
?>
