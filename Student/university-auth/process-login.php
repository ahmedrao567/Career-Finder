<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = implode('', $_POST['otp']);
    $email = $_SESSION['university_email'];
    
    error_log("OTP Verification Attempt:");
    error_log("Email: " . $email);
    error_log("Received OTP: " . $otp);
    error_log("Session Email: " . $_SESSION['university_email']);
    
    // Check what's in the database
    $stmt = $pdo->prepare("SELECT otp_code FROM universities WHERE email = ?");
    $stmt->execute([$email]);
    $db_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("Database OTP: " . $db_data['otp_code']);
    
    // Verify OTP (No expiry check)
    $stmt = $pdo->prepare("SELECT * FROM universities WHERE email = ? AND otp_code = ? AND is_active = 1");
    $stmt->execute([$email, $otp]);
    $university = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($university) {
        error_log("OTP Verification SUCCESS");
        
        // Clear OTP after successful verification
        $stmt = $pdo->prepare("UPDATE universities SET otp_code = NULL WHERE email = ?");
        $stmt->execute([$email]);
        
        // Set session variables
        $_SESSION['university_id'] = $university['id'];
        $_SESSION['university_name'] = $university['university_name'];
        $_SESSION['university_email'] = $university['email'];
        $_SESSION['university_logged_in'] = true;
        
        // Clear OTP session data
        unset($_SESSION['otp_sent']);
        
        // Redirect to dashboard
        header('Location: ../university-dashboard/index.php');
        exit();
    } else {
        error_log("OTP Verification FAILED");
        
        // More detailed error checking
        $stmt = $pdo->prepare("SELECT * FROM universities WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $uni_check = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$uni_check) {
            error_log("University not found or inactive");
            $_SESSION['error'] = "University account not found or inactive.";
        } else {
            $stmt = $pdo->prepare("SELECT otp_code FROM universities WHERE email = ?");
            $stmt->execute([$email]);
            $stored_otp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stored_otp || empty($stored_otp['otp_code'])) {
                error_log("No OTP found for this email");
                $_SESSION['error'] = "No OTP found. Please request a new OTP.";
            } else {
                error_log("OTP code doesn't match. Stored: " . $stored_otp['otp_code'] . ", Received: " . $otp);
                $_SESSION['error'] = "Invalid OTP code. Please check and try again.";
            }
        }
        
        header('Location: verify-otp.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>