<?php
include 'config.php';

$company_id = $_SESSION['company_id'];
$company = getCompanyProfile($pdo, $company_id);
$jobs = getCompanyJobs($pdo, $company_id);

function getCompanyJobs($pdo, $company_id) {
    $stmt = $pdo->prepare("
        SELECT j.*, 
               COUNT(ja.id) as application_count,
               SUM(CASE WHEN ja.status = 'pending' THEN 1 ELSE 0 END) as pending_applications
        FROM jobs j 
        LEFT JOIN job_applications ja ON j.id = ja.job_id 
        WHERE j.company_id = ? 
        GROUP BY j.id 
        ORDER BY j.created_at DESC
    ");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle job actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'toggle_job_status':
            $job_id = $_POST['job_id'];
            $job = getJobById($pdo, $job_id, $company_id);
            if ($job) {
                $new_status = $job['is_active'] ? 0 : 1;
                $stmt = $pdo->prepare("UPDATE jobs SET is_active = ? WHERE id = ? AND company_id = ?");
                if ($stmt->execute([$new_status, $job_id, $company_id])) {
                    $_SESSION['success'] = "Job status updated successfully!";
                }
            }
            break;
            
        case 'delete_job':
            $job_id = $_POST['job_id'];
            $job = getJobById($pdo, $job_id, $company_id);
            if ($job) {
                $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND company_id = ?");
                if ($stmt->execute([$job_id, $company_id])) {
                    $_SESSION['success'] = "Job deleted successfully!";
                }
            }
            break;
    }
    header("Location: jobs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - CareerFinder</title>
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
        .job-card {
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        .status-badge {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Job Management</h1>
                <p class="text-gray-600">Manage your job postings and applications</p>
            </div>
            <button onclick="openCreateJobModal()" 
                    class="mt-4 lg:mt-0 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium transition flex items-center space-x-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Post New Job</span>
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-2"><?php echo count($jobs); ?></p>
                        <p class="text-gray-600 font-medium">Total Jobs</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-100 text-blue-600">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?php echo array_reduce($jobs, function($carry, $job) { return $carry + $job['application_count']; }, 0); ?>
                        </p>
                        <p class="text-gray-600 font-medium">Total Applications</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-green-100 text-green-600">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?php echo array_reduce($jobs, function($carry, $job) { return $carry + $job['pending_applications']; }, 0); ?>
                        </p>
                        <p class="text-gray-600 font-medium">Pending Reviews</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?php echo array_reduce($jobs, function($carry, $job) { return $carry + ($job['is_active'] ? 1 : 0); }, 0); ?>
                        </p>
                        <p class="text-gray-600 font-medium">Active Jobs</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-purple-100 text-purple-600">
                        <i class="fas fa-eye text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Jobs List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">Your Job Postings</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php if (count($jobs) > 0): ?>
                            <?php foreach ($jobs as $job): ?>
                                <div class="job-card p-6 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($job['title']); ?></h3>
                                                <span class="status-badge px-3 py-1 rounded-full text-xs font-medium <?php echo $job['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                                    <?php echo $job['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                                                <span class="flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                                    <?php echo $job['location'] ?: 'Not specified'; ?>
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <?php echo $job['type']; ?>
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                                    <?php echo $job['salary_range'] ?: 'Salary not specified'; ?>
                                                </span>
                                            </div>
                                            
                                            <p class="text-gray-700 text-sm mb-4 line-clamp-2">
                                                <?php echo htmlspecialchars($job['description']); ?>
                                            </p>
                                            
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-users mr-1"></i>
                                                        <?php echo $job['application_count']; ?> applications
                                                    </span>
                                                    <?php if ($job['pending_applications'] > 0): ?>
                                                        <span class="flex items-center text-orange-600">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            <?php echo $job['pending_applications']; ?> pending
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>
                                                        Posted <?php echo date('M j, Y', strtotime($job['created_at'])); ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <a href="job-applications.php?id=<?php echo $job['id']; ?>" 
                                                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center space-x-2">
                                                        <i class="fas fa-eye"></i>
                                                        <span>View Applications</span>
                                                    </a>
                                                    
                                                    <div class="relative group">
                                                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        
                                                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                                                            <button onclick="openEditJobModal(<?php echo $job['id']; ?>)" 
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 flex items-center space-x-2">
                                                                <i class="fas fa-edit text-blue-600"></i>
                                                                <span>Edit Job</span>
                                                            </button>
                                                            
                                                            <form method="POST" class="w-full">
                                                                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                                <input type="hidden" name="action" value="toggle_job_status">
                                                                <button type="submit" 
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 flex items-center space-x-2">
                                                                    <i class="fas fa-power-off text-orange-600"></i>
                                                                    <span><?php echo $job['is_active'] ? 'Deactivate' : 'Activate'; ?></span>
                                                                </button>
                                                            </form>
                                                            
                                                            <form method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this job? This action cannot be undone.');">
                                                                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                                <input type="hidden" name="action" value="delete_job">
                                                                <button type="submit" 
                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center space-x-2">
                                                                    <i class="fas fa-trash"></i>
                                                                    <span>Delete Job</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Jobs Posted Yet</h3>
                                <p class="text-gray-500 mb-6">Start attracting talent by posting your first job opportunity</p>
                                <button onclick="openCreateJobModal()" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-medium">
                                    Post Your First Job
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Applications -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <button onclick="openCreateJobModal()" 
                                class="w-full text-left p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-green-400 hover:bg-green-50 transition flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Post New Job</h3>
                                <p class="text-gray-600 text-sm">Create a new job posting</p>
                            </div>
                        </button>
                        
                        <a href="job-applications.php" 
                           class="w-full text-left p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">View All Applications</h3>
                                <p class="text-gray-600 text-sm">Manage all job applications</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Applications</h2>
                        <a href="job-applications.php" class="text-green-600 hover:text-green-700 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-4">
                        <?php
                        $recent_applications = getAllCompanyApplications($pdo, $company_id);
                        $recent_applications = array_slice($recent_applications, 0, 3);
                        ?>
                        
                        <?php if (count($recent_applications) > 0): ?>
                            <?php foreach ($recent_applications as $application): ?>
                                <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 hover:border-green-200 hover:bg-green-50 transition group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        <?php echo strtoupper(substr($application['full_name'], 0, 1)); ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate"><?php echo htmlspecialchars($application['full_name']); ?></p>
                                        <p class="text-sm text-gray-600 truncate">Applied for <?php echo htmlspecialchars($application['job_title']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo date('M j, g:i A', strtotime($application['applied_at'])); ?></p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        <?php echo $application['status'] == 'pending' ? 'bg-orange-100 text-orange-800' : 
                                               ($application['status'] == 'accepted' ? 'bg-green-100 text-green-800' : 
                                               ($application['status'] == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')); ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="text-sm">No applications yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Job Modals -->
    <?php include 'modals/create-job-modal.php'; ?>
    <?php include 'modals/edit-job-modal.php'; ?>

    <script>
        function openCreateJobModal() {
            document.getElementById('createJobModal').classList.remove('hidden');
        }

        function openEditJobModal(jobId) {
            // Load job data via AJAX
            fetch(`get-job-data.php?id=${jobId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit modal
                        document.getElementById('editJobId').value = data.id;
                        document.getElementById('editJobTitle').value = data.title;
                        document.getElementById('editJobDescription').value = data.description;
                        document.getElementById('editJobRequirements').value = data.requirements;
                        document.getElementById('editJobLocation').value = data.location;
                        document.getElementById('editJobType').value = data.type;
                        document.getElementById('editSalaryRange').value = data.salary_range;
                        document.getElementById('editApplicationDeadline').value = data.application_deadline;
                        
                        // Show modal
                        document.getElementById('editJobModal').classList.remove('hidden');
                    } else {
                        alert('Error loading job data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading job data');
                });
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

        // Add line-clamp utility
        const style = document.createElement('style');
        style.textContent = `
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>