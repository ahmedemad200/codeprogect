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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    // Validate form inputs
    if (empty($name) || empty($quantity) || empty($price) || empty($description)) {
        $_SESSION['errors'] = ['All fields are required.'];
        header("Location: teachers-edit.php?id=$id");
        exit;
    }

    // Handle image upload
    $target_file = ''; // Default to empty, use existing image if not updated
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $valid_extensions) && move_uploaded_file($image["tmp_name"], $target_file)) {
            // Image uploaded successfully
        } else {
            $_SESSION['errors'] = ['Failed to upload image or invalid file type.'];
            header("Location: teachers-edit.php?id=$id");
            exit;
        }
    }

    // Fetch existing image if no new image is uploaded
    if (empty($target_file)) {
        try {
            $pdo = new DBManager();
            $sql = "SELECT image FROM products WHERE id = :id";
            $args = [':id' => $id];
            $stmt = $pdo->query($sql, ...$args);
            $product = $stmt->fetch(PDO::FETCH_OBJ);
            $target_file = $product->image;
        } catch (\PDOException $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            header("Location: teachers-edit.php?id=$id");
            exit;
        }
    }

    try {
        // Initialize database connection
        $pdo = new DBManager();

        // Update product in the database
        $sql = "UPDATE products SET name = :name, quantity = :quantity, price = :price, image = :image, description = :description WHERE id = :id";
        $args = [
            ':name' => $name,
            ':quantity' => $quantity,
            ':price' => $price,
            ':image' => $target_file,
            ':description' => $description,
            ':id' => $id
        ];
        $pdo->query($sql, ...$args);

        // Success message
        $_SESSION['done'] = ['Product updated successfully!'];
        header('Location: allproducts2.php');
        exit;

    } catch (\PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: teachers-edit.php?id=$id");
        exit;
    }
}

// Fetch the product data
$product = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $pdo = new DBManager();
        $sql = "SELECT * FROM products WHERE id = :id";
        $args = [':id' => $id];
        $stmt = $pdo->query($sql, ...$args);
        $product = $stmt->fetch(PDO::FETCH_OBJ);
    } catch (\PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header('Location: allproducts2.php');
        exit;
    }
}

if (!$product) {
    $_SESSION['errors'] = ['Product not found.'];
    header('Location: allproducts2.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Product</h2>

        <!-- Error and success messages -->
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= ($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['done'])): ?>
            <div class="alert alert-success">
                <?php foreach ($_SESSION['done'] as $done): ?>
                    <p><?= ($done) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Edit form -->
        <form action="teachers-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= ($product->id) ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= ($product->name) ?>" required>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?= ($product->quantity) ?>" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?= ($product->price) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= ($product->description) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <img src="<?= ($product->image) ?>" alt="Product image" class="mt-2" style="width: 150px;">
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>

<?php
// Clear session messages after displaying them
unset($_SESSION['errors']);
unset($_SESSION['done']);
?>
