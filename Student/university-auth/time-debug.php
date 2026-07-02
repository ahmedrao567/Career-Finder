<?php
// Set timezone
date_default_timezone_set('Asia/Karachi');

echo "<h2>Time Debug Information</h2>";

echo "<h3>PHP Server Time:</h3>";
echo "Date/Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Timezone: " . date_default_timezone_get() . "<br>";
echo "Timestamp: " . time() . "<br>";

echo "<h3>Database Time:</h3>";

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'career_finder';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set timezone for this connection
    $pdo->exec("SET time_zone = '+05:00'");
    
    $stmt = $pdo->query("SELECT NOW() as db_time, UTC_TIMESTAMP() as utc_time, @@system_time_zone as system_tz, @@time_zone as db_tz");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Database Time: " . $result['db_time'] . "<br>";
    echo "UTC Time: " . $result['utc_time'] . "<br>";
    echo "System Timezone: " . $result['system_tz'] . "<br>";
    echo "Database Timezone: " . $result['db_tz'] . "<br>";
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>Time Synchronization Check:</h3>";

$php_time = strtotime(date('Y-m-d H:i:s'));
$db_time = strtotime($result['db_time']);
$time_diff = $php_time - $db_time;

echo "PHP Time: " . date('Y-m-d H:i:s') . " ($php_time)<br>";
echo "DB Time: " . $result['db_time'] . " ($db_time)<br>";
echo "Time Difference: " . $time_diff . " seconds (" . ($time_diff/60) . " minutes)<br>";

if (abs($time_diff) > 300) {
    echo "<span style='color: red;'>⚠️ WARNING: Time difference is more than 5 minutes! This will cause OTP issues.</span><br>";
} else {
    echo "<span style='color: green;'>✅ Time synchronization is good.</span><br>";
}

echo "<h3>Quick Fixes:</h3>";
echo "1. Sync your server time with internet time<br>";
echo "2. On Windows: Right-click clock → Adjust date/time → Sync now<br>";
echo "3. On XAMPP: Check if MySQL and Apache times match<br>";
?>