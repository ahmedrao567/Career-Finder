<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $skill_name = trim($_POST['skill_name']);
    $proficiency = $_POST['proficiency'];
    
    $stmt = $pdo->prepare("INSERT INTO user_skills (user_id, skill_name, proficiency) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$user_id, $skill_name, $proficiency])) {
        $_SESSION['success'] = "Skill added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add skill. Please try again.";
    }
    
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>