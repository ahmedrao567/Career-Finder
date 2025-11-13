<?php
include 'config.php';

if (!isset($_SESSION['university_email']) || !isset($_SESSION['otp_sent'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .otp-input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            outline: none;
        }
        .otp-input.filled {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="glass-effect rounded-2xl shadow-2xl w-full max-w-md p-8 bg-white">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Verify OTP</h2>
            <p class="text-gray-600">Enter the 6-digit code sent to your email</p>
            <p class="text-sm text-gray-500 mt-2 font-mono"><?php echo $_SESSION['university_email']; ?></p>
        </div>

        <form id="otpForm" action="process-login.php" method="POST" novalidate>
            <!-- Display error messages -->
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

            <div class="mb-6">
                <div class="flex justify-center space-x-3 mb-4">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="text" name="otp[]" maxlength="1" 
                            class="otp-input"
                            data-index="<?php echo $i; ?>"
                            oninput="moveToNext(this, <?php echo $i; ?>)" 
                            onkeydown="handleBackspace(this, <?php echo $i; ?>, event)"
                            onpaste="handlePaste(event)">
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="full_otp" id="fullOtp">
                <span id="otpError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-purple-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                Verify OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Need a new code? 
                <a href="send-otp.php?resend=true&email=<?php echo urlencode($_SESSION['university_email']); ?>" 
                   class="text-purple-600 hover:text-purple-800 font-medium transition"
                   id="resendLink">
                    Resend OTP
                </a>
            </p>
        </div>
    </div>

    <script>
        const otpInputs = document.querySelectorAll('input[name="otp[]"]');
        const fullOtpInput = document.getElementById('fullOtp');
        const submitBtn = document.getElementById('submitBtn');

        function moveToNext(input, currentIndex) {
            const value = input.value;
            
            if (value.length === 1) {
                input.classList.add('filled');
                
                if (currentIndex < 5) {
                    otpInputs[currentIndex + 1].focus();
                }
            } else {
                input.classList.remove('filled');
            }
            
            updateFullOtp();
            updateSubmitButton();
        }

        function handleBackspace(input, currentIndex, event) {
            if (event.key === 'Backspace' && input.value === '' && currentIndex > 0) {
                otpInputs[currentIndex - 1].focus();
            }
            
            updateFullOtp();
            updateSubmitButton();
        }

        function handlePaste(event) {
            event.preventDefault();
            const pasteData = event.clipboardData.getData('text').slice(0, 6);
            
            for (let i = 0; i < pasteData.length; i++) {
                if (i < otpInputs.length) {
                    otpInputs[i].value = pasteData[i];
                    otpInputs[i].classList.add('filled');
                }
            }
            
            if (pasteData.length > 0) {
                otpInputs[Math.min(pasteData.length - 1, 5)].focus();
            }
            
            updateFullOtp();
            updateSubmitButton();
        }

        function updateFullOtp() {
            const fullOtp = Array.from(otpInputs).map(input => input.value).join('');
            fullOtpInput.value = fullOtp;
        }

        function updateSubmitButton() {
            const fullOtp = fullOtpInput.value;
            submitBtn.disabled = fullOtp.length !== 6;
        }

        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fullOtp = fullOtpInput.value;
            
            if (fullOtp.length !== 6) {
                showError('otpError', 'Please enter complete 6-digit OTP');
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';
            
            this.submit();
        });

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            otpInputs[0].focus();
            updateSubmitButton();
        });
    </script>
</body>
</html>