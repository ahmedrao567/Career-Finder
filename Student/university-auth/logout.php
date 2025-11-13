<?php
session_start();

// Unset all university session variables
unset($_SESSION['university_id']);
unset($_SESSION['university_name']);
unset($_SESSION['university_email']);
unset($_SESSION['university_logged_in']);
unset($_SESSION['otp_sent']);

// Destroy the session
session_destroy();

// Redirect to university login page
header("Location: index.php");
exit();
?>