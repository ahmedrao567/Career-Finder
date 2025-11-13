<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $university = trim($_POST['university']);
    $degree = trim($_POST['degree']);
    $field_of_study = trim($_POST['field_of_study']);
    $graduation_year = $_POST['graduation_year'] ?: null;
    
    // Check if profile exists
    $profile = getUserProfile($pdo, $user_id);
    
    if ($profile) {
        // Update existing profile
        $stmt = $pdo->prepare("UPDATE user_profiles SET university = ?, degree = ?, field_of_study = ?, graduation_year = ? WHERE user_id = ?");
        $stmt->execute([$university, $degree, $field_of_study, $graduation_year, $user_id]);
    } else {
        // Create new profile
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, university, degree, field_of_study, graduation_year) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $university, $degree, $field_of_study, $graduation_year]);
    }
    
    $_SESSION['success'] = "Education information updated successfully!";
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>