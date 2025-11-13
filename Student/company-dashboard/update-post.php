<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $company_id = $_SESSION['company_id'];
    $post_text = trim($_POST['post_text']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND company_id = ?");
    $stmt->execute([$post_id, $company_id]);
    
    if ($stmt->fetch()) {
        // Handle file upload if new image provided
        $post_image = null;
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../user-dashboard/assets/uploads/";
            $file_extension = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
            $file_name = "post_" . $company_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $file_name;

            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            if (in_array($file_extension, $allowed_extensions) && $_FILES['post_image']['size'] <= 5000000) {
                if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                    $post_image = $file_name;
                }
            }
        }

        // Update post
        if ($post_image) {
            $stmt = $pdo->prepare("UPDATE posts SET post_text = ?, post_image = ?, is_published = ? WHERE id = ?");
            $stmt->execute([$post_text, $post_image, $is_published, $post_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE posts SET post_text = ?, is_published = ? WHERE id = ?");
            $stmt->execute([$post_text, $is_published, $post_id]);
        }

        $_SESSION['success'] = "Post updated successfully!";
    } else {
        $_SESSION['error'] = "Post not found or you don't have permission to edit it.";
    }

    header("Location: posts.php");
    exit();
}
?>