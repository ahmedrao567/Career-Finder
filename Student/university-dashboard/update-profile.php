<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university_id = $_SESSION['university_id'];
    
    // Basic information
    $category = trim($_POST['category']);
    $established_year = $_POST['established_year'] ?: null;
    $sector = $_POST['sector'];
    $chartered_by = trim($_POST['chartered_by']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $is_recognized = isset($_POST['is_recognized']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("
            UPDATE universities 
            SET category = ?, established_year = ?, sector = ?, chartered_by = ?, 
                city = ?, province = ?, is_recognized = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");

        if ($stmt->execute([$category, $established_year, $sector, $chartered_by, $city, $province, $is_recognized, $university_id])) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update profile. Please try again.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: profile.php");
    exit();
}
?>