<?php
require "DBManger.php";  // Include your database manager file

// Fetch all the manager IDs from the manager table
try {
    $pdo = new DBManager();
    $sql = "SELECT id FROM manager";  // Assuming 'id' is the column that stores manager IDs
    $stmt = $pdo->getConnecetion()->query($sql);
    $managerIds = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Manager ID</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Select Manager ID</h2>

        <form action="process.php" method="post">
            <div class="mb-3">
                <label for="manager_id" class="form-label">Manager ID</label>
                <select class="form-control" id="manager_id" name="manager_id">
                    <option value="">Select a Manager ID</option>
                    <?php foreach ($managerIds as $manager): ?>
                        <option value="<?= htmlspecialchars($manager['id']) ?>"><?= htmlspecialchars($manager['id']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
