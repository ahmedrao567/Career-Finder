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

// PHPMailer configuration (update with your SMTP details)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'muneeb.amed0@gmail.com');
define('SMTP_PASSWORD', 'cbfgzztxvhdtinxr');
define('SMTP_FROM', 'muneeb.amed0@gmail.com');
define('SMTP_FROM_NAME', 'CareerFinder');

// Set base URL
define('BASE_URL', 'http://localhost/career-finder');

// Generate OTP
function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
// Send OTP via Email
function sendOTP($email, $otp) {
    
    
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = 'tls';
    
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress($email);
    $mail->isHTML(true);
    
    $mail->Subject = 'Your OTP for University Login - CareerFinder';
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #4F46E5; text-align: center;'>CareerFinder University Portal</h2>
            <div style='background: #F3F4F6; padding: 20px; border-radius: 10px;'>
                <h3 style='color: #1F2937;'>Your One-Time Password (OTP)</h3>
                <p style='color: #6B7280;'>Use the following OTP to login to your university account:</p>
                <div style='background: white; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                    <span style='font-size: 32px; font-weight: bold; color: #4F46E5; letter-spacing: 5px;'>$otp</span>
                </div>
                <p style='color: #6B7280; font-size: 14px;'>
                    This OTP will remain valid until you request a new one.
                </p>
            </div>
        </div>
    ";
    
    $mail->AltBody = "Your OTP for CareerFinder University Login is: $otp. This OTP will remain valid until you request a new one.";
    
    return $mail->send();
}
?>