<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    $company_name = $_SESSION['company_name'];
    $post_text = trim($_POST['post_text']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Handle file upload
    $post_image = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../user-dashboard/assets/uploads/";
        $file_extension = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        $file_name = "post_" . $company_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        // Check if uploads directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Validate file
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (in_array($file_extension, $allowed_extensions)) {
            if ($_FILES['post_image']['size'] <= 5000000) { // 5MB
                if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                    $post_image = $file_name;
                }
            }
        }
    }

    // Insert post
    $stmt = $pdo->prepare("
        INSERT INTO posts (poster_id, company_id, poster_type, poster_name, post_text, post_image, is_published) 
        VALUES (?, ?, 'company', ?, ?, ?, ?)
    ");

    if ($stmt->execute([$company_id, $company_id, $company_name, $post_text, $post_image, $is_published])) {
        $_SESSION['success'] = "Post " . ($is_published ? "published" : "saved as draft") . " successfully!";
    } else {
        $_SESSION['error'] = "Failed to create post. Please try again.";
    }

    header("Location: posts.php");
    exit();
}
?>