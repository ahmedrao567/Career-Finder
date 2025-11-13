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
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Set base URL for proper redirection
define('BASE_URL', 'http://localhost/career-finder');
?>