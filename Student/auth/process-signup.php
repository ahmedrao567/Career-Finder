<?php
include 'config.php';
include 'phpmailer-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header('Location: signup.php');
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username already exists. Please choose a different username.";
        header('Location: signup.php');
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));
    
    // Insert user into database with OTP
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, otp_code) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$full_name, $username, $email, $hashed_password, $otp])) {
        // Send OTP email
        if (sendOTP($email, $otp, $full_name, 'user')) {
            $_SESSION['verify_email'] = $email;
            $_SESSION['success'] = "Account created successfully! Please check your email for verification code.";
            header('Location: verify-otp.php');
            exit();
        } else {
            $_SESSION['error'] = "Account created but failed to send verification email. Please contact support.";
            header('Location: signup.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header('Location: signup.php');
        exit();
    }
} else {
    header('Location: signup.php');
    exit();
}
?>