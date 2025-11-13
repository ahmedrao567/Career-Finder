<?php
include 'config.php'; // Changed from ../config.php to config.php

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Experience ID not provided']);
    exit();
}

$exp_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify ownership and get experience data
$stmt = $pdo->prepare("SELECT * FROM user_experiences WHERE id = ? AND user_id = ?");
$stmt->execute([$exp_id, $user_id]);
$experience = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$experience) {
    echo json_encode(['success' => false, 'message' => 'Experience not found']);
    exit();
}

// Format dates for input fields
$start_date = date('Y-m', strtotime($experience['start_date']));
$end_date = $experience['end_date'] ? date('Y-m', strtotime($experience['end_date'])) : '';

echo json_encode([
    'success' => true,
    'position' => $experience['position'],
    'company' => $experience['company'],
    'start_date' => $start_date,
    'end_date' => $end_date,
    'description' => $experience['description'],
    'current_job' => (bool)$experience['current_job']
]);
?>