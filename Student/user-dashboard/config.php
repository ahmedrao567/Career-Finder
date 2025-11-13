<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'career_finder';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    header("Location: ../auth/index.php");
    exit();
}

// Set base path for file uploads and includes
define('BASE_PATH', dirname(__FILE__));

// Get user profile data
function getUserProfile($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserExperiences($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM user_experiences WHERE user_id = ? ORDER BY start_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserSkills($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get posts with pagination - FIXED
function getPosts($pdo, $limit = 10, $offset = 0)
{
    // Convert to integers to ensure proper binding
    $limit = (int)$limit;
    $offset = (int)$offset;

    $stmt = $pdo->prepare("
        SELECT p.*, 
               COUNT(DISTINCT sp.id) as save_count,
               EXISTS(SELECT 1 FROM saved_posts sp2 WHERE sp2.user_id = ? AND sp2.post_id = p.id) as is_saved
        FROM posts p
        LEFT JOIN saved_posts sp ON p.id = sp.post_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");

    // Bind parameters with explicit types
    $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get saved posts
function getSavedPosts($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT p.*, sp.saved_at
        FROM posts p
        INNER JOIN saved_posts sp ON p.id = sp.post_id
        WHERE sp.user_id = ?
        ORDER BY sp.saved_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Toggle save post
function toggleSavePost($pdo, $user_id, $post_id)
{
    // Check if already saved
    $stmt = $pdo->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);

    if ($stmt->fetch()) {
        // Unsave
        $stmt = $pdo->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?");
        return $stmt->execute([$user_id, $post_id]);
    } else {
        // Save
        $stmt = $pdo->prepare("INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $post_id]);
    }
}

// Get current year for graduation year dropdown
function getGraduationYears()
{
    $currentYear = date('Y');
    $years = [];
    for ($i = $currentYear; $i <= $currentYear + 10; $i++) {
        $years[] = $i;
    }
    return $years;
}

// Get all active jobs with company info
// Alternative getJobs function - more compatible
function getJobs($pdo, $filters = [], $limit = 10, $offset = 0) {
    // Convert to integers
    $limit = (int)$limit;
    $offset = (int)$offset;
    $user_id = $_SESSION['user_id'];
    
    $whereConditions = ["j.is_active = 1"];
    $params = [];
    
    if (!empty($filters['type'])) {
        $whereConditions[] = "j.type = ?";
        $params[] = $filters['type'];
    }
    
    if (!empty($filters['location'])) {
        $whereConditions[] = "j.location LIKE ?";
        $params[] = "%" . $filters['location'] . "%";
    }
    
    if (!empty($filters['search'])) {
        $whereConditions[] = "(j.title LIKE ? OR j.description LIKE ? OR c.company_name LIKE ?)";
        $params[] = "%" . $filters['search'] . "%";
        $params[] = "%" . $filters['search'] . "%";
        $params[] = "%" . $filters['search'] . "%";
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    // Use string concatenation for LIMIT (less ideal but works)
    $sql = "
        SELECT j.*, 
               c.company_name,
               c.logo as company_logo,
               COUNT(DISTINCT ja.id) as application_count,
               EXISTS(SELECT 1 FROM saved_jobs sj WHERE sj.user_id = $user_id AND sj.job_id = j.id) as is_saved,
               EXISTS(SELECT 1 FROM job_applications ja2 WHERE ja2.user_id = $user_id AND ja2.job_id = j.id) as has_applied
        FROM jobs j
        LEFT JOIN companies c ON j.company_id = c.id
        LEFT JOIN job_applications ja ON j.id = ja.job_id
        WHERE $whereClause
        GROUP BY j.id
        ORDER BY j.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind only the filter parameters
    foreach ($params as $index => $param) {
        $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get saved jobs for user
function getSavedJobs($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT j.*, c.company_name, c.logo as company_logo, sj.saved_at
        FROM jobs j
        INNER JOIN saved_jobs sj ON j.id = sj.job_id
        LEFT JOIN companies c ON j.company_id = c.id
        WHERE sj.user_id = ? AND j.is_active = 1
        ORDER BY sj.saved_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Toggle save job
function toggleSaveJob($pdo, $user_id, $job_id) {
    // Check if already saved
    $stmt = $pdo->prepare("SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$user_id, $job_id]);

    if ($stmt->fetch()) {
        // Unsave
        $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?");
        return $stmt->execute([$user_id, $job_id]);
    } else {
        // Save
        $stmt = $pdo->prepare("INSERT INTO saved_jobs (user_id, job_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $job_id]);
    }
}

// Apply for job - FIXED VERSION
function applyForJob($pdo, $user_id, $job_id, $cv_file, $cover_letter = '') {
    $user_id = (int)$user_id;
    $job_id = (int)$job_id;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO job_applications (job_id, user_id, cv_file, cover_letter) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bindValue(1, $job_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $cv_file, PDO::PARAM_STR);
        $stmt->bindValue(4, $cover_letter, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("PDO Error in applyForJob: " . $e->getMessage());
        return false;
    }
}

// Check if user has applied for job - FIXED
function hasApplied($pdo, $user_id, $job_id) {
    $user_id = (int)$user_id;
    $job_id = (int)$job_id;
    
    $stmt = $pdo->prepare("SELECT id FROM job_applications WHERE user_id = ? AND job_id = ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $job_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch() ? true : false;
}
