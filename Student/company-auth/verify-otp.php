<?php
include 'config.php';
include 'email-config.php';

// Redirect if no signup data exists
if (!isset($_SESSION['company_signup_data']) || empty($_SESSION['company_signup_data'])) {
    header("Location: signup.php");
    exit();
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_otp'])) {
        $entered_otp = trim($_POST['otp']);
        $stored_otp = $_SESSION['company_signup_otp'];

        if ($entered_otp === $stored_otp) {
            // OTP verified, create company account
            $signup_data = $_SESSION['company_signup_data'];
            
            try {
                $stmt = $pdo->prepare("INSERT INTO companies (company_name, email, password, website, phone, founded_year, company_size) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([
                    $signup_data['company_name'],
                    $signup_data['email'], 
                    $signup_data['password'],
                    $signup_data['website'],
                    $signup_data['phone'],
                    $signup_data['founded_year'],
                    $signup_data['company_size']
                ])) {
                    $company_id = $pdo->lastInsertId();
                    
                    // Create company profile
                    $stmt = $pdo->prepare("INSERT INTO company_profiles (company_id) VALUES (?)");
                    $stmt->execute([$company_id]);
                    
                    // Clear session data
                    unset($_SESSION['company_signup_data']);
                    unset($_SESSION['company_signup_otp']);
                    
                    $_SESSION['success'] = "Company account created successfully! You can now login.";
                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Failed to create company account. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Invalid OTP. Please try again.";
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Resend OTP
        $emailSender = new CompanyEmailSender();
        if ($emailSender->sendOTP($_SESSION['company_signup_data']['email'], $_SESSION['company_signup_data']['company_name'], $_SESSION['company_signup_otp'])) {
            $_SESSION['success'] = "OTP resent successfully!";
        } else {
            $_SESSION['error'] = "Failed to resend OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <!-- Logo -->
    <div class="absolute top-6 left-6">
        <div class="text-2xl text-green-600 flex items-center font-bold">
            <i class="fas fa-building mr-2"></i>
            CareerFinder
        </div>
    </div>

    <!-- OTP Verification Form -->
    <div class="glass-effect rounded-2xl shadow-xl w-full max-w-md p-8 bg-white">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm0-10V7a4 4 0 018 0v4"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Verify Your Company Email</h2>
            <p class="text-gray-600 mb-2">We sent a 6-digit OTP to</p>
            <p class="text-green-600 font-medium mb-6"><?php echo $_SESSION['company_signup_data']['email']; ?></p>
        </div>

        <!-- Display messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="otp" class="block text-gray-700 text-sm font-medium mb-2">Enter OTP Code</label>
                <input type="text" id="otp" name="otp" maxlength="6" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-center text-xl font-mono tracking-widest"
                    placeholder="000000">
            </div>

            <div class="flex space-x-4">
                <button type="submit" name="verify_otp"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-green-500">
                    Verify OTP
                </button>
                <button type="submit" name="resend_otp"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-gray-500">
                    Resend OTP
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-500 text-sm">Didn't receive the code? Check your spam folder or try resending.</p>
        </div>
    </div>

    <script>
        document.getElementById('otp').focus();
        
        // document.getElementById('otp').addEventListener('input', function(e) {
        //     if (this.value.length === 6) {
        //         this.form.submit();
        //     }
        // });
    </script>
</body>
</html>