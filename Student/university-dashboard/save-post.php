<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $university_id = $_SESSION['university_id'];
    
    if (toggleSavePostUniversity($pdo, $university_id, $post_id)) {
        // Check if post is now saved
        $stmt = $pdo->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$university_id, $post_id]);
        $is_saved = $stmt->fetch() ? true : false;
        
        echo json_encode([
            'success' => true,
            'saved' => $is_saved,
            'message' => $is_saved ? 'Post saved successfully!' : 'Post removed from saved!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update post save status'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>