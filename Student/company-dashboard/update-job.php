<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    $job_id = $_POST['job_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $location = trim($_POST['location']);
    $type = $_POST['type'];
    $salary_range = trim($_POST['salary_range']);
    $application_deadline = $_POST['application_deadline'] ?: null;
    
    // Verify ownership
    $job = getJobById($pdo, $job_id, $company_id);
    if (!$job) {
        $_SESSION['error'] = "Job not found or you don't have permission to edit it.";
        header("Location: jobs.php");
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE jobs SET title = ?, description = ?, requirements = ?, location = ?, type = ?, salary_range = ?, application_deadline = ? WHERE id = ? AND company_id = ?");
        
        if ($stmt->execute([$title, $description, $requirements, $location, $type, $salary_range, $application_deadline, $job_id, $company_id])) {
            $_SESSION['success'] = "Job updated successfully!";
        } else {
            $errorInfo = $stmt->errorInfo();
            $_SESSION['error'] = "Failed to update job. Database error: " . $errorInfo[2];
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: jobs.php");
    exit();
} else {
    header("Location: jobs.php");
    exit();
}
?>