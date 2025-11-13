<?php
include 'config.php';

if (isset($_GET['id'])) {
    $skill_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify ownership
    $stmt = $pdo->prepare("DELETE FROM user_skills WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$skill_id, $user_id])) {
        $_SESSION['success'] = "Skill deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete skill.";
    }
}

header("Location: profile.php");
exit();
?>