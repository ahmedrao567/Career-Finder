<?php
include 'config.php';

// Handle resend OTP via GET
if (isset($_GET['resend']) && isset($_GET['email'])) {
    $email = $_GET['email'];
    $_SESSION['university_email'] = $email;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
} else {
    header('Location: index.php');
    exit();
}

// Check if university exists
$stmt = $pdo->prepare("SELECT * FROM universities WHERE email = ? AND is_active = 1");
$stmt->execute([$email]);
$university = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$university) {
    $_SESSION['error'] = "No university found with this email or account is inactive.";
    header('Location: index.php');
    exit();
}

// Generate and send OTP (No expiry)
$otp = generateOTP();

error_log("Generating OTP for: " . $email);
error_log("Generated OTP: " . $otp);

// Update OTP in database
$stmt = $pdo->prepare("UPDATE universities SET otp_code = ? WHERE email = ?");
$update_result = $stmt->execute([$otp, $email]);

error_log("Database update result: " . ($update_result ? "Success" : "Failed"));

// Verify the OTP was saved
$stmt = $pdo->prepare("SELECT otp_code FROM universities WHERE email = ?");
$stmt->execute([$email]);
$verify_otp = $stmt->fetch(PDO::FETCH_ASSOC);

error_log("Verified OTP in DB: " . $verify_otp['otp_code']);

// Send OTP via email
if (sendOTP($email, $otp)) {
    $_SESSION['university_email'] = $email;
    $_SESSION['otp_sent'] = true;
    $_SESSION['success'] = "OTP sent successfully to your email!";
    header('Location: verify-otp.php');
    exit();
} else {
    $_SESSION['error'] = "Failed to send OTP. Please try again.";
    header('Location: index.php');
    exit();
}
?>