<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['verify_email'])) {
        $_SESSION['error'] = "Session expired. Please sign up again.";
        header('Location: signup.php');
        exit();
    }

    $email = $_SESSION['verify_email'];
    
    // Combine OTP digits
    $entered_otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . 
                   $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];

    // Verify OTP
    $stmt = $pdo->prepare("SELECT id, otp_code FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp_code'] === $entered_otp) {
        // Mark as verified
        $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, otp_code = NULL WHERE email = ?");
        if ($stmt->execute([$email])) {
            unset($_SESSION['verify_email']);
            $_SESSION['success'] = "Email verified successfully! You can now login.";
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = "Verification failed. Please try again.";
            header('Location: verify-otp.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid verification code. Please try again.";
        header('Location: verify-otp.php');
        exit();
    }
} else {
    header('Location: verify-otp.php');
    exit();
}
?>