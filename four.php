<?php
require "DBManger.php";
require "Server.php";
require "Request.php";

session_start();

// Initialize Server and Request objects
$server = new Server();
$request = new Request();

if ($server->isPostRequest()) {
    $name = $request->get('name');
    $email = $request->get('email');  
    $phone = $request->get('phone');
    $manager_id = $request->get('manager_id');
    $target_file = '';

    // Handle file upload
    if ($request->hasFile('picture')) {
        $file = $request->file('picture');
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $file['tmp_name'];
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = time() . '.' . $fileExtension; // Use the current time to generate a unique file name
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($newFileName);

            if (move_uploaded_file($fileTmpPath, $target_file)) {
                // Image uploaded successfully
            } else {
                $_SESSION['errors'] = ['Image upload failed.'];
                header("Location: two.php");
                exit;
            }
        } else {
            $_SESSION['errors'] = ['File upload error: ' . $file['error']];
            header("Location: two.php");
            exit;
        }
    }

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($manager_id)) {
        $_SESSION['errors'] = ['All fields are required'];
        header("Location: two.php");
        exit;
    }

    try {
        // Initialize database connection
        $pdo = new DBManager();
        $sql = "INSERT INTO employee (name, email, phone, picture, manager_id) VALUES (:name, :email, :phone, :picture, :manager_id)";
        $stmt = $pdo->getConnecetion()->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':picture', $target_file);
        $stmt->bindParam(':manager_id', $manager_id);
        $stmt->execute();

        $_SESSION['done'] = ['Employee added successfully!'];
        header("Location: one.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: two.php");
        exit;
    }
}
?>
