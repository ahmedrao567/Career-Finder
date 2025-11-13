<?php
include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Project ID not provided']);
    exit();
}

$project_id = $_GET['id'];
$company_id = $_SESSION['company_id'];

// Verify ownership and get project data
$project = getProjectById($pdo, $project_id, $company_id);

if (!$project) {
    echo json_encode(['success' => false, 'message' => 'Project not found']);
    exit();
}

echo json_encode([
    'success' => true,
    'id' => $project['id'],
    'project_title' => $project['project_title'],
    'project_description' => $project['project_description'],
    'project_link' => $project['project_link'],
    'technologies' => $project['technologies'],
    'project_date' => $project['project_date']
]);
?>