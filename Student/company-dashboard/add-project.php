<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    $project_title = trim($_POST['project_title']);
    $project_description = trim($_POST['project_description']);
    $project_link = trim($_POST['project_link']);
    $technologies = trim($_POST['technologies']);
    $project_date = $_POST['project_date'] ?: null;
    
    // Handle file upload
    $project_thumbnail = null;
    if (isset($_FILES['project_thumbnail']) && $_FILES['project_thumbnail']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/uploads/";
        $file_extension = strtolower(pathinfo($_FILES["project_thumbnail"]["name"], PATHINFO_EXTENSION));
        $file_name = "project_" . $company_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        
        // Check if uploads directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Check file size and type
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if ($_FILES["project_thumbnail"]["size"] <= 2000000 && in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($_FILES["project_thumbnail"]["tmp_name"], $target_file)) {
                $project_thumbnail = $file_name;
            }
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO company_projects (company_id, project_title, project_description, project_thumbnail, project_link, technologies, project_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$company_id, $project_title, $project_description, $project_thumbnail, $project_link, $technologies, $project_date])) {
        $_SESSION['success'] = "Project added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add project. Please try again.";
    }
    
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>