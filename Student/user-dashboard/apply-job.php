<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['user_id'];
    $cover_letter = trim($_POST['cover_letter'] ?? '');

    // Debug: Check if we're receiving the form data
    error_log("Apply Job: User $user_id applying for job $job_id");

    // Check if already applied
    if (hasApplied($pdo, $user_id, $job_id)) {
        $_SESSION['error'] = "You have already applied for this job.";
        header("Location: jobs.php");
        exit();
    }

    // Validate CV file upload
    if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
        $upload_error = "Please upload your CV. ";
        
        // Provide specific error messages
        switch ($_FILES['cv_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $upload_error .= "File size is too large (max 5MB).";
                break;
            case UPLOAD_ERR_PARTIAL:
                $upload_error .= "File was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $upload_error .= "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $upload_error .= "Missing temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $upload_error .= "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $upload_error .= "File upload stopped by extension.";
                break;
            default:
                $upload_error .= "Please select a valid CV file.";
                break;
        }
        
        $_SESSION['error'] = $upload_error;
        header("Location: jobs.php");
        exit();
    }

    // CV upload configuration
    $target_dir = "assets/uploads/cvs/";
    $original_name = $_FILES['cv_file']['name'];
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $file_name = "cv_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    // Create uploads directory if it doesn't exist
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            $_SESSION['error'] = "Failed to create upload directory. Please contact administrator.";
            header("Location: jobs.php");
            exit();
        }
    }

    // Check if directory is writable
    if (!is_writable($target_dir)) {
        $_SESSION['error'] = "Upload directory is not writable. Please contact administrator.";
        header("Location: jobs.php");
        exit();
    }

    // Validate file type by extension
    $allowed_extensions = ["pdf", "doc", "docx"];
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['error'] = "Only PDF, DOC, and DOCX files are allowed. Your file type: ." . $file_extension;
        header("Location: jobs.php");
        exit();
    }

    // Validate file size (5MB max)
    if ($_FILES['cv_file']['size'] > 5000000) {
        $_SESSION['error'] = "File size must be less than 5MB. Your file: " . round($_FILES['cv_file']['size'] / 1024 / 1024, 2) . "MB";
        header("Location: jobs.php");
        exit();
    }

    // Additional security: Check file name for potential threats
    if (preg_match('/[^\w\.\-]/', $original_name)) {
        $_SESSION['error'] = "Invalid file name. Please use only letters, numbers, dots, and hyphens.";
        header("Location: jobs.php");
        exit();
    }

    // Check if file is actually uploaded (not a local file)
    if (!is_uploaded_file($_FILES['cv_file']['tmp_name'])) {
        $_SESSION['error'] = "Invalid file upload attempt.";
        header("Location: jobs.php");
        exit();
    }

    // Check if temporary file exists
    if (!file_exists($_FILES['cv_file']['tmp_name'])) {
        $_SESSION['error'] = "Temporary file not found. Please try again.";
        header("Location: jobs.php");
        exit();
    }

    // Upload file with error handling
    if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $target_file)) {
        // Debug: File uploaded successfully
        error_log("CV uploaded successfully: $target_file");
        
        // Verify file was actually saved
        if (!file_exists($target_file)) {
            $_SESSION['error'] = "File upload failed - file not found after move.";
            header("Location: jobs.php");
            exit();
        }

        // Verify file size after upload (to detect truncation)
        if (filesize($target_file) !== $_FILES['cv_file']['size']) {
            unlink($target_file); // Delete the corrupted file
            $_SESSION['error'] = "File upload corrupted during transfer. Please try again.";
            header("Location: jobs.php");
            exit();
        }

        // Save application to database
        try {
            if (applyForJob($pdo, $user_id, $job_id, $file_name, $cover_letter)) {
                $_SESSION['success'] = "Application submitted successfully! Your CV has been uploaded.";
                
                // Debug: Application saved
                error_log("Application saved to database for user $user_id, job $job_id, CV: $file_name");
            } else {
                // If database save fails, delete the uploaded file
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
                $_SESSION['error'] = "Failed to submit application to database. Please try again.";
            }
        } catch (Exception $e) {
            // If any error occurs, delete the uploaded file
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            error_log("Database error in apply-job: " . $e->getMessage());
        }
    } else {
        // Detailed upload error
        $upload_error = "Error uploading your CV. ";
        
        // Check various possible issues
        if (!is_uploaded_file($_FILES['cv_file']['tmp_name'])) {
            $upload_error .= "File was not properly uploaded.";
        } elseif (!file_exists($_FILES['cv_file']['tmp_name'])) {
            $upload_error .= "Temporary file doesn't exist.";
        } else {
            // Check for common permission issues
            $upload_error .= "Please check directory permissions or try again. ";
            $upload_error .= "Upload dir: " . (is_writable($target_dir) ? "writable" : "not writable");
        }
        
        $_SESSION['error'] = $upload_error;
        error_log("File upload failed: " . $upload_error);
    }

    header("Location: jobs.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: jobs.php");
    exit();
}
?>