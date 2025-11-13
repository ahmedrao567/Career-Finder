<?php
session_start();
include 'config.php';

// Redirect if no email in session or already verified
if (!isset($_SESSION['company_email']) || (isset($_SESSION['company_id']) && $_SESSION['company_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = implode('', $_POST['otp']);
    $email = $_SESSION['company_email'];
    
    error_log("Company OTP Verification Attempt - Email: $email, OTP: $otp");
    
    // Verify OTP (no expiry check)
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE email = ? AND otp_code = ?");
    $stmt->execute([$email, $otp]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
        error_log("Company OTP Verification SUCCESS");
        
        // Clear OTP after successful verification
        $stmt = $pdo->prepare("UPDATE companies SET otp_code = NULL, is_verified = TRUE WHERE email = ?");
        $stmt->execute([$email]);
        
        // Set session variables
        $_SESSION['company_id'] = $company['id'];
        $_SESSION['company_name'] = $company['company_name'];
        $_SESSION['company_email'] = $company['email'];
        $_SESSION['company_logged_in'] = true;
        
        // Clear OTP session data
        unset($_SESSION['otp_sent']);
        
        // Redirect to dashboard
        header('Location: ../company-dashboard/index.php');
        exit();
    } else {
        error_log("Company OTP Verification FAILED");
        
        // More detailed error checking
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE email = ?");
        $stmt->execute([$email]);
        $company_check = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$company_check) {
            error_log("Company not found");
            $error = "Company account not found.";
        } else {
            $stmt = $pdo->prepare("SELECT otp_code FROM companies WHERE email = ?");
            $stmt->execute([$email]);
            $stored_otp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stored_otp || empty($stored_otp['otp_code'])) {
                error_log("No OTP found for this email");
                $error = "No OTP found. Please request a new OTP.";
            } else {
                error_log("OTP code doesn't match. Stored: " . $stored_otp['otp_code'] . ", Received: " . $otp);
                $error = "Invalid OTP code. Please check and try again.";
            }
        }
    }
}

// Handle resend OTP
if (isset($_POST['resend_otp'])) {
    $email = $_SESSION['company_email'];
    $otp = generateOTP();
    
    // Store OTP in database
    $stmt = $pdo->prepare("UPDATE companies SET otp_code = ? WHERE email = ?");
    if ($stmt->execute([$otp, $email])) {
        $success = "New OTP sent successfully!";
        error_log("New OTP generated for company: $otp");
    } else {
        $error = "Failed to generate new OTP. Please try again.";
    }
}

function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            margin: 0 5px;
        }
        .otp-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
            outline: none;
        }
        .otp-input.filled {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
    </style>
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
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-green-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">OTP Verification</h2>
            <p class="text-gray-600">Enter the 6-digit code sent to your company email</p>
            <p class="text-sm text-green-600 font-medium mt-2"><?php echo $_SESSION['company_email']; ?></p>
        </div>

        <!-- Display Messages -->
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form id="otpForm" method="POST" class="space-y-6">
            <!-- OTP Inputs -->
            <div class="flex justify-center space-x-3 mb-6">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" 
                           name="otp[]" 
                           maxlength="1" 
                           class="otp-input" 
                           data-index="<?php echo $i; ?>"
                           oninput="moveToNext(this, <?php echo $i; ?>)" 
                           onkeydown="handleBackspace(this, <?php echo $i; ?>, event)"
                           onpaste="handlePaste(event)"
                           required>
                <?php endfor; ?>
            </div>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-green-500">
                Verify OTP
            </button>
        </form>

        <!-- Resend OTP -->
        <form method="POST" class="mt-6 text-center">
            <p class="text-gray-600 mb-4">Didn't receive the code?</p>
            <button type="submit" name="resend_otp"
                    class="text-green-600 hover:text-green-800 font-medium transition">
                <i class="fas fa-redo mr-2"></i>Resend OTP
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-gray-500 text-sm">
                <i class="fas fa-info-circle mr-1"></i>
                OTP does not expire. You can request a new one anytime.
            </p>
        </div>
    </div>

    <script>
        function moveToNext(input, currentIndex) {
            const value = input.value;
            
            if (value.length === 1) {
                input.classList.add('filled');
                
                // Move to next input if available
                if (currentIndex < 6) {
                    const nextInput = document.querySelector(`input[data-index="${currentIndex + 1}"]`);
                    if (nextInput) {
                        nextInput.focus();
                    }
                }
                
                // Auto-submit when all inputs are filled
                if (currentIndex === 6) {
                    const allFilled = Array.from(document.querySelectorAll('.otp-input'))
                        .every(input => input.value.length === 1);
                    if (allFilled) {
                        document.getElementById('otpForm').submit();
                    }
                }
            } else {
                input.classList.remove('filled');
            }
        }

        function handleBackspace(input, currentIndex, event) {
            if (event.key === 'Backspace' && input.value === '') {
                if (currentIndex > 1) {
                    const prevInput = document.querySelector(`input[data-index="${currentIndex - 1}"]`);
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.value = '';
                        prevInput.classList.remove('filled');
                    }
                }
            }
        }

        function handlePaste(event) {
            event.preventDefault();
            const pasteData = event.clipboardData.getData('text').slice(0, 6);
            const inputs = document.querySelectorAll('.otp-input');
            
            pasteData.split('').forEach((char, index) => {
                if (inputs[index]) {
                    inputs[index].value = char;
                    inputs[index].classList.add('filled');
                }
            });
            
            // Focus the next empty input or submit if all filled
            const emptyInput = Array.from(inputs).find(input => !input.value);
            if (emptyInput) {
                emptyInput.focus();
            } else {
                document.getElementById('otpForm').submit();
            }
        }

        // Auto-focus first input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[data-index="1"]');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Only allow numbers in OTP inputs
        document.querySelectorAll('.otp-input').forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>