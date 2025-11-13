<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1);

if (!isset($_SESSION['university_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated. Please log in again.']);
    exit;
}

$university_id = $_SESSION['university_id'];
$action = $_POST['action'] ?? '';

error_log("Programs Handler - Action: $action, University ID: $university_id");

if ($action === 'save_program') {
    handleSaveProgram($pdo, $university_id);
} elseif ($action === 'delete_program') {
    handleDeleteProgram($pdo, $university_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
    exit;
}

function handleSaveProgram($pdo, $university_id) {
    try {
        $program_id = $_POST['program_id'] ?? '';
        $program_name = trim($_POST['program_name'] ?? '');
        $program_category = trim($_POST['program_category'] ?? '');
        $closing_merit = isset($_POST['closing_merit']) ? floatval($_POST['closing_merit']) : 0;

        error_log("Save Program - ID: $program_id, Name: $program_name, Category: $program_category, Merit: $closing_merit");

        // Validation
        if (empty($program_name)) {
            throw new Exception('Program name is required.');
        }

        if (empty($program_category)) {
            throw new Exception('Program category is required.');
        }

        if ($closing_merit < 0 || $closing_merit > 100) {
            throw new Exception('Closing merit must be between 0 and 100.');
        }

        if (!empty($program_id)) {
            // Update existing program
            $stmt = $pdo->prepare("UPDATE programs SET program_name = ?, program_category = ?, closing_merit = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND university_id = ?");
            $result = $stmt->execute([$program_name, $program_category, $closing_merit, $program_id, $university_id]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Program updated successfully!']);
            } else {
                throw new Exception('Program not found or no changes made.');
            }
        } else {
            // Insert new program
            $stmt = $pdo->prepare("INSERT INTO programs (university_id, program_name, program_category, closing_merit) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$university_id, $program_name, $program_category, $closing_merit]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Program added successfully!']);
            } else {
                throw new Exception('Failed to add program to database.');
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in handleSaveProgram: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("Error in handleSaveProgram: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleDeleteProgram($pdo, $university_id) {
    try {
        $program_id = $_POST['program_id'] ?? '';

        error_log("Delete Program - ID: $program_id");

        if (empty($program_id)) {
            throw new Exception('Program ID is required for deletion.');
        }

        if (!is_numeric($program_id)) {
            throw new Exception('Invalid program ID.');
        }

        // First, verify the program belongs to the university
        $checkStmt = $pdo->prepare("SELECT id FROM programs WHERE id = ? AND university_id = ?");
        $checkStmt->execute([$program_id, $university_id]);
        
        if ($checkStmt->rowCount() === 0) {
            throw new Exception('Program not found or you do not have permission to delete it.');
        }

        // Delete the program
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ? AND university_id = ?");
        $result = $stmt->execute([$program_id, $university_id]);

        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully!']);
        } else {
            throw new Exception('Failed to delete program from database.');
        }
    } catch (PDOException $e) {
        error_log("Database error in handleDeleteProgram: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("Error in handleDeleteProgram: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>