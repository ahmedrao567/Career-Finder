<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Check if user exists and is verified
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (!$user['is_verified']) {
            $_SESSION['error'] = "Please verify your email before logging in.";
            $_SESSION['verify_email'] = $email;
            header('Location: verify-otp.php');
            exit();
        }
        
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;

            // Redirect to dashboard
            header('Location: ../user-dashboard/index.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: index.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>