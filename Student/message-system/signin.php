<?php
// signin.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $userType = sanitizeInput($_POST['user_type']);
    
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Determine which table to query based on user type
    $table = '';
    $idField = '';
    
    switch($userType) {
        case 'user':
            $table = 'users';
            $idField = 'id';
            break;
        case 'company':
            $table = 'companies';
            $idField = 'id';
            break;
        case 'university':
            $table = 'universities';
            $idField = 'id';
            break;
        default:
            die("Invalid user type");
    }
    
    // Check credentials
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Create session
        $sessionToken = generateSessionToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, user_type, session_token, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user[$idField], $userType, $sessionToken, $expiresAt]);
        
        setcookie('session_token', $sessionToken, time() + (86400 * 1), "/"); // 1 day
        $_SESSION['user_id'] = $user[$idField];
        $_SESSION['user_type'] = $userType;
        $_SESSION['user_name'] = $userType === 'user' ? $user['full_name'] : 
                                ($userType === 'company' ? $user['company_name'] : $user['university_name']);
        
        header("Location: messages.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - ConnectHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
            <p class="text-gray-600">Sign in to continue your conversations</p>
        </div>

        <!-- Signin Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <!-- User Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">I am a</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="user_type" value="user" class="hidden" checked>
                            <i class="fas fa-user text-gray-400 text-xl mb-2"></i>
                            <span class="text-sm font-medium">User</span>
                        </label>
                        <label class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="user_type" value="company" class="hidden">
                            <i class="fas fa-building text-gray-400 text-xl mb-2"></i>
                            <span class="text-sm font-medium">Company</span>
                        </label>
                        <label class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="user_type" value="university" class="hidden">
                            <i class="fas fa-graduation-cap text-gray-400 text-xl mb-2"></i>
                            <span class="text-sm font-medium">University</span>
                        </label>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" required 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" required 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Enter your password">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="#" class="text-blue-600 font-semibold hover:text-blue-700">Sign up</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Radio button selection styling
        document.querySelectorAll('input[name="user_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label[class*="border-"]').forEach(label => {
                    label.classList.remove('border-blue-500', 'bg-blue-50');
                    label.classList.add('border-gray-200');
                    label.querySelector('i').classList.remove('text-blue-500');
                    label.querySelector('i').classList.add('text-gray-400');
                });
                
                if (this.checked) {
                    const label = this.parentElement;
                    label.classList.remove('border-gray-200');
                    label.classList.add('border-blue-500', 'bg-blue-50');
                    label.querySelector('i').classList.remove('text-gray-400');
                    label.querySelector('i').classList.add('text-blue-500');
                }
            });
        });

        // Trigger initial state
        document.querySelector('input[name="user_type"]:checked').dispatchEvent(new Event('change'));
    </script>
</body>
</html>