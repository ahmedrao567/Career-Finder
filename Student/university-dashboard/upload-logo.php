<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $university_id = $_SESSION['university_id'];
    
    // File upload configuration
    $target_dir = "assets/uploads/";
    $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $file_name = "logo_" . $university_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    // Check if uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Check file size (max 5MB)
    if ($_FILES['logo']['size'] > 5000000) {
        $_SESSION['error'] = "Sorry, your file is too large. Maximum size is 5MB.";
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

    // Try to upload file
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE universities SET logo = ? WHERE id = ?");
        if ($stmt->execute([$file_name, $university_id])) {
            $_SESSION['success'] = "Logo updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update logo in database.";
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