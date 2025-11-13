<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation -->
<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php" class="text-xl text-green-600 flex items-center font-bold">
                    <i class="fas fa-building mr-2"></i>
                    CareerFinder
                </a>
            </div>

            <!-- Company Menu -->
            <div class="flex items-center space-x-6">
                <a href="index.php" class="flex flex-col items-center text-gray-600 hover:text-green-600 transition <?php echo $current_page == 'index.php' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-home text-lg"></i>
                    <span class="text-xs mt-1">Dashboard</span>
                </a>

                <a href="profile.php" class="flex flex-col items-center text-gray-600 hover:text-green-600 transition <?php echo $current_page == 'profile.php' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-building text-lg"></i>
                    <span class="text-xs mt-1">Profile</span>
                </a>

                <a href="posts.php" class="flex flex-col items-center text-gray-600 hover:text-green-600 transition <?php echo $current_page == 'posts.php' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-newspaper text-lg"></i>
                    <span class="text-xs mt-1">Posts</span>
                </a>

                <a href="jobs.php" class="flex flex-col items-center text-gray-600 hover:text-green-600 transition <?php echo $current_page == 'jobs.php' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-briefcase text-lg"></i>
                    <span class="text-xs mt-1">Jobs</span>
                </a>

                <a href="../message-system/messages.php" class="flex flex-col items-center text-gray-600 hover:text-purple-600 transition">
                    <i class="fas fa-envelope text-lg"></i>
                    <span class="text-xs mt-1">Messages</span>
                </a>

                <!-- Company Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 text-gray-700 hover:text-green-600 transition">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            <?php echo strtoupper(substr($_SESSION['company_name'], 0, 1)); ?>
                        </div>
                        <span class="hidden md:block"><?php echo $_SESSION['company_name']; ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-building mr-2"></i>Company Profile
                        </a>
                        <a href="settings.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <a href="../company-auth/logout.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>