<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $website = trim($_POST['website']);
    $phone = trim($_POST['phone']);
    $founded_year = $_POST['founded_year'] ?: null;
    $company_size = $_POST['company_size'] ?: null;

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM companies WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header('Location: signup.php');
        exit();
    }

    // Check if company name already exists
    $stmt = $pdo->prepare('SELECT id FROM companies WHERE company_name = ?');
    $stmt->execute([$company_name]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Company name already exists. Please choose a different name.";
        header('Location: signup.php');
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));

    // Insert company into database with OTP
    $stmt = $pdo->prepare('INSERT INTO companies (company_name, email, password, website, phone, founded_year, company_size, otp_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

    if ($stmt->execute([$company_name, $email, $hashed_password, $website, $phone, $founded_year, $company_size, $otp])) {
        $company_id = $pdo->lastInsertId();

        // Create company profile
        $stmt = $pdo->prepare('INSERT INTO company_profiles (company_id) VALUES (?)');
        $stmt->execute([$company_id]);

        // Store email in session for OTP verification
        $_SESSION['company_email'] = $email;
        $_SESSION['success'] = "Company account created successfully! Please verify your email with the OTP sent to your inbox.";
        
        // In a real application, you would send the OTP via email here
        error_log("OTP for company $email: $otp"); // Remove this in production
        
        // Redirect to OTP verification
        header('Location: otp-verification.php');
        exit();
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