<?php
require 'DBManger.php';
session_start();

// Check if user is authenticated
$is_authenticated = !empty($_SESSION['user']);
if (!$is_authenticated) {
    $_SESSION['errors'] = ['Please log in first!'];
    header('Location: login.php');
    exit;
}

$pdo = (new DBManager())->getConnecetion();

// Fetching reviews from the database
$sql = 'SELECT * FROM review';
$stmt = $pdo->query($sql);
$reviews = $stmt->fetchAll(PDO::FETCH_OBJ);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $reviewId = $_GET['id'];

    $sql = 'DELETE FROM review WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([':id' => $reviewId]);
        $_SESSION['done'] = ['Review deleted successfully!'];
    } catch (PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
    }
    header('Location: crudreviews.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Reviews</title>
    <!-- Include Bootstrap CSS here if needed -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-container {
            margin-top: 5rem;
        }
        .alert {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container table-container">
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['done'])): ?>
            <div class="alert alert-success">
                <?php foreach ($_SESSION['done'] as $message): ?>
                    <p><?= htmlspecialchars($message) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['done']); ?>
            </div>
        <?php endif; ?>

        <section class="h-100">
            <div class="card w-100 bg-transparent text-light text-center border-0">
                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?= ($review->id) ?></td>
                                    <td><?= ($review->fname) ?></td>
                                    <td><?= ($review->lname) ?></td>
                                    <td><a href="mailto:<?= ($review->email) ?>" class="text-decoration-none"><?= ($review->email) ?></a></td>
                                    <td><a href="tel:<?= ($review->phone) ?>" class="text-decoration-none"><?= ($review->phone) ?></a></td>
                                    <td><?= ($review->description) ?></td>
                                    <td>
                                        <a href="editreview.php?id=<?= ($review->id) ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>edit</a>
                                        <a href="crudreviews.php?action=delete&id=<?= ($review->id) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Include Bootstrap JS and dependencies here if needed -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
