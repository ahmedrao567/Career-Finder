<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php" class="logo text-xl text-blue-600 flex items-center font-bold">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    ProfessionalHub
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <input type="text"
                        placeholder="Search for jobs, people, posts..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <!-- Main Menu -->
            <div class="flex items-center space-x-6">
                <a href="index.php" class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition <?php echo $current_page == 'index.php' ? 'text-blue-600' : ''; ?>">
                    <i class="fas fa-home text-lg"></i>
                    <span class="text-xs mt-1">Feed</span>
                </a>

                <a href="jobs.php" class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-briefcase text-lg"></i>
                    <span class="text-xs mt-1">Jobs</span>
                </a>

                <a href="../message-system/messages.php" class="flex flex-col items-center text-gray-600 hover:text-purple-600 transition">
                    <i class="fas fa-envelope text-lg"></i>
                    <span class="text-xs mt-1">Messages</span>
                </a>

                <!-- <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="text-xs mt-1">Notifications</span>
                </a> -->

                <!-- User Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                        <span class="hidden md:block"><?php echo $_SESSION['full_name']; ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <a href="../auth/logout.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>