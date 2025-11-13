<?php
session_start();
include 'config.php';

echo "<h2>Programs Debug Information</h2>";

// Check session
echo "<h3>Session Info:</h3>";
echo "University ID: " . ($_SESSION['university_id'] ?? 'NOT SET') . "<br>";

// Check programs table
echo "<h3>Database Info:</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM programs");
    $result = $stmt->fetch();
    echo "Total programs in database: " . $result['count'] . "<br>";
    
    // Show programs for current university
    if (isset($_SESSION['university_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM programs WHERE university_id = ?");
        $stmt->execute([$_SESSION['university_id']]);
        $programs = $stmt->fetchAll();
        
        echo "<h3>Your Programs:</h3>";
        if (empty($programs)) {
            echo "No programs found for your university.<br>";
        } else {
            foreach ($programs as $program) {
                echo "ID: {$program['id']}, Name: {$program['program_name']}, Category: {$program['program_category']}, Merit: {$program['closing_merit']}<br>";
            }
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Check if handlers are accessible
echo "<h3>Handler Test:</h3>";
echo "programs_handler.php is " . (file_exists('programs_handler.php') ? 'FOUND' : 'MISSING') . "<br>";
?>