<?php
include 'config.php';

if (isset($_GET['id'])) {
    $exp_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify ownership
    $stmt = $pdo->prepare("DELETE FROM user_experiences WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$exp_id, $user_id])) {
        $_SESSION['success'] = "Experience deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete experience.";
    }
} else {
    $_SESSION['error'] = "Experience ID not provided.";
}

header("Location: profile.php");
exit();
?>