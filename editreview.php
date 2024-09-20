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

// Initialize database connection
$pdo = (new DBManager())->getConnecetion();

if (isset($_GET['id'])) {
    $reviewId = $_GET['id'];

    // Fetch review details for editing
    $sql = 'SELECT * FROM review WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $reviewId]);
    $review = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$review) {
        $_SESSION['errors'] = ['Review not found.'];
        header('Location: crudreviews.php');
        exit;
    }
} else {
    $_SESSION['errors'] = ['No review ID provided.'];
    header('Location: crudreviews.php');
    exit;
}

// Handle form submission for updating the review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    extract($_POST);

    // Validate form inputs
    if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($description)) {
        $_SESSION['errors'] = ['All fields are required.'];
        header("Location: editreview.php?id=$reviewId");
        exit;
    }

    // Update review in the database
    $sql = 'UPDATE review SET fname = :fname, lname = :lname, email = :email, phone = :phone, description = :description WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':email' => $email,
            ':phone' => $phone,
            ':description' => $description,
            ':id' => $reviewId
        ]);
        $_SESSION['done'] = ['Review updated successfully!'];
        header('Location: crudreviews.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: editreview.php?id=$reviewId");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review</title>
    <!-- Include Bootstrap CSS here if needed -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= ($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['done'])): ?>
            <div class="alert alert-success">
                <?php foreach ($_SESSION['done'] as $message): ?>
                    <p><?= ($message) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['done']); ?>
            </div>
        <?php endif; ?>

        <h2>Edit Review</h2>
        <form action="editreview.php?id=<?= ($reviewId) ?>" method="post">
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?= ($review->fname) ?>" required>
            </div>
            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?= ($review->lname) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= ($review->phone) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= ($review->email) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= ($review->description) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Review</button>
        </form>
    </div>

    <!-- Include Bootstrap JS and dependencies here if needed -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
