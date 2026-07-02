<?php
// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'career_finder';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Remove otp_expiry column if it exists
    try {
        $pdo->exec("ALTER TABLE universities DROP COLUMN otp_expiry");
        echo "✅ Removed otp_expiry column from universities table<br>";
    } catch (PDOException $e) {
        echo "ℹ️ otp_expiry column doesn't exist or already removed<br>";
    }
    
    // Verify table structure
    $stmt = $pdo->query("DESCRIBE universities");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "✅ Current universities table columns: " . implode(', ', $columns) . "<br>";
    
    if (in_array('otp_code', $columns) && !in_array('otp_expiry', $columns)) {
        echo "✅ Database structure is correct! OTP will not expire.<br>";
    } else {
        echo "❌ Database structure needs adjustment.<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br>🎯 Database update completed!";
?>