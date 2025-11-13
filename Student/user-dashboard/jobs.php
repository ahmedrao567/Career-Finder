<?php
include 'config.php';

// Handle job actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_job'])) {
        $job_id = $_POST['job_id'];
        if (toggleSaveJob($pdo, $_SESSION['user_id'], $job_id)) {
            $_SESSION['success'] = "Job saved successfully!";
        } else {
            $_SESSION['error'] = "Failed to save job.";
        }
        header("Location: jobs.php" . (!empty($_GET) ? '?' . http_build_query($_GET) : ''));
        exit();
    }
}

// Get filters from URL
$filters = [
    'type' => $_GET['type'] ?? '',
    'location' => $_GET['location'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Get jobs with filters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;
$jobs = getJobs($pdo, $filters, $limit, $offset);

// Get unique locations and types for filters
$locations_stmt = $pdo->query("SELECT DISTINCT location FROM jobs WHERE is_active = 1 AND location IS NOT NULL ORDER BY location");
$locations = $locations_stmt->fetchAll(PDO::FETCH_COLUMN);

$types_stmt = $pdo->query("SELECT DISTINCT type FROM jobs WHERE is_active = 1 ORDER BY type");
$types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Jobs - CareerFinder</title>
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
        .job-card {
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-full-time { background-color: #10B981; color: white; }
        .badge-part-time { background-color: #3B82F6; color: white; }
        .badge-contract { background-color: #8B5CF6; color: white; }
        .badge-internship { background-color: #F59E0B; color: white; }
        .badge-remote { background-color: #EF4444; color: white; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Find Your Dream Job</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover thousands of job opportunities from top companies. Filter by location, job type, and more.
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Jobs</label>
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                               placeholder="Job title, company, or keywords"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                        <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- Job Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                        <option value="">All Types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo $filters['type'] === $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('-', ' ', $type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <select name="location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $location): ?>
                            <option value="<?php echo htmlspecialchars($location); ?>" <?php echo $filters['location'] === $location ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="md:col-span-4 flex justify-end space-x-3 pt-2">
                    <a href="jobs.php" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition flex items-center space-x-2">
                        <i class="fas fa-filter"></i>
                        <span>Apply Filters</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                <?php echo count($jobs); ?> Job<?php echo count($jobs) !== 1 ? 's' : ''; ?> Found
                <?php if (!empty($filters['search']) || !empty($filters['type']) || !empty($filters['location'])): ?>
                    <span class="text-lg font-normal text-gray-600 ml-2">(Filtered)</span>
                <?php endif; ?>
            </h2>
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span class="flex items-center space-x-1">
                    <i class="fas fa-sort"></i>
                    <span>Sort by: </span>
                    <select class="border-0 bg-transparent focus:ring-0">
                        <option>Most Recent</option>
                        <option>Most Relevant</option>
                        <option>Salary: High to Low</option>
                    </select>
                </span>
            </div>
        </div>

        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 job-card fade-in hover:border-blue-300">
                        <!-- Job Header -->
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <?php if ($job['company_logo']): ?>
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <img src="../user-dashboard/assets/uploads/<?php echo $job['company_logo']; ?>" 
                                                 alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-building text-blue-600 text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg leading-tight">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </h3>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                    </div>
                                </div>
                                <form method="POST" class="flex-shrink-0">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                    <button type="submit" name="save_job" 
                                            class="p-2 rounded-lg transition <?php echo $job['is_saved'] ? 'bg-blue-100 text-blue-600 hover:bg-blue-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'; ?>"
                                            title="<?php echo $job['is_saved'] ? 'Remove from saved' : 'Save for later'; ?>">
                                        <i class="fas fa-bookmark <?php echo $job['is_saved'] ? 'text-blue-600' : ''; ?>"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Job Badges -->
                            <div class="flex flex-wrap gap-2">
                                <span class="badge badge-<?php echo $job['type']; ?>">
                                    <?php echo ucfirst(str_replace('-', ' ', $job['type'])); ?>
                                </span>
                                <?php if ($job['location']): ?>
                                    <span class="badge bg-gray-100 text-gray-700">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($job['salary_range']): ?>
                                    <span class="badge bg-green-100 text-green-700">
                                        <i class="fas fa-dollar-sign mr-1"></i>
                                        <?php echo htmlspecialchars($job['salary_range']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="p-6">
                            <p class="text-gray-700 line-clamp-3 mb-4">
                                <?php echo htmlspecialchars(substr($job['description'], 0, 150) . (strlen($job['description']) > 150 ? '...' : '')); ?>
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $job['application_count']; ?> applications</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-clock"></i>
                                    <span>
                                        <?php 
                                        $days_ago = floor((time() - strtotime($job['created_at'])) / (60 * 60 * 24));
                                        echo $days_ago == 0 ? 'Today' : ($days_ago . ' day' . ($days_ago != 1 ? 's' : '') . ' ago');
                                        ?>
                                    </span>
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <button onclick="openJobDetailsModal(<?php echo $job['id']; ?>)" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-medium transition text-center">
                                    View Details
                                </button>
                                <?php if ($job['has_applied']): ?>
                                    <button class="flex-1 bg-green-100 text-green-700 py-2.5 px-4 rounded-lg font-medium cursor-default">
                                        <i class="fas fa-check mr-2"></i>Applied
                                    </button>
                                <?php else: ?>
                                    <button onclick="openApplyModal(<?php echo $job['id']; ?>)" 
                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 px-4 rounded-lg font-medium transition">
                                        Apply Now
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-12">
                    <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No jobs found</h3>
                    <p class="text-gray-500 mb-4">Try adjusting your search filters or check back later for new opportunities.</p>
                    <a href="jobs.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-flex items-center space-x-2">
                        <i class="fas fa-refresh"></i>
                        <span>Clear Filters</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More -->
        <?php if (count($jobs) >= $limit): ?>
            <div class="text-center">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                   class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-8 py-3 rounded-lg font-medium transition inline-flex items-center space-x-2">
                    <i class="fas fa-redo"></i>
                    <span>Load More Jobs</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Job Details Modal -->
    <div id="jobDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <!-- Content loaded via AJAX -->
    </div>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <!-- Content loaded via AJAX -->
    </div>

    <script>
        // Open Job Details Modal
        function openJobDetailsModal(jobId) {
            fetch('get-job-details.php?id=' + jobId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('jobDetailsModal').innerHTML = html;
                    document.getElementById('jobDetailsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading job details');
                });
        }

        // Open Apply Modal
        function openApplyModal(jobId) {
            fetch('get-apply-form.php?id=' + jobId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('applyModal').innerHTML = html;
                    document.getElementById('applyModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading application form');
                });
        }

        // Close Modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // Real-time search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });

            // Auto-submit on filter changes
            document.querySelectorAll('select[name="type"], select[name="location"]').forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
</body>
</html>