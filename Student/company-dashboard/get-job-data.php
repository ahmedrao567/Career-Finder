<?php
include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Job ID not provided']);
    exit();
}

$job_id = $_GET['id'];
$company_id = $_SESSION['company_id'];

// Verify ownership and get job data
$job = getJobById($pdo, $job_id, $company_id);

if (!$job) {
    echo json_encode(['success' => false, 'message' => 'Job not found']);
    exit();
}

echo json_encode([
    'success' => true,
    'id' => $job['id'],
    'title' => $job['title'],
    'description' => $job['description'],
    'requirements' => $job['requirements'],
    'location' => $job['location'],
    'type' => $job['type'],
    'salary_range' => $job['salary_range'],
    'application_deadline' => $job['application_deadline']
]);
?>