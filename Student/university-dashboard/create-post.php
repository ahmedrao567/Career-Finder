<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university_id = $_SESSION['university_id'];
    $post_text = trim($_POST['post_text']);
    
    // Validate post text
    if (empty($post_text)) {
        $_SESSION['error'] = "Post text cannot be empty.";
        header("Location: index.php");
        exit();
    }
    
    // Handle image upload
    $post_image = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../user-dashboard/assets/uploads/";
        $file_extension = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        $file_name = "post_" . $university_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        // Check if uploads directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Check file size (max 5MB)
        if ($_FILES['post_image']['size'] > 5000000) {
            $_SESSION['error'] = "Sorry, your file is too large. Maximum size is 5MB.";
            header("Location: index.php");
            exit();
        }

        // Allow certain file formats
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: index.php");
            exit();
        }

        // Try to upload file
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
            $post_image = $file_name;
        }
    }

    // Create the post
    if (createUniversityPost($pdo, $university_id, $post_text, $post_image)) {
        $_SESSION['success'] = "Post created successfully!";
    } else {
        $_SESSION['error'] = "Failed to create post. Please try again.";
    }

    header("Location: index.php");
    exit();
}
?>