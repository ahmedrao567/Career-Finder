<?php
include 'config.php';

$user_id = $_SESSION['user_id'];

// Remove university information
$stmt = $pdo->prepare("UPDATE user_profiles SET university = NULL, degree = NULL, field_of_study = NULL, graduation_year = NULL WHERE user_id = ?");

if ($stmt->execute([$user_id])) {
    $_SESSION['success'] = "Education information removed successfully!";
} else {
    $_SESSION['error'] = "Failed to remove education information. Please try again.";
}

header("Location: profile.php");
exit();
?>