<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerFinder - Find Your Path</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to CareerFinder</h1>
            <p class="text-xl text-gray-600">Choose your path to get started</p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Job Seeker Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-blue-200 overflow-hidden card-hover">
                <div class="h-3 bg-blue-500"></div>
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-graduate text-blue-600 text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Job Seeker</h2>
                        <p class="text-gray-600">Find your dream job and build your career</p>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Browse thousands of job opportunities</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Connect with top companies</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Build your professional profile</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Get personalized job recommendations</span>
                        </li>
                    </ul>

                    <div class="space-y-3">
                        <a href="auth/signup.php"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i>
                            Sign Up as Job Seeker
                        </a>
                        <a href="auth/index.php"
                            class="w-full border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In as Job Seeker
                        </a>
                    </div>
                </div>
            </div>

            <!-- Company Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-green-200 overflow-hidden card-hover">
                <div class="h-3 bg-green-500"></div>
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-green-600 text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Company</h2>
                        <p class="text-gray-600">Hire talent and grow your business</p>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Post job opportunities</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Connect with qualified candidates</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Showcase your company culture</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Build your employer brand</span>
                        </li>
                    </ul>

                    <div class="space-y-3">
                        <a href="company-auth/signup.php"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-building mr-2"></i>
                            Sign Up as Company
                        </a>
                        <a href="company-auth/index.php"
                            class="w-full border border-green-600 text-green-600 hover:bg-green-50 font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In as Company
                        </a>
                    </div>
                </div>
            </div>

            <!-- Add this card to the existing choice.php grid -->
            <div class="bg-white rounded-2xl shadow-xl border border-purple-200 overflow-hidden card-hover">
                <div class="h-3 bg-purple-500"></div>
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-university text-purple-600 text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">University</h2>
                        <p class="text-gray-600">Manage your university profile and posts</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Share updates and announcements</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Connect with students</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Showcase university achievements</span>
                        </li>
                    </ul>
                    <div class="space-y-3">
                        <a href="university-auth/index.php"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In as University
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12">
            <p class="text-gray-500">
                Already have an account?
                <a href="#" class="text-blue-600 hover:underline font-medium">Check your email</a>
                to see which type of account you have.
            </p>
        </div>
    </div>
</body>

</html>