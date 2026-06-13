<?php
// save_program.php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Check if university is logged in
if (!isset($_SESSION['university_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit;
}

$university_id = $_SESSION['university_id'];
$action = $_POST['action'] ?? '';

if ($action === 'save_program') {
    // Get form data
    $program_id = $_POST['program_id'] ?? '';
    $program_name = trim($_POST['program_name'] ?? '');
    $program_category = trim($_POST['program_category'] ?? '');
    $closing_merit = floatval($_POST['closing_merit'] ?? 0);
    
    // Validation
    if (empty($program_name)) {
        echo json_encode(['success' => false, 'message' => 'Program name is required']);
        exit;
    }
    
    if (empty($program_category)) {
        echo json_encode(['success' => false, 'message' => 'Program category is required']);
        exit;
    }
    
    if ($closing_merit < 0 || $closing_merit > 100) {
        echo json_encode(['success' => false, 'message' => 'Closing merit must be between 0 and 100']);
        exit;
    }
    
    try {
        if (!empty($program_id)) {
            // Update existing program
            $stmt = $pdo->prepare("UPDATE programs SET program_name = ?, program_category = ?, closing_merit = ? WHERE id = ? AND university_id = ?");
            $stmt->execute([$program_name, $program_category, $closing_merit, $program_id, $university_id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Program updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made or program not found']);
            }
        } else {
            // Insert new program
            $stmt = $pdo->prepare("INSERT INTO programs (university_id, program_name, program_category, closing_merit) VALUES (?, ?, ?, ?)");
            $stmt->execute([$university_id, $program_name, $program_category, $closing_merit]);
            
            echo json_encode(['success' => true, 'message' => 'Program added successfully']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} elseif ($action === 'delete_program') {
    $program_id = $_POST['program_id'] ?? '';
    
    if (empty($program_id)) {
        echo json_encode(['success' => false, 'message' => 'Program ID is required']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ? AND university_id = ?");
        $stmt->execute([$program_id, $university_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Program not found or already deleted']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>