<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['session_token'])) {
    die(json_encode([]));
}

$db = new Database();
$pdo = $db->getConnection();

$query = sanitizeInput($_POST['query']);
$type = sanitizeInput($_POST['type']);

$results = [];

switch($type) {
    case 'user':
        $stmt = $pdo->prepare("SELECT id, full_name as name FROM users WHERE full_name LIKE ? OR username LIKE ? LIMIT 10");
        $stmt->execute(["%$query%", "%$query%"]);
        $results = $stmt->fetchAll();
        break;
        
    case 'company':
        $stmt = $pdo->prepare("SELECT id, company_name as name FROM companies WHERE company_name LIKE ? LIMIT 10");
        $stmt->execute(["%$query%"]);
        $results = $stmt->fetchAll();
        break;
        
    case 'university':
        $stmt = $pdo->prepare("SELECT id, university_name as name FROM universities WHERE university_name LIKE ? LIMIT 10");
        $stmt->execute(["%$query%"]);
        $results = $stmt->fetchAll();
        break;
}

echo json_encode($results);
?>