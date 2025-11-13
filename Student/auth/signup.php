<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Auth System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <!-- Logo -->
    <div class="absolute top-6 left-6">
        <div class="logo text-2xl text-blue-600 flex items-center">
            <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            SecureAuth
        </div>
    </div>

    <!-- Signup Form -->
    <div class="glass-effect rounded-2xl shadow-xl w-full max-w-md p-8 bg-white">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Create Account</h2>
        <p class="text-center text-gray-600 mb-8">Join us today</p>
        
        <form id="signupForm" action="process-signup.php" method="POST" novalidate>
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

            <div class="mb-4">
                <label for="full_name" class="block text-gray-700 text-sm font-medium mb-2">Full Name</label>
                <input type="text" id="full_name" name="full_name" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus transition"
                    placeholder="Enter your full name">
                <span id="fullNameError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus transition"
                    placeholder="Choose a username">
                <span id="usernameError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus transition"
                    placeholder="Enter your email">
                <span id="emailError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus transition"
                    placeholder="Create a password">
                <span id="passwordError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus transition"
                    placeholder="Confirm your password">
                <span id="confirmPasswordError" class="text-red-500 text-xs mt-1 hidden"></span>
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Already have an account? 
                <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium transition">Sign In</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset errors
            document.querySelectorAll('.error').forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });
            
            let isValid = true;
            
            // Full Name validation
            const fullName = document.getElementById('full_name').value.trim();
            if (fullName === '') {
                showError('fullNameError', 'Full name is required');
                isValid = false;
            } else if (fullName.length < 2) {
                showError('fullNameError', 'Full name must be at least 2 characters');
                isValid = false;
            }
            
            // Username validation
            const username = document.getElementById('username').value.trim();
            if (username === '') {
                showError('usernameError', 'Username is required');
                isValid = false;
            } else if (username.length < 3) {
                showError('usernameError', 'Username must be at least 3 characters');
                isValid = false;
            }
            
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
            } else if (password.length < 6) {
                showError('passwordError', 'Password must be at least 6 characters');
                isValid = false;
            }
            
            // Confirm Password validation
            const confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword === '') {
                showError('confirmPasswordError', 'Please confirm your password');
                isValid = false;
            } else if (password !== confirmPassword) {
                showError('confirmPasswordError', 'Passwords do not match');
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