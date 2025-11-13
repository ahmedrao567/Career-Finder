<?php
session_start();

// Unset all company session variables
unset($_SESSION['company_id']);
unset($_SESSION['company_name']);
unset($_SESSION['company_email']);
unset($_SESSION['company_logged_in']);

// Destroy the session
session_destroy();

// Redirect to company login page
header("Location: index.php");
exit();
?>