<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $company_id = $_SESSION['company_id'];
    
    // File upload configuration
    $target_dir = "../assets/uploads/";
    
    // Check if uploads directory exists, if not create it
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $_SESSION['error'] = "Failed to create upload directory.";
            header("Location: profile.php");
            exit();
        }
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        $_SESSION['error'] = "Upload directory is not writable.";
        header("Location: profile.php");
        exit();
    }
    
    $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
    $file_name = "company_logo_" . $company_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Debug: Check file information
    error_log("File upload attempt: " . $_FILES["logo"]["name"]);
    error_log("File size: " . $_FILES["logo"]["size"]);
    error_log("File error: " . $_FILES["logo"]["error"]);
    error_log("Target file: " . $target_file);
    
    // Check file size (max 2MB)
    if ($_FILES["logo"]["size"] > 2000000) {
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
    if ($_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "File upload error: " . $_FILES["logo"]["error"];
        header("Location: profile.php");
        exit();
    }
    
    // Try to upload file
    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE company_profiles SET logo = ? WHERE company_id = ?");
        
        if ($stmt->execute([$file_name, $company_id])) {
            $_SESSION['success'] = "Logo updated successfully!";
            error_log("Logo uploaded successfully: " . $file_name);
        } else {
            $errorInfo = $stmt->errorInfo();
            $_SESSION['error'] = "Database update failed: " . $errorInfo[2];
            error_log("Database error: " . $errorInfo[2]);
        }
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file. Check server permissions.";
        error_log("File move failed for: " . $target_file);
    }
} else {
    $_SESSION['error'] = "No file uploaded or invalid request.";
}

header("Location: profile.php");
exit();
?>