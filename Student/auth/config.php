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
define('BASE_URL', 'http://localhost:8000');

// Email Configuration (Update with your SMTP details)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'muneeb.amed0@gmail.com');
define('SMTP_PASSWORD', 'cbfgzztxvhdtinxr');
define('SMTP_FROM', 'muneeb.amed0@gmail.com');
define('SMTP_FROM_NAME', 'CareerFinder');

// Generate OTP Function
function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// Send OTP Email Function
function sendOTPEmail($toEmail, $toName, $otpCode) {
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Verification Code - CareerFinder';
        
        $emailTemplate = getOTPEmailTemplate($toName, $otpCode);
        $mail->Body    = $emailTemplate;
        $mail->AltBody = "Your OTP code is: $otpCode\n\nThis code will not expire. Please use it to verify your account.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function getOTPEmailTemplate($name, $otpCode) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; margin: -30px -30px 30px -30px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #333; text-align: center; margin: 20px 0; letter-spacing: 5px; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🔐 OTP Verification</h1>
            </div>
            <h2>Hello, ' . htmlspecialchars($name) . '!</h2>
            <p>Thank you for registering with CareerFinder. Please use the following OTP to verify your email address:</p>
            <div class="otp-code">' . $otpCode . '</div>
            <p>This OTP will not expire. Please keep it secure and do not share it with anyone.</p>
            <div class="footer">
                <p>CareerFinder Team<br>This is an automated message.</p>
            </div>
        </div>
    </body>
    </html>
    ';
}
?>