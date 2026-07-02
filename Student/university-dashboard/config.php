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
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if university is logged in
if (!isset($_SESSION['university_id']) || !$_SESSION['university_logged_in']) {
    header('Location: ../university-auth/index.php');
    exit();
}

function sanitize_input($data) {
    global $conn;
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get university profile data
function getUniversityProfile($pdo, $university_id)
{
    $stmt = $pdo->prepare("SELECT * FROM universities WHERE id = ?");
    $stmt->execute([$university_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Common categories for universities
function getUniversityCategories()
{
    return [
        'Engineering & Technology',
        'Medical & Health Sciences',
        'Business & Management',
        'Arts & Humanities',
        'Social Sciences',
        'Natural Sciences',
        'Computer Science & IT',
        'Law & Legal Studies',
        'Education',
        'Agriculture',
        'Architecture',
        'Other'
    ];
}

// Common provinces
function getProvinces()
{
    return [
        'Punjab',
        'Sindh',
        'Khyber Pakhtunkhwa',
        'Balochistan',
        'Gilgit-Baltistan',
        'Azad Jammu & Kashmir'
    ];
}

// Add these functions to your existing config.php

// Get posts with pagination for university feed
// Get posts with pagination for university feed - FIXED VERSION
// Updated getUniversityFeedPosts function
function getUniversityFeedPosts($pdo, $limit = 10, $offset = 0)
{
    $university_id = $_SESSION['university_id'];

    $sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add save count and is_saved status for each post
    foreach ($posts as &$post) {
        // Get save count
        $stmt_count = $pdo->prepare("SELECT COUNT(*) as save_count FROM saved_posts WHERE post_id = ?");
        $stmt_count->execute([$post['id']]);
        $save_data = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $post['save_count'] = $save_data['save_count'] ?? 0;

        // Check if current university saved this post
        $stmt_saved = $pdo->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
        $stmt_saved->execute([$university_id, $post['id']]);
        $post['is_saved'] = $stmt_saved->fetch() ? true : false;
    }

    return $posts;
}


// Toggle save post for university

function toggleSavePostUniversity($pdo, $university_id, $post_id)
{
    // Check if already saved
    $stmt = $pdo->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$university_id, $post_id]);

    if ($stmt->fetch()) {
        // Unsave
        $stmt = $pdo->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?");
        return $stmt->execute([$university_id, $post_id]);
    } else {
        // Save
        $stmt = $pdo->prepare("INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)");
        return $stmt->execute([$university_id, $post_id]);
    }
}

// Get saved posts for university
function getSavedPostsUniversity($pdo, $university_id)
{
    $stmt = $pdo->prepare("
        SELECT p.*, sp.saved_at,
               CASE 
                   WHEN p.poster_type = 'company' THEN c.company_name
                   WHEN p.poster_type = 'university' THEN u.university_name
               END as poster_display_name,
               CASE 
                   WHEN p.poster_type = 'company' THEN cp.logo
                   WHEN p.poster_type = 'university' THEN u.logo
               END as poster_logo
        FROM posts p
        INNER JOIN saved_posts sp ON p.id = sp.post_id
        LEFT JOIN companies c ON p.poster_type = 'company' AND p.poster_id = c.id
        LEFT JOIN universities u ON p.poster_type = 'university' AND p.poster_id = u.id
        LEFT JOIN company_profiles cp ON c.id = cp.company_id
        WHERE sp.user_id = ?
        ORDER BY sp.saved_at DESC
    ");
    $stmt->execute([$university_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Create new post for university
// Create new post for university - NULL version
function createUniversityPost($pdo, $university_id, $post_text, $post_image = null) {
    $university_name = $_SESSION['university_name'];
    
    // Get university logo for avatar
    $logo = null;
    $stmt_logo = $pdo->prepare("SELECT logo FROM universities WHERE id = ?");
    $stmt_logo->execute([$university_id]);
    $uni_data = $stmt_logo->fetch(PDO::FETCH_ASSOC);
    if ($uni_data && $uni_data['logo']) {
        $logo = $uni_data['logo'];
    }
    
    // Try with NULL for company_id
    $stmt = $pdo->prepare("
        INSERT INTO posts (poster_id, company_id, poster_type, poster_name, poster_avatar, post_text, post_image) 
        VALUES (?, NULL, 'university', ?, ?, ?, ?)
    ");
    
    return $stmt->execute([$university_id, $university_name, $logo, $post_text, $post_image]);
}


// Add this function to your existing config.php
function getUniversityPrograms($pdo, $university_id) {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE university_id = :university_id ORDER BY program_name");
    $stmt->bindParam(':university_id', $university_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProgramCategories() {
    return [
        'Engineering & Technology',
        'Medical & Health Sciences',
        'Computer Science & IT',
        'Business & Management',
        'Social Sciences',
        'Natural Sciences',
        'Arts & Humanities',
        'Law & Legal Studies',
        'Education',
        'Agriculture',
        'Architecture & Planning',
        'Pharmacy',
        'Media & Communication',
        'Environmental Sciences',
        'Mathematics & Statistics'
    ];
}
