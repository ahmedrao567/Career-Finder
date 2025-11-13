<?php
include 'config.php';

$company_id = $_SESSION['company_id'];
$company = getCompanyProfile($pdo, $company_id);

// Get dashboard statistics
function getDashboardStats($pdo, $company_id)
{
    $stats = [
        'total_posts' => 0,
        'total_jobs' => 0,
        'active_jobs' => 0,
        'profile_views' => 0,
        'applications' => 0
    ];

    try {
        // Get total posts
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE company_id = ?");
        $stmt->execute([$company_id]);
        $stats['total_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get total jobs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE company_id = ?");
        $stmt->execute([$company_id]);
        $stats['total_jobs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get active jobs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE company_id = ? AND is_active = 1 AND (application_deadline IS NULL OR application_deadline >= CURDATE())");
        $stmt->execute([$company_id]);
        $stats['active_jobs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get applications (placeholder - will implement later)
        $stats['applications'] = 0;

        // Get profile views (placeholder - will implement later)
        $stats['profile_views'] = rand(50, 200); // Random for demo

    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
    }

    return $stats;
}

$stats = getDashboardStats($pdo, $company_id);

// Get recent activity
function getRecentActivity($pdo, $company_id)
{
    $activities = [];

    try {
        // Get recent posts
        $stmt = $pdo->prepare("SELECT 'post' as type, created_at, 'Posted: ' as action, LEFT(post_text, 50) as description FROM posts WHERE company_id = ? ORDER BY created_at DESC LIMIT 3");
        $stmt->execute([$company_id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get recent jobs
        $stmt = $pdo->prepare("SELECT 'job' as type, created_at, 'Job Posted: ' as action, title as description FROM jobs WHERE company_id = ? ORDER BY created_at DESC LIMIT 2");
        $stmt->execute([$company_id]);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $activities = array_merge($posts, $jobs);

        // Sort by date
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Limit to 5 activities
        $activities = array_slice($activities, 0, 5);
    } catch (PDOException $e) {
        error_log("Error getting recent activity: " . $e->getMessage());
    }

    return $activities;
}

$recent_activity = getRecentActivity($pdo, $company_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - CareerFinder</title>
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

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .animate-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-xl text-white p-8 mb-8 animate-in">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="text-center lg:text-left mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold mb-3">Welcome back, <?php echo $company['company_name']; ?>! 👋</h1>
                    <p class="text-green-100 text-lg">Here's what's happening with your company today.</p>
                    <div class="flex flex-wrap gap-4 mt-4 justify-center lg:justify-start">
                        <div class="flex items-center bg-white bg-opacity-20 px-4 py-2 rounded-full">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span><?php echo date('l, F j, Y'); ?></span>
                        </div>
                        <div class="flex items-center bg-white bg-opacity-20 px-4 py-2 rounded-full">
                            <i class="fas fa-clock mr-2"></i>
                            <span><?php echo date('g:i A'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="text-6xl pulse-animation">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Posts Card -->
            <a href="posts.php" class="stat-card group">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 mb-2"><?php echo $stats['total_posts']; ?></p>
                            <p class="text-gray-600 font-medium">Total Posts</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-100 text-blue-600 group-hover:bg-blue-200 transition">
                            <i class="fas fa-newspaper text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-blue-600 font-medium">
                        <span>View all posts</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Jobs Card -->
            <a href="jobs.php" class="stat-card group">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 mb-2"><?php echo $stats['total_jobs']; ?></p>
                            <p class="text-gray-600 font-medium">Total Jobs</p>
                            <p class="text-sm text-green-600 font-medium mt-1">
                                <?php echo $stats['active_jobs']; ?> active
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-green-100 text-green-600 group-hover:bg-green-200 transition">
                            <i class="fas fa-briefcase text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-green-600 font-medium">
                        <span>Manage jobs</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Applications Card -->
            <div class="stat-card group">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 mb-2"><?php echo $stats['applications']; ?></p>
                            <p class="text-gray-600 font-medium">Applications</p>
                            <p class="text-sm text-purple-600 font-medium mt-1">
                                New applications
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-purple-100 text-purple-600 group-hover:bg-purple-200 transition">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-purple-600 font-medium">
                        <span>View applications</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- Profile Views Card -->
            <!-- <div class="stat-card group">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 mb-2"><?php echo $stats['profile_views']; ?></p>
                            <p class="text-gray-600 font-medium">Profile Views</p>
                            <p class="text-sm text-orange-600 font-medium mt-1">
                                This month
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-orange-100 text-orange-600 group-hover:bg-orange-200 transition">
                            <i class="fas fa-eye text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-orange-600 font-medium">
                        <span>View analytics</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Quick Actions & Recent Activity -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.1s;">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Create Post -->
                        <button onclick="openCreatePostModal()"
                            class="group p-6 border-2 border-dashed border-gray-300 rounded-2xl hover:border-green-400 hover:bg-green-50 transition-all duration-300 text-left">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-200 transition">
                                    <i class="fas fa-plus text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">Create Post</h3>
                                    <p class="text-gray-600 text-sm mt-1">Share updates with job seekers</p>
                                </div>
                            </div>
                        </button>

                        <!-- Post a Job -->
                        <button onclick="openCreateJobModal()"
                            class="group p-6 border-2 border-dashed border-gray-300 rounded-2xl hover:border-blue-400 hover:bg-blue-50 transition-all duration-300 text-left">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-200 transition">
                                    <i class="fas fa-briefcase text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">Post a Job</h3>
                                    <p class="text-gray-600 text-sm mt-1">Hire talented professionals</p>
                                </div>
                            </div>
                        </button>

                        <!-- Manage Jobs -->
                        <a href="jobs.php"
                            class="group p-6 border-2 border-gray-200 rounded-2xl hover:border-purple-400 hover:bg-purple-50 transition-all duration-300 text-left">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 group-hover:bg-purple-200 transition">
                                    <i class="fas fa-tasks text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">Manage Jobs</h3>
                                    <p class="text-gray-600 text-sm mt-1">View and edit job postings</p>
                                </div>
                            </div>
                        </a>

                        <!-- Company Profile -->
                        <a href="profile.php"
                            class="group p-6 border-2 border-gray-200 rounded-2xl hover:border-orange-400 hover:bg-orange-50 transition-all duration-300 text-left">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 group-hover:bg-orange-200 transition">
                                    <i class="fas fa-building text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">Company Profile</h3>
                                    <p class="text-gray-600 text-sm mt-1">Update company information</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.2s;">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Recent Activity</h2>
                        <a href="#" class="text-green-600 hover:text-green-700 font-medium text-sm">View All</a>
                    </div>

                    <div class="space-y-4">
                        <?php if (count($recent_activity) > 0): ?>
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="flex items-start space-x-4 p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition group">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 
                                        <?php echo $activity['type'] == 'post' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'; ?>">
                                        <i class="fas <?php echo $activity['type'] == 'post' ? 'fa-newspaper' : 'fa-briefcase'; ?>"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-900 font-medium">
                                            <?php echo $activity['action'] . $activity['description']; ?>
                                            <?php if (strlen($activity['description']) > 50): ?>...<?php endif; ?>
                                        </p>
                                        <p class="text-gray-500 text-sm mt-1">
                                            <?php echo date('M j, Y \a\t g:i A', strtotime($activity['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition">
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-600 mb-2">No recent activity</h3>
                                <p class="text-gray-500">Your recent posts and jobs will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Profile Preview & Tips -->
            <div class="space-y-8">
                <!-- Profile Completion -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.3s;">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Profile Completion</h2>

                    <?php
                    $completion_items = [
                        'Basic Info' => !empty($company['about']),
                        'Logo' => !empty($company['logo']),
                        'Cover Photo' => !empty($company['cover_photo']),
                        'Location' => !empty($company['location']),
                        'Industry' => !empty($company['industry']),
                        'Website' => !empty($company['website'])
                    ];

                    $completed = array_filter($completion_items);
                    $completion_percentage = count($completed) / count($completion_items) * 100;
                    ?>

                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700"><?php echo round($completion_percentage); ?>% Complete</span>
                            <span class="text-sm text-gray-500"><?php echo count($completed); ?>/<?php echo count($completion_items); ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-500"
                                style="width: <?php echo $completion_percentage; ?>%"></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($completion_items as $item => $completed): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700"><?php echo $item; ?></span>
                                <?php if ($completed): ?>
                                    <i class="fas fa-check-circle text-green-500"></i>
                                <?php else: ?>
                                    <a href="profile.php" class="text-green-600 hover:text-green-700 text-sm font-medium">Add</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($completion_percentage < 100): ?>
                        <a href="profile.php"
                            class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-xl transition flex items-center justify-center space-x-2">
                            <i class="fas fa-edit"></i>
                            <span>Complete Your Profile</span>
                        </a>
                    <?php else: ?>
                        <div class="w-full mt-6 bg-green-100 text-green-700 font-medium py-3 px-4 rounded-xl text-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Profile Complete!
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Tips -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg text-white p-6 animate-in" style="animation-delay: 0.4s;">
                    <h2 class="text-2xl font-bold mb-4">Quick Tips</h2>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-lightbulb text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Engage Your Audience</h3>
                                <p class="text-blue-100 text-sm mt-1">Post regularly to keep job seekers interested in your company.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-bullseye text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Clear Job Descriptions</h3>
                                <p class="text-blue-100 text-sm mt-1">Write detailed job posts to attract the right candidates.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-chart-line text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Track Performance</h3>
                                <p class="text-blue-100 text-sm mt-1">Monitor your posts and jobs to see what works best.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-blue-400">
                        <p class="text-blue-100 text-sm text-center">
                            Need help? <a href="#" class="font-semibold hover:text-white underline">Contact Support</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <!-- Create Post Modal -->
    <div id="createPostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900">Create New Post</h3>
                    <button onclick="closeModal('createPostModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Add enctype="multipart/form-data" to the form -->
            <form action="create-post.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Content *</label>
                    <textarea name="post_text" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                        placeholder="Share updates, news, or opportunities with job seekers..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Image (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Click to upload image</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WEBP up to 5MB</p>
                            </div>
                            <input type="file" name="post_image" class="hidden" accept="image/*" onchange="previewPostImage(this)">
                        </label>
                    </div>
                    <!-- Add image preview -->
                    <div id="postImagePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-700 mb-2">Preview:</p>
                        <img id="postPreview" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('createPostModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Publish Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Job Modal -->
    <div id="createJobModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900">Post a New Job</h3>
                    <button onclick="closeModal('createJobModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="create-job.php" method="POST" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                            placeholder="e.g., Senior Software Engineer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Type *</label>
                        <select name="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                            <option value="">Select Type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" name="location"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                            placeholder="e.g., Remote, New York, NY">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                        <input type="text" name="salary_range"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                            placeholder="e.g., $80,000 - $120,000">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                    <textarea name="description" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                        placeholder="Describe the role, responsibilities, and requirements..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                    <textarea name="requirements" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                        placeholder="List the required skills, experience, and qualifications..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                    <input type="date" name="application_deadline"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('createJobModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-briefcase mr-2"></i>
                        Post Job
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openCreatePostModal() {
            document.getElementById('createPostModal').classList.remove('hidden');
        }

        // Add this to your existing JavaScript in index.php

        function previewPostImage(input) {
            const preview = document.getElementById('postPreview');
            const previewContainer = document.getElementById('postImagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
            }
        }

        function openCreateJobModal() {
            document.getElementById('createJobModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
                card.classList.add('animate-in');
            });
        });
    </script>
</body>

</html>