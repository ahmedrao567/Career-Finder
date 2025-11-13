<?php
session_start();

// Check what type of user is logged in and redirect accordingly
if (isset($_SESSION['user_id']) && $_SESSION['logged_in']) {
    header('Location: user-dashboard/index.php');
    exit();
} elseif (isset($_SESSION['company_id']) && $_SESSION['company_logged_in']) {
    header('Location: company-dashboard/index.php');
    exit();
} elseif (isset($_SESSION['university_id']) && $_SESSION['university_logged_in']) {
    header('Location: university-dashboard/index.php');
    exit();
} else {
    header('Location: choice.php');
    exit();
}
?>