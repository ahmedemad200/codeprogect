<?php
require "DBManger.php";
require "Server.php";
require "Request.php";

session_start();

// Initialize Server and Request objects
$server = new Server();
$request = new Request();

// Handle the delete action
if ($server->isGetRequest() && $request->equal('action', 'delete')) {
    $employee_id = $request->get('id');

    if ($employee_id) {
        try {
            // Initialize database connection
            $pdo = new DBManager();
            $sql = "DELETE FROM `employee` WHERE id=?";
            $stmt = $pdo->getConnecetion()->prepare($sql);
            
            // Execute the query with the provided employee ID
            $stmt->execute([$employee_id]);

            if ($stmt->rowCount() <= 0) {
                $_SESSION['errors'] = ['No employee found with the provided id!'];
            } else {
                $_SESSION['done'] = ['Employee deleted successfully!'];
            }
        } catch (\PDOException $e) {
            $_SESSION['errors'] = ['Error deleting employee: ' . $e->getMessage()];
        }

        // Redirect back to the list page
        header("Location: cc.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Employee List</h2>

        <!-- Display error messages -->
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); // Clear errors after displaying ?>
            </div>
        <?php endif; ?>

        <!-- Display success messages -->
        <?php if (!empty($_SESSION['done'])): ?>
            <div class="alert alert-success">
                <?php foreach ($_SESSION['done'] as $done): ?>
                    <p><?= htmlspecialchars($done) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['done']); // Clear success messages after displaying ?>
            </div>
        <?php endif; ?>

        <!-- Employee Table -->
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Picture</th>
                    <th>Manager ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch employee data from the database
                $pdo = new DBManager();
                $sql = "SELECT * FROM employee";
                $stmt = $pdo->getConnecetion()->query($sql);

                foreach ($stmt as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row['picture']) . "' alt='Employee Picture' style='width: 50px; height: 50px;'></td>";
                    echo "<td>" . htmlspecialchars($row['manager_id']) . "</td>";
                    // Add Edit and Delete buttons
                    echo "<td>
                            <a href='edit.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-warning'>Edit</a>
                            <a href='cc.php?action=delete&id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
