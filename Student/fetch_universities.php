<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();

// Database configuration
$host = 'localhost';
$dbname = 'career_finder'; // Your database name
$username = 'root'; // Your database username
$password = '1234'; // Your database password

header('Content-Type: application/json');

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch all universities with their programs
    $stmt = $pdo->query("
        SELECT 
            u.id as university_id,
            u.university_name,
            u.category as university_category,
            u.city as location,
            u.logo,
            p.id as program_id,
            p.program_name,
            p.program_category,
            p.closing_merit
        FROM universities u
        LEFT JOIN programs p ON u.id = p.university_id
        WHERE u.is_active = 1
        ORDER BY u.university_name, p.program_name
    ");
    
    $universities = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $uniId = $row['university_id'];
        
        if (!isset($universities[$uniId])) {
            $universities[$uniId] = [
                'id' => $row['university_id'],
                'name' => $row['university_name'],
                'category' => $row['university_category'],
                'type' => 'Public', // Default type
                'location' => $row['location'],
                'logo' => $row['logo'],
                'programs' => []
            ];
        }
        
        if ($row['program_id']) {
            $universities[$uniId]['programs'][] = [
                'id' => $row['program_id'],
                'name' => $row['program_name'],
                'category' => $row['program_category'],
                'closing_merit' => (float) $row['closing_merit']
            ];
        }
    }
    
    // Convert to indexed array
    $universities = array_values($universities);
    
    echo json_encode([
        'success' => true,
        'universities' => $universities,
        'count' => count($universities)
    ]);
    
} catch (PDOException $e) {
    // Log the error (in production, log to file)
    error_log("Database error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch university data. Please try again later.',
        'error' => 'Database connection failed'
    ]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.',
        'error' => $e->getMessage()
    ]);
}
?>