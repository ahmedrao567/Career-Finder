<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $designation = trim($_POST['designation']);
    $location = trim($_POST['location']);
    $website = trim($_POST['website']);
    $about = trim($_POST['about']);
    
    // Check if profile exists
    $profile = getUserProfile($pdo, $user_id);
    
    if ($profile) {
        // Update existing profile
        $stmt = $pdo->prepare("UPDATE user_profiles SET designation = ?, location = ?, website = ?, about = ? WHERE user_id = ?");
        $stmt->execute([$designation, $location, $website, $about, $user_id]);
    } else {
        // Create new profile
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, designation, location, website, about) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $designation, $location, $website, $about]);
    }
    
    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>