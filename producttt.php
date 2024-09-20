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

try {
    // Initialize database connection
    $pdo = (new DBManager())->getConnecetion();
    
    // Fetch all products from the database
    $sql = "SELECT * FROM `products`";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['errors'] = [$e->getMessage()];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">All Products</h2>

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

        <!-- Display products in a grid -->
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card" style="width: 18rem;">
                        <img src="<?= ($product['image']) ?>" class="card-img-top" alt="<?= ($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= ($product['name']) ?></h5>
                            <p class="card-text">Quantity: <?= ($product['quantity']) ?></p>
                            <p class="card-text">Price: $<?= ($product['price']) ?></p>
                            <p class="card-text"><?= ($product['description']) ?></p>
                            <a href="add_to_cart.php?name=<?= urlencode($product['name']) ?>&price=<?= urlencode($product['price']) ?>&image=<?= urlencode($product['image']) ?>&quantity=<?= urlencode($product['quantity']) ?>" class="btn btn-primary">Add to Cart</a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
// Clear session messages after displaying them
unset($_SESSION['errors']);
unset($_SESSION['done']);
?>
