<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['company_id'];
    
    $company_name = trim($_POST['company_name']);
    $industry = trim($_POST['industry']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $website = trim($_POST['website']);
    $portfolio_link = trim($_POST['portfolio_link']);
    $phone = trim($_POST['phone']);
    $company_size = $_POST['company_size'] ?: null;
    $specialization = trim($_POST['specialization']);
    $about = trim($_POST['about']);
    
    try {
        // Update companies table
        $stmt = $pdo->prepare("UPDATE companies SET company_name = ?, website = ?, phone = ?, company_size = ? WHERE id = ?");
        $stmt->execute([$company_name, $website, $phone, $company_size, $company_id]);
        
        // Check if company profile exists
        $checkStmt = $pdo->prepare("SELECT id FROM company_profiles WHERE company_id = ?");
        $checkStmt->execute([$company_id]);
        $profileExists = $checkStmt->fetch();
        
        if ($profileExists) {
            // Update existing profile
            $stmt = $pdo->prepare("
                UPDATE company_profiles 
                SET industry = ?, category = ?, location = ?, portfolio_link = ?, specialization = ?, about = ? 
                WHERE company_id = ?
            ");
            
            $result = $stmt->execute([$industry, $category, $location, $portfolio_link, $specialization, $about, $company_id]);
        } else {
            // Create new profile
            $stmt = $pdo->prepare("
                INSERT INTO company_profiles (company_id, industry, category, location, portfolio_link, specialization, about) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$company_id, $industry, $category, $location, $portfolio_link, $specialization, $about]);
        }
        
        if ($result) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $errorInfo = $stmt->errorInfo();
            $_SESSION['error'] = "Failed to update profile: " . $errorInfo[2];
            error_log("Database error: " . $errorInfo[2]);
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        error_log("PDO Exception: " . $e->getMessage());
    }
    
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>