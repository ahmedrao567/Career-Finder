<?php
include 'config.php';

$company_id = $_SESSION['company_id'];
$company = getCompanyProfile($pdo, $company_id);
$projects = getCompanyProjects($pdo, $company_id);

// Initialize profile if doesn't exist
if (!$company || !$company['about']) {
    if (!$company) {
        $stmt = $pdo->prepare("INSERT INTO company_profiles (company_id) VALUES (?)");
        $stmt->execute([$company_id]);
        $company = getCompanyProfile($pdo, $company_id);
    }
}

// Common industries/categories
$industries = [
    'Technology',
    'Healthcare',
    'Finance',
    'Education',
    'E-commerce',
    'Manufacturing',
    'Marketing',
    'Real Estate',
    'Entertainment',
    'Food & Beverage',
    'Travel',
    'Automotive',
    'Energy',
    'Telecommunications',
    'Retail'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - CareerFinder</title>
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

        .project-card {
            transition: all 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto pb-8">
        <!-- Cover Photo Section -->
        <!-- Cover Photo Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="relative">
                <!-- Cover Photo -->
                <div class="h-64 gradient-bg relative">
                    <?php if ($company['cover_photo']): ?>
                        <img src="../assets/uploads/<?php echo $company['cover_photo']; ?>"
                            alt="Cover Photo" class="w-full h-64 object-cover">
                    <?php endif; ?>

                    <!-- Cover Photo Upload Button -->
                    <button onclick="openCoverPhotoModal()"
                        class="absolute bottom-4 right-4 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                        <i class="fas fa-camera"></i>
                        <span><?php echo $company['cover_photo'] ? 'Change Cover' : 'Add Cover'; ?></span>
                    </button>
                </div>

                <!-- Logo -->
                <div class="absolute -bottom-8 left-8">
                    <div class="relative">
                        <div class="w-24 h-24 bg-white rounded-xl p-1 shadow-xl">
                            <div class="w-full h-full bg-gradient-to-br from-green-400 to-blue-500 rounded-lg flex items-center justify-center text-white text-2xl font-bold overflow-hidden">
                                <?php if ($company['logo']): ?>
                                    <img src="../assets/uploads/<?php echo $company['logo']; ?>"
                                        alt="Company Logo"
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.parentNode.innerHTML='<?php echo strtoupper(substr($company['company_name'], 0, 1)); ?>';">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($company['company_name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Logo Upload Button -->
                        <button onclick="openLogoModal()"
                            class="absolute -bottom-1 -right-1 bg-green-600 hover:bg-green-700 text-white p-2 rounded-full shadow-lg transition">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company Info in Content Area -->
            <div class="pt-12 pb-6 px-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo $company['company_name']; ?></h1>
                        <div class="flex items-center space-x-4 text-gray-600">
                            <?php if ($company['industry']): ?>
                                <span class="flex items-center">
                                    <i class="fas fa-industry mr-2"></i>
                                    <?php echo $company['industry']; ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($company['location']): ?>
                                <span class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <?php echo $company['location']; ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($company['company_size']): ?>
                                <span class="flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    <?php echo $company['company_size']; ?> employees
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button onclick="openEditProfileModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition shadow-sm">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Company Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-in">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">About Us</h2>
                        <button onclick="openEditProfileModal()" class="text-green-600 hover:text-green-700 transition">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div class="prose max-w-none">
                        <?php if ($company['about']): ?>
                            <p class="text-gray-700 leading-relaxed text-lg"><?php echo nl2br(htmlspecialchars($company['about'])); ?></p>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-info-circle text-4xl mb-4"></i>
                                <p class="text-lg">Tell your company's story and mission</p>
                                <button onclick="openEditProfileModal()" class="mt-4 text-green-600 hover:text-green-700 font-medium">
                                    Add About Section
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Projects Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.1s;">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Our Projects</h2>
                        <button onclick="openAddProjectModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                            <i class="fas fa-plus"></i>
                            <span>Add Project</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php if (count($projects) > 0): ?>
                            <?php foreach ($projects as $project): ?>
                                <div class="project-card bg-gray-50 rounded-xl border border-gray-200 overflow-hidden group">
                                    <div class="relative">
                                        <?php if ($project['project_thumbnail']): ?>
                                            <img src="../assets/uploads/<?php echo $project['project_thumbnail']; ?>"
                                                alt="<?php echo htmlspecialchars($project['project_title']); ?>"
                                                class="w-full h-48 object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-48 bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white">
                                                <i class="fas fa-project-diagram text-4xl"></i>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Project Actions -->
                                        <div class="absolute top-3 right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                            <button onclick="openEditProjectModal(<?php echo $project['id']; ?>)"
                                                class="bg-white bg-opacity-90 hover:bg-opacity-100 text-blue-600 p-2 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button onclick="deleteProject(<?php echo $project['id']; ?>)"
                                                class="bg-white bg-opacity-90 hover:bg-opacity-100 text-red-600 p-2 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 text-lg mb-2"><?php echo htmlspecialchars($project['project_title']); ?></h3>

                                        <?php if ($project['project_description']): ?>
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($project['project_description']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($project['technologies']): ?>
                                            <div class="flex flex-wrap gap-1 mb-3">
                                                <?php
                                                $techs = explode(',', $project['technologies']);
                                                foreach ($techs as $tech):
                                                    $tech = trim($tech);
                                                    if (!empty($tech)):
                                                ?>
                                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($tech); ?></span>
                                                <?php endif;
                                                endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex justify-between items-center text-sm text-gray-500">
                                            <?php if ($project['project_date']): ?>
                                                <span><?php echo date('M Y', strtotime($project['project_date'])); ?></span>
                                            <?php endif; ?>

                                            <?php if ($project['project_link']): ?>
                                                <a href="<?php echo htmlspecialchars($project['project_link']); ?>"
                                                    target="_blank"
                                                    class="text-green-600 hover:text-green-700 font-medium flex items-center space-x-1">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    <span>View Project</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-2 text-center py-12">
                                <i class="fas fa-project-diagram text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Projects Yet</h3>
                                <p class="text-gray-500 mb-6">Showcase your amazing work to attract talent and clients</p>
                                <button onclick="openAddProjectModal()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition mx-auto">
                                    <i class="fas fa-plus"></i>
                                    <span>Add Your First Project</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Contact & Details -->
            <div class="space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.2s;">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h2>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Email</p>
                                <p class="text-gray-600"><?php echo $company['email']; ?></p>
                            </div>
                        </div>

                        <?php if ($company['website']): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Website</p>
                                    <a href="<?php echo htmlspecialchars($company['website']); ?>"
                                        target="_blank"
                                        class="text-green-600 hover:text-green-700 transition">
                                        <?php echo htmlspecialchars($company['website']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['portfolio_link']): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 flex-shrink-0">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Portfolio</p>
                                    <a href="<?php echo htmlspecialchars($company['portfolio_link']); ?>"
                                        target="_blank"
                                        class="text-green-600 hover:text-green-700 transition">
                                        View Portfolio
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['phone']): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 flex-shrink-0">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Phone</p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($company['phone']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['location']): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600 flex-shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Location</p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($company['location']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Company Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.3s;">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Company Details</h2>

                    <div class="space-y-4">
                        <?php if ($company['industry']): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Industry</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($company['industry']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['category']): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Category</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($company['category']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['company_size']): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Company Size</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($company['company_size']); ?> employees</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($company['founded_year']): ?>
                            <div class="flex justify-between items-center py-2">
                                <span class="font-medium text-gray-700">Founded</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($company['founded_year']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Specialization -->
                <?php if ($company['specialization']): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-in" style="animation-delay: 0.4s;">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Specialization</h2>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $specializations = explode(',', $company['specialization']);
                            foreach ($specializations as $spec):
                                $spec = trim($spec);
                                if (!empty($spec)):
                            ?>
                                    <span class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-3 py-1 rounded-full text-sm">
                                        <?php echo htmlspecialchars($spec); ?>
                                    </span>
                            <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include All Modals -->
    <?php include 'modals/cover-photo-modal.php'; ?>
    <?php include 'modals/logo-modal.php'; ?>
    <?php include 'modals/edit-profile-modal.php'; ?>
    <?php include 'modals/add-project-modal.php'; ?>
    <?php include 'modals/edit-project-modal.php'; ?>

    <script>
        // Modal functions
        function openCoverPhotoModal() {
            document.getElementById('coverPhotoModal').classList.remove('hidden');
        }

        function openLogoModal() {
            document.getElementById('logoModal').classList.remove('hidden');
        }

        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
        }

        function openAddProjectModal() {
            document.getElementById('addProjectModal').classList.remove('hidden');
        }

        function openEditProjectModal(projectId) {
            // Load project data via AJAX and populate the edit modal
            fetch(`get-project-data.php?id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit modal with data
                        document.getElementById('editProjectId').value = data.id;
                        document.getElementById('editProjectTitle').value = data.project_title;
                        document.getElementById('editProjectDescription').value = data.project_description;
                        document.getElementById('editProjectLink').value = data.project_link;
                        document.getElementById('editTechnologies').value = data.technologies;
                        document.getElementById('editProjectDate').value = data.project_date;

                        // Show modal
                        document.getElementById('editProjectModal').classList.remove('hidden');
                    } else {
                        alert('Error loading project data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading project data');
                });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function deleteProject(projectId) {
            if (confirm('Are you sure you want to delete this project?')) {
                fetch(`delete-project.php?id=${projectId}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove project element
                            const projectElement = document.querySelector(`[onclick="openEditProjectModal(${projectId})"]`).closest('.project-card');
                            projectElement.style.opacity = '0';
                            projectElement.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                projectElement.remove();
                                // Show success message
                                showNotification('Project deleted successfully!', 'success');
                            }, 300);
                        } else {
                            alert('Error deleting project');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting project');
                    });
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // Notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            notification.style.transform = 'translateX(100%)';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

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