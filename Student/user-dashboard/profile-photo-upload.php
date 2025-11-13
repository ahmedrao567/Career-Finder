<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $user_id = $_SESSION['user_id'];
    
    // File upload configuration
    $target_dir = "assets/uploads/";
    $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
    $file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Check if uploads directory exists, if not create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Check file size (max 2MB)
    if ($_FILES["profile_photo"]["size"] > 2000000) {
        $_SESSION['error'] = "Sorry, your file is too large. Maximum size is 2MB.";
        header("Location: profile.php");
        exit();
    }
    
    // Allow certain file formats
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        header("Location: profile.php");
        exit();
    }
    
    // Check if file upload was successful
    if ($_FILES["profile_photo"]["error"] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        header("Location: profile.php");
        exit();
    }
    
    // Check image dimensions (square image preferred)
    list($width, $height) = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($width < 100 || $height < 100) {
        $_SESSION['error'] = "Image dimensions should be at least 100x100 pixels.";
        header("Location: profile.php");
        exit();
    }
    
    // Try to upload file
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE user_profiles SET profile_photo = ? WHERE user_id = ?");
        
        if ($stmt->execute([$file_name, $user_id])) {
            $_SESSION['success'] = "Profile photo updated successfully!";
        } else {
            $_SESSION['error'] = "Database update failed.";
        }
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
    }
} else {
    $_SESSION['error'] = "No file uploaded.";
}

header("Location: profile.php");
exit();
?>