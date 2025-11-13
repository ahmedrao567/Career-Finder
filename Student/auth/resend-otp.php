<?php
include 'config.php';
include 'phpmailer-config.php';

if (!isset($_SESSION['verify_email'])) {
    header('Location: signup.php');
    exit();
}

$email = $_SESSION['verify_email'];

// Generate new OTP
$otp = sprintf("%06d", mt_rand(1, 999999));

// Update OTP in database
$stmt = $pdo->prepare("UPDATE users SET otp_code = ? WHERE email = ?");
if ($stmt->execute([$otp, $email])) {
    // Get user name for email
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (sendOTP($email, $otp, $user['full_name'], 'user')) {
        $_SESSION['success'] = "Verification code has been resent to your email.";
    } else {
        $_SESSION['error'] = "Failed to resend verification email. Please try again.";
    }
} else {
    $_SESSION['error'] = "Failed to generate new verification code.";
}

header('Location: verify-otp.php');
exit();
?>