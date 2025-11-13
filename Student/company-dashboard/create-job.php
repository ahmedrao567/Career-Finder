<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $location = trim($_POST['location']);
    $type = $_POST['type'];
    $salary_range = trim($_POST['salary_range']);
    $application_deadline = $_POST['application_deadline'] ?: null;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (company_id, title, description, requirements, location, type, salary_range, application_deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$company_id, $title, $description, $requirements, $location, $type, $salary_range, $application_deadline])) {
            $_SESSION['success'] = "Job posted successfully!";
        } else {
            $errorInfo = $stmt->errorInfo();
            $_SESSION['error'] = "Failed to post job. Database error: " . $errorInfo[2];
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