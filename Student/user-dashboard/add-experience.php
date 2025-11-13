<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $company = trim($_POST['company']);
    $position = trim($_POST['position']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $current_job = isset($_POST['current_job']) ? 1 : 0;
    
    // If it's current job, set end_date to null
    if ($current_job) {
        $end_date = null;
    }
    
    $stmt = $pdo->prepare("INSERT INTO user_experiences (user_id, company, position, description, start_date, end_date, current_job) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $company, $position, $description, $start_date, $end_date, $current_job])) {
        $_SESSION['success'] = "Experience added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add experience. Please try again.";
    }
    
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>