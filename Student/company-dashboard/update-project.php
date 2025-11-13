<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    $project_id = $_POST['project_id'];
    $project_title = trim($_POST['project_title']);
    $project_description = trim($_POST['project_description']);
    $project_link = trim($_POST['project_link']);
    $technologies = trim($_POST['technologies']);
    $project_date = $_POST['project_date'] ?: null;
    
    // Verify ownership
    $project = getProjectById($pdo, $project_id, $company_id);
    if (!$project) {
        $_SESSION['error'] = "Project not found or you don't have permission to edit it.";
        header("Location: profile.php");
        exit();
    }
    
    // Handle file upload if new thumbnail provided
    $project_thumbnail = $project['project_thumbnail'];
    if (isset($_FILES['project_thumbnail']) && $_FILES['project_thumbnail']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/uploads/";
        $file_extension = strtolower(pathinfo($_FILES["project_thumbnail"]["name"], PATHINFO_EXTENSION));
        $file_name = "project_" . $company_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if ($_FILES["project_thumbnail"]["size"] <= 2000000 && in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($_FILES["project_thumbnail"]["tmp_name"], $target_file)) {
                $project_thumbnail = $file_name;
            }
        }
    }
    
    $stmt = $pdo->prepare("UPDATE company_projects SET project_title = ?, project_description = ?, project_thumbnail = ?, project_link = ?, technologies = ?, project_date = ? WHERE id = ? AND company_id = ?");
    
    if ($stmt->execute([$project_title, $project_description, $project_thumbnail, $project_link, $technologies, $project_date, $project_id, $company_id])) {
        $_SESSION['success'] = "Project updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update project. Please try again.";
    }
    
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>