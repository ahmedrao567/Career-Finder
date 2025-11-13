<?php
include 'config.php';

// Redirect if no email to verify
if (!isset($_SESSION['verify_email'])) {
    header('Location: signup.php');
    exit();
}

$email = $_SESSION['verify_email'];

// Check if user is already verified
$stmt = $pdo->prepare("SELECT is_verified FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && $user['is_verified']) {
    unset($_SESSION['verify_email']);
    $_SESSION['success'] = "Email already verified. Please login.";
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 5px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <!-- Logo -->
    <div class="absolute top-6 left-6">
        <div class="logo text-2xl text-blue-600 flex items-center font-bold">
            <i class="fas fa-graduation-cap mr-2"></i>
            CareerFinder
        </div>
    </div>

    <!-- OTP Verification Form -->
    <div class="glass-effect rounded-2xl shadow-2xl w-full max-w-md p-8 bg-white">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Verify Your Email</h2>
            <p class="text-gray-600">Enter the 6-digit code sent to</p>
            <p class="text-blue-600 font-semibold"><?php echo htmlspecialchars($email); ?></p>
        </div>

        <!-- Display error/success messages -->
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

        <form id="otpForm" action="process-verify-otp.php" method="POST" class="space-y-6">
            <div class="flex justify-center space-x-3">
                <input type="text" name="otp1" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
                <input type="text" name="otp2" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
                <input type="text" name="otp3" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
                <input type="text" name="otp4" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
                <input type="text" name="otp5" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
                <input type="text" name="otp6" maxlength="1" class="otp-input border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500">
                Verify Email
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Didn't receive the code?
                <a href="resend-otp.php" class="text-blue-600 hover:text-blue-800 font-medium transition">Resend OTP</a>
            </p>
        </div>
    </div>

    <script>
        // Auto-focus and move to next input
        const inputs = document.querySelectorAll('.otp-input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && index > 0 && !e.target.value) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Paste OTP from clipboard
        document.addEventListener('paste', (e) => {
            const pasteData = e.clipboardData.getData('text');
            if (pasteData.length === 6 && /^\d+$/.test(pasteData)) {
                inputs.forEach((input, index) => {
                    input.value = pasteData[index] || '';
                });
                inputs[5].focus();
            }
        });
    </script>
</body>
</html>