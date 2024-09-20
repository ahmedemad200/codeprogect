<?php
session_start();

// Check if user is authenticated
if (empty($_SESSION['user'])) {
    $_SESSION['errors'] = ['Please log in first!'];
    header('Location: login.php');
    exit;
}

// Check if cart is empty
$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Shopping Cart</h2>

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

        <!-- Cart Table -->
        <?php if (!empty($cart)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart as $item):
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                        <tr>
                            <td><img src="<?= ($item['image']) ?>" alt="<?= ($item['name']) ?>" style="width: 100px;"></td>
                            <td><?= ($item['name']) ?></td>
                            <td>$<?= ($item['price']) ?></td>
                            <td><?= ($item['quantity']) ?></td>
                            <td>$<?= number_format($itemTotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                        <td>$<?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Clear session messages after displaying them
unset($_SESSION['errors']);
unset($_SESSION['done']);
?>
