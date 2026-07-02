<?php
session_start();

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'career_finder';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if company is logged in
if (!isset($_SESSION['company_id']) || !$_SESSION['company_logged_in']) {
    header("Location: ../company-auth/index.php");
    exit();
}

// Get company profile data
function getCompanyProfile($pdo, $company_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, cp.* 
        FROM companies c 
        LEFT JOIN company_profiles cp ON c.id = cp.company_id 
        WHERE c.id = ?
    ");
    $stmt->execute([$company_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get company projects
function getCompanyProjects($pdo, $company_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM company_projects 
        WHERE company_id = ? 
        ORDER BY project_date DESC, created_at DESC
    ");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get project by ID
function getProjectById($pdo, $project_id, $company_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM company_projects 
        WHERE id = ? AND company_id = ?
    ");
    $stmt->execute([$project_id, $company_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// function getCompanyJobs($pdo, $company_id) {
//     $stmt = $pdo->prepare("
//         SELECT j.*, 
//                COUNT(ja.id) as application_count,
//                SUM(CASE WHEN ja.status = 'pending' THEN 1 ELSE 0 END) as pending_applications
//         FROM jobs j 
//         LEFT JOIN job_applications ja ON j.id = ja.job_id 
//         WHERE j.company_id = ? 
//         GROUP BY j.id 
//         ORDER BY j.created_at DESC
//     ");
//     $stmt->execute([$company_id]);
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }


// Get job by ID
function getJobById($pdo, $job_id, $company_id) {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND company_id = ?");
    $stmt->execute([$job_id, $company_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get job applications
// function getJobApplications($pdo, $job_id, $company_id) {
//     $stmt = $pdo->prepare("
//         SELECT ja.*, u.full_name, u.email, u.username, up.designation, up.location as user_location
//         FROM job_applications ja
//         JOIN users u ON ja.user_id = u.id
//         LEFT JOIN user_profiles up ON u.id = up.user_id
//         JOIN jobs j ON ja.job_id = j.id
//         WHERE ja.job_id = ? AND j.company_id = ?
//         ORDER BY ja.applied_at DESC
//     ");
//     $stmt->execute([$job_id, $company_id]);
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

// Get all applications for company
function getAllCompanyApplications($pdo, $company_id) {
    $stmt = $pdo->prepare("
        SELECT ja.*, j.title as job_title, u.full_name, u.email, u.username, 
               up.designation, up.location as user_location, j.company_id
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.id
        JOIN users u ON ja.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE j.company_id = ?
        ORDER BY ja.applied_at DESC
    ");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update application status
function updateApplicationStatus($pdo, $application_id, $status, $company_id) {
    $stmt = $pdo->prepare("
        UPDATE job_applications ja
        JOIN jobs j ON ja.job_id = j.id
        SET ja.status = ?
        WHERE ja.id = ? AND j.company_id = ?
    ");
    return $stmt->execute([$status, $application_id, $company_id]);
}
?>