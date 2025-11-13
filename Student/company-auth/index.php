<?php include 'config.php'; 

// Redirect if already logged in
if (isset($_SESSION['company_id']) && $_SESSION['company_logged_in']) {
    header("Location: ../company-dashboard/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login - CareerFinder</title>
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

    <!-- Login Form -->
    <div class="glass-effect rounded-2xl shadow-xl w-full max-w-md p-8 bg-white">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Company Login</h2>
            <p class="text-gray-600">Sign in to your company account</p>
        </div>
        
        <form id="loginForm" action="process-login.php" method="POST" novalidate>
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

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Company Email</label>
                <input type="email" id="email" name="email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="company@example.com">
                <span id="emailError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="Enter your password">
                <span id="passwordError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <button type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-green-300">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Don't have a company account? 
                <a href="signup.php" class="text-green-600 hover:text-green-800 font-medium transition">Sign Up</a>
            </p>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-gray-500 text-sm">Looking for a job? 
                    <a href="../auth-system/index.php" class="text-blue-600 hover:text-blue-800 font-medium transition">Sign in as Job Seeker</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset errors
            document.querySelectorAll('.error').forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });
            
            let isValid = true;
            
            // Email validation
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                showError('emailError', 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                showError('emailError', 'Please enter a valid email address');
                isValid = false;
            }
            
            // Password validation
            const password = document.getElementById('password').value;
            if (password === '') {
                showError('passwordError', 'Password is required');
                isValid = false;
            }
            
            if (isValid) {
                this.submit();
            }
        });
        
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    </script>
</body>
</html>