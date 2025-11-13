<?php 
include 'config.php';
include 'email-config.php';

// Generate OTP if not exists
if (!isset($_SESSION['company_signup_data'])) {
    $_SESSION['company_signup_data'] = [];
    $_SESSION['company_signup_otp'] = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $website = trim($_POST['website']);
    $phone = trim($_POST['phone']);

    // Basic validation
    $errors = [];
    
    if (empty($company_name)) $errors[] = "Company name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM companies WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    // Check if company name already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM companies WHERE company_name = ?");
        $stmt->execute([$company_name]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Company name already exists";
        }
    }

    if (empty($errors)) {
        // Generate OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Store data in session
        $_SESSION['company_signup_data'] = [
            'company_name' => $company_name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'website' => $website,
            'phone' => $phone,
            'founded_year' => $_POST['founded_year'] ?: null,
            'company_size' => $_POST['company_size'] ?: null
        ];
        $_SESSION['company_signup_otp'] = $otp;
        
        // Send OTP email
        $emailSender = new CompanyEmailSender();
        if ($emailSender->sendOTP($email, $company_name, $otp)) {
            $_SESSION['success'] = "OTP sent to your email successfully!";
            header("Location: verify-otp.php");
            exit();
        } else {
            $errors[] = "Failed to send OTP. Please try again.";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Sign Up - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    <!-- Signup Form -->
    <div class="glass-effect rounded-2xl shadow-xl w-full max-w-2xl p-8 bg-white">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Company Registration</h2>
            <p class="text-gray-600">Create your company account and start hiring talent</p>
        </div>

        <form id="signupForm" method="POST" novalidate class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Display error/success messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="col-span-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="col-span-2">
                <label for="company_name" class="block text-gray-700 text-sm font-medium mb-2">Company Name *</label>
                <input type="text" id="company_name" name="company_name" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="Enter your company name">
            </div>

            <div class="col-span-2">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Company Email *</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="company@example.com">
            </div>

            <div>
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password *</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="Create a password">
            </div>

            <div>
                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="Confirm your password">
            </div>

            <div>
                <label for="website" class="block text-gray-700 text-sm font-medium mb-2">Website</label>
                <input type="url" id="website" name="website"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="https://">
            </div>

            <div>
                <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Phone</label>
                <input type="tel" id="phone" name="phone"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="+1 (555) 123-4567">
            </div>

            <div>
                <label for="founded_year" class="block text-gray-700 text-sm font-medium mb-2">Founded Year</label>
                <input type="number" id="founded_year" name="founded_year" min="1900" max="<?php echo date('Y'); ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                    placeholder="<?php echo date('Y'); ?>">
            </div>

            <div>
                <label for="company_size" class="block text-gray-700 text-sm font-medium mb-2">Company Size</label>
                <select id="company_size" name="company_size"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                    <option value="">Select Size</option>
                    <option value="1-10">1-10 employees</option>
                    <option value="11-50">11-50 employees</option>
                    <option value="51-200">51-200 employees</option>
                    <option value="201-500">201-500 employees</option>
                    <option value="501-1000">501-1000 employees</option>
                    <option value="1000+">1000+ employees</option>
                </select>
            </div>

            <div class="col-span-2">
                <button type="submit" name="send_otp"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-green-500">
                    Send OTP Verification
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Already have a company account?
                <a href="index.php" class="text-green-600 hover:text-green-800 font-medium transition">Sign In</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            let isValid = true;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                isValid = false;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long!');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>