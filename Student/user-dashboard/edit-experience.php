<?php
include 'config.php'; // Changed from ../config.php to config.php

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get experience ID from URL parameter
    $exp_id = $_GET['id'] ?? null;
    
    if (!$exp_id) {
        $_SESSION['error'] = "Experience ID not provided.";
        header("Location: profile.php");
        exit();
    }

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
    
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM user_experiences WHERE id = ? AND user_id = ?");
    $stmt->execute([$exp_id, $user_id]);
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "Experience not found or you don't have permission to edit it.";
        header("Location: profile.php");
        exit();
    }
    
    $stmt = $pdo->prepare("UPDATE user_experiences SET company = ?, position = ?, description = ?, start_date = ?, end_date = ?, current_job = ? WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$company, $position, $description, $start_date, $end_date, $current_job, $exp_id, $user_id])) {
        $_SESSION['success'] = "Experience updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update experience. Please try again.";
    }
    
    header("Location: profile.php");
    exit();
} else {
    // If it's a GET request, redirect to profile
    header("Location: profile.php");
    exit();
}
?>