<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

// Redirect to user dashboard
header("Location: ../user-dashboard/index.php");
exit();
?>