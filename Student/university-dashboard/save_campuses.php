<?php
// save_campuses.php
require_once 'config.php'; // Include your database configuration

header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

// Debug: Log the received data (remove in production)
error_log('Received campuses data: ' . print_r($input, true));

// Validate session and university ID
if (!isset($_SESSION['university_id']) || empty($_SESSION['university_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated. Please log in.']);
    exit;
}

$university_id = $_SESSION['university_id'];

// Validate input
if (!isset($input['campuses']) || !is_array($input['campuses'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid campuses data']);
    exit;
}

// Sanitize campuses data
$sanitizedCampuses = [];
foreach ($input['campuses'] as $index => $campus) {
    if (!isset($campus['name']) || empty(trim($campus['name']))) {
        echo json_encode(['success' => false, 'message' => "Campus at index $index must have a name"]);
        exit;
    }
    
    $sanitizedCampuses[] = [
        'name' => $conn->real_escape_string(htmlspecialchars(trim($campus['name']), ENT_QUOTES, 'UTF-8')),
        'address' => isset($campus['address']) ? $conn->real_escape_string(htmlspecialchars(trim($campus['address']), ENT_QUOTES, 'UTF-8')) : '',
        'phone' => isset($campus['phone']) ? $conn->real_escape_string(htmlspecialchars(trim($campus['phone']), ENT_QUOTES, 'UTF-8')) : ''
    ];
}

// Convert to JSON for storage
$campuses_json = json_encode($sanitizedCampuses);

// Prepare the SQL statement
$sql = "UPDATE universities SET campuses = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
    exit;
}

$stmt->bind_param("si", $campuses_json, $university_id);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        $response = [
            'success' => true,
            'message' => 'Campuses saved successfully!',
            'campuses' => $sanitizedCampuses,
            'count' => count($sanitizedCampuses)
        ];
        
        // Update session variable if needed
        $_SESSION['campuses'] = $sanitizedCampuses;
    } else {
        // No rows affected - might be same data or university not found
        $response = [
            'success' => true,
            'message' => 'Campuses data is up to date',
            'campuses' => $sanitizedCampuses,
            'count' => count($sanitizedCampuses),
            'note' => 'No changes were made to the database'
        ];
    }
    
    echo json_encode($response);
} else {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to save campuses: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>