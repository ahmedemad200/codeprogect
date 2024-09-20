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
?>

<?php include "error.php"; ?>
<?php include "success.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Submit a Product</h2>

        <!-- Error and success messages -->
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['done'])): ?>
            <div class="alert alert-success">
                <?php foreach ($_SESSION['done'] as $done): ?>
                    <p><?= $done ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Product submission form -->
        <form action="allproducts4.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Product</button>
        </form>
    </div>
</body>
</html>

<?php
// Clear session messages after displaying them
unset($_SESSION['errors']);
unset($_SESSION['done']);
?>
