<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Check if company exists and is verified
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE email = ? AND is_verified = TRUE");
    $stmt->execute([$email]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company && password_verify($password, $company['password'])) {
        // Set session variables
        $_SESSION['company_id'] = $company['id'];
        $_SESSION['company_name'] = $company['company_name'];
        $_SESSION['company_email'] = $company['email'];
        $_SESSION['company_logged_in'] = true;

        // Redirect to company dashboard
        header('Location: ../company-dashboard/index.php');
        exit();
    } else {
        // Check if company exists but not verified
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE email = ? AND is_verified = false");
        $stmt->execute([$email]);
        $unverified_company = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($unverified_company && password_verify($password, $unverified_company['password'])) {
            $_SESSION['company_email'] = $email;
            $_SESSION['error'] = "Please verify your company email address first.";
            header('Location: ../company-dashboard/profile.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: index.php');
            exit();
        }
    }
} else {
    header('Location: index.php');
    exit();
}
?>