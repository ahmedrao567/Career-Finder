<?php
session_start();
require_once "config.php"; // include your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['delete_account'])) {
    try {
        // Prepare delete query
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Logout user after deletion
            session_destroy();
            header("Location: goodbye.php"); // or login page
            exit();
        } else {
            echo "Error deleting your account. Please try again later.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
