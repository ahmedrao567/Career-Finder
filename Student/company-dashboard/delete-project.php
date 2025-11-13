<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $project_id = $_GET['id'];
    $company_id = $_SESSION['company_id'];
    
    // Verify ownership
    $project = getProjectById($pdo, $project_id, $company_id);
    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit();
    }
    
    $stmt = $pdo->prepare("DELETE FROM company_projects WHERE id = ? AND company_id = ?");
    
    if ($stmt->execute([$project_id, $company_id])) {
        echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete project']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>