<?php
include 'config.php';

$user_id = $_SESSION['user_id'];
$profile = getUserProfile($pdo, $user_id);
$experiences = getUserExperiences($pdo, $user_id);
$skills = getUserSkills($pdo, $user_id);
$graduation_years = getGraduationYears();

// Initialize profile if doesn't exist
if (!$profile) {
    $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $profile = getUserProfile($pdo, $user_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ProfessionalHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>

    <div class="max-w-4xl mx-auto pb-8 mt-5">
        <!-- Cover Photo Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="relative">
                <!-- Cover Photo -->
                <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-lg relative">
                    <?php if ($profile['cover_photo']): ?>
                        <img src="assets/uploads/<?php echo $profile['cover_photo']; ?>"
                            alt="Cover Photo" class="w-full h-48 object-cover rounded-t-lg">
                    <?php endif; ?>

                    <!-- Cover Photo Upload Button -->
                    <button onclick="openCoverPhotoModal()"
                        class="absolute bottom-4 right-4 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                        <i class="fas fa-camera"></i>
                        <span><?php echo $profile['cover_photo'] ? 'Change Cover' : 'Add Cover'; ?></span>
                    </button>
                </div>

                <!-- Profile Photo -->
                <div class="absolute -bottom-8 left-8">
                    <div class="relative">
                        <div class="w-32 h-32 bg-white rounded-full p-1 shadow-lg">
                            <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                <?php if ($profile['profile_photo']): ?>
                                    <img src="assets/uploads/<?php echo $profile['profile_photo']; ?>"
                                        alt="Profile Photo" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-blue-500 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Profile Photo Upload Button -->
                        <button onclick="openProfilePhotoModal()"
                            class="absolute bottom-2 right-2 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="pt-12 pb-6 px-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo $_SESSION['full_name']; ?></h1>
                        <p class="text-gray-600 text-lg mt-1"><?php echo $profile['designation'] ?: 'Add your designation'; ?></p>

                        <!-- University Section in Profile Info -->
                        <div class="mt-2">
                            <?php if ($profile['university']): ?>
                                <div class="flex items-center text-gray-700 group">
                                    <i class="fas fa-university mr-2"></i>
                                    <span class="font-medium"><?php echo $profile['university']; ?></span>

                                    <button onclick="openEditUniversityModal()"
                                        class="ml-2 text-blue-600 hover:text-blue-800 opacity-0 group-hover:opacity-100 transition">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-university mr-2"></i>
                                    <span>No university added</span>
                                    <a href="../find-university.php" class="ml-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Find best university for you
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <p class="text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <?php echo $_SESSION['email']; ?>
                        </p>
                        <?php if ($profile['location']): ?>
                            <p class="text-gray-500 mt-1 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <?php echo $profile['location']; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="flex space-x-2">
                        <button onclick="openEditUniversityModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                            <i class="fas fa-university"></i>
                            <span><?php echo $profile['university'] ? 'Edit Education' : 'Add Education'; ?></span>
                        </button>
                        <button onclick="openEditProfileModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Education Section (Detailed View) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Education</h2>
                <button onclick="openEditUniversityModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-edit"></i>
                    <span><?php echo $profile['university'] ? 'Edit Education' : 'Add Education'; ?></span>
                </button>
            </div>

            <?php if ($profile['university']): ?>
                <div class="space-y-4">
                    <div class="flex justify-between items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg"><?php echo $profile['university']; ?></h3>
                            <div class="mt-2 space-y-1">
                                <?php if ($profile['degree']): ?>
                                    <p class="text-gray-700 flex items-center">
                                        <i class="fas fa-graduation-cap mr-2 text-blue-500"></i>
                                        <span class="font-medium">Degree:</span>
                                        <span class="ml-2"><?php echo $profile['degree']; ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if ($profile['field_of_study']): ?>
                                    <p class="text-gray-700 flex items-center">
                                        <i class="fas fa-book mr-2 text-green-500"></i>
                                        <span class="font-medium">Field of Study:</span>
                                        <span class="ml-2"><?php echo $profile['field_of_study']; ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if ($profile['graduation_year']): ?>
                                    <p class="text-gray-700 flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                                        <span class="font-medium">Graduation Year:</span>
                                        <span class="ml-2"><?php echo $profile['graduation_year']; ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openEditUniversityModal()"
                                class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUniversity()"
                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-university text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Education Added</h3>
                    <p class="text-gray-500 mb-4">Add your educational background to showcase your qualifications</p>
                    <button onclick="openEditUniversityModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition mx-auto">
                        <i class="fas fa-plus"></i>
                        <span>Add Education</span>
                    </button>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-search mr-2"></i>Find best university for you
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rest of the sections (About, Skills, Experience) remain the same -->
        <!-- About Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">About</h2>
                <button onclick="openEditProfileModal()" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            <p class="text-gray-700 leading-relaxed">
                <?php echo $profile['about'] ?: 'Share information about yourself, your background, and your professional interests.'; ?>
            </p>
        </div>

        <!-- Skills Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Skills</h2>
                <button onclick="openAddSkillModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-plus"></i>
                    <span>Add Skill</span>
                </button>
            </div>

            <div class="flex flex-wrap gap-3">
                <?php if (count($skills) > 0): ?>
                    <?php foreach ($skills as $skill): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-full px-4 py-2 flex items-center space-x-2 group">
                            <span class="text-blue-800 font-medium"><?php echo $skill['skill_name']; ?></span>
                            <span class="text-blue-600 text-sm">(<?php echo $skill['proficiency']; ?>)</span>
                            <button onclick="deleteSkill(<?php echo $skill['id']; ?>)"
                                class="text-blue-600 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No skills added yet. Add your skills to showcase your expertise.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Experience Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Experience</h2>
                <button onclick="openAddExperienceModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-plus"></i>
                    <span>Add Experience</span>
                </button>
            </div>

            <div class="space-y-6" id="experiencesContainer">
                <?php if (count($experiences) > 0): ?>
                    <?php foreach ($experiences as $exp): ?>
                        <div class="border-l-4 border-blue-500 pl-4 py-1 relative group" id="experience-<?php echo $exp['id']; ?>">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($exp['position']); ?></h3>
                                    <p class="text-gray-700"><?php echo htmlspecialchars($exp['company']); ?></p>
                                    <p class="text-gray-500 text-sm mt-1">
                                        <?php echo date('M Y', strtotime($exp['start_date'])); ?> -
                                        <?php echo $exp['current_job'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                                    </p>
                                    <?php if ($exp['description']): ?>
                                        <p class="text-gray-600 mt-2"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                    <button onclick="loadExperienceData(<?php echo $exp['id']; ?>)"
                                        class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteExperience(<?php echo $exp['id']; ?>)"
                                        class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No experience added yet. Add your professional experience.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Saved Posts Section -->
        <!-- Saved Posts Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Saved Posts</h2>
                <span class="text-gray-500 text-sm" id="savedCount">
                    <?php
                    $saved_posts = getSavedPosts($pdo, $user_id);
                    echo count($saved_posts) . ' saved';
                    ?>
                </span>
            </div>

            <div class="space-y-4" id="savedPostsContainer">
                <?php if (count($saved_posts) > 0): ?>
                    <?php foreach ($saved_posts as $post): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition group saved-post-item" data-post-id="<?php echo $post['id']; ?>">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                                            <?php if ($post['poster_avatar']): ?>
                                                <img src="assets/uploads/<?php echo $post['poster_avatar']; ?>"
                                                    alt="<?php echo htmlspecialchars($post['poster_name']); ?>"
                                                    class="w-full h-full rounded-full object-cover">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($post['poster_name'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="#" class="font-medium text-gray-900 hover:text-blue-600 transition hover:underline">
                                                <?php echo htmlspecialchars($post['poster_name']); ?>
                                            </a>
                                            <p class="text-gray-500 text-xs">
                                                <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                                • Saved <?php echo date('M j', strtotime($post['saved_at'])); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-gray-700 text-sm line-clamp-2 leading-relaxed">
                                        <?php echo htmlspecialchars($post['post_text']); ?>
                                    </p>

                                    <?php if ($post['post_image']): ?>
                                        <div class="mt-2 text-blue-600 text-xs">
                                            <i class="fas fa-image mr-1"></i>
                                            Contains image
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button type="button"
                                    onclick="unsavePost(<?php echo $post['id']; ?>)"
                                    class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition opacity-0 group-hover:opacity-100"
                                    title="Remove from saved">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-bookmark text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No saved posts</h3>
                        <p class="text-gray-500 mb-4">Save interesting posts to find them later</p>
                        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-compass mr-2"></i>
                            Explore Feed
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Saved Jobs Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Saved Jobs</h2>
                <span class="text-gray-500 text-sm">
                    <?php
                    $saved_jobs = getSavedJobs($pdo, $user_id);
                    echo count($saved_jobs) . ' saved';
                    ?>
                </span>
            </div>

            <div class="space-y-4" id="savedJobsContainer">
                <?php if (count($saved_jobs) > 0): ?>
                    <?php foreach ($saved_jobs as $job): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <?php if ($job['company_logo']): ?>
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                <img src="assets/uploads/<?php echo $job['company_logo']; ?>"
                                                    alt="<?php echo htmlspecialchars($job['company_name']); ?>"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-building text-blue-600"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($job['title']); ?></h3>
                                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-sm">
                                        <span class="badge badge-<?php echo $job['type']; ?>">
                                            <?php echo ucfirst(str_replace('-', ' ', $job['type'])); ?>
                                        </span>
                                        <?php if ($job['location']): ?>
                                            <span class="text-gray-500 flex items-center">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                <?php echo htmlspecialchars($job['location']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($job['salary_range']): ?>
                                            <span class="text-gray-500 flex items-center">
                                                <i class="fas fa-dollar-sign mr-1"></i>
                                                <?php echo htmlspecialchars($job['salary_range']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                    <a href="jobs.php?view=<?php echo $job['id']; ?>"
                                        class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition"
                                        title="View Job">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="jobs.php" class="inline">
                                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                        <button type="submit" name="save_job"
                                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition"
                                            title="Remove from saved">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No saved jobs</h3>
                        <p class="text-gray-500 mb-4">Save interesting jobs to apply later</p>
                        <a href="jobs.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-search mr-2"></i>
                            Browse Jobs
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Delete Account Button -->
        <form action="../auth/delete-account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
            <button
                type="submit"
                name="delete_account"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                Delete Account
            </button>
        </form>


    </div>

    <!-- Include All Modals -->
    <?php include 'modals/cover-photo-modal.php'; ?>
    <?php include 'modals/profile-photo-modal.php'; ?>
    <?php include 'modals/edit-profile-modal.php'; ?>
    <?php include 'modals/edit-university-modal.php'; ?>
    <?php include 'modals/add-skill-modal.php'; ?>
    <?php include 'modals/add-experience-modal.php'; ?>
    <?php include 'modals/edit-experience-modal.php'; ?>

    <script>
        // Modal functions
        function openCoverPhotoModal() {
            document.getElementById('coverPhotoModal').classList.remove('hidden');
        }

        function openProfilePhotoModal() {
            document.getElementById('profilePhotoModal').classList.remove('hidden');
        }

        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
        }

        function openEditUniversityModal() {
            document.getElementById('editUniversityModal').classList.remove('hidden');
        }

        function openAddSkillModal() {
            document.getElementById('addSkillModal').classList.remove('hidden');
        }

        function openAddExperienceModal() {
            document.getElementById('addExperienceModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function deleteSkill(skillId) {
            if (confirm('Are you sure you want to delete this skill?')) {
                window.location.href = 'delete-skill.php?id=' + skillId;
            }
        }

        function deleteExperience(expId) {
            if (confirm('Are you sure you want to delete this experience?')) {
                window.location.href = 'delete-experience.php?id=' + expId;
            }
        }

        function deleteUniversity() {
            if (confirm('Are you sure you want to remove your education information?')) {
                window.location.href = 'delete-university.php';
            }
        }

        // AJAX function to load experience data for editing
        function loadExperienceData(expId) {
            fetch(`get-experience-data.php?id=${expId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit modal with data
                        document.getElementById('editPosition').value = data.position;
                        document.getElementById('editCompany').value = data.company;
                        document.getElementById('editStartDate').value = data.start_date;
                        document.getElementById('editEndDate').value = data.end_date;
                        document.getElementById('editDescription').value = data.description;
                        document.getElementById('editCurrentJob').checked = data.current_job;

                        // Update form action with the experience ID
                        const form = document.getElementById('editExperienceForm');
                        form.action = `edit-experience.php?id=${expId}`;

                        // Show modal and handle current job state
                        document.getElementById('editExperienceModal').classList.remove('hidden');
                        toggleEditEndDate();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading experience data');
                });
        }

        function toggleEditEndDate() {
            const currentJobCheckbox = document.getElementById('editCurrentJob');
            const endDateInput = document.getElementById('editEndDate');

            if (currentJobCheckbox.checked) {
                endDateInput.disabled = true;
                endDateInput.value = '';
                endDateInput.removeAttribute('required');
            } else {
                endDateInput.disabled = false;
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // Set current year as max for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const currentMonth = (new Date().getMonth() + 1).toString().padStart(2, '0');
            const currentDate = `${currentYear}-${currentMonth}`;

            // Set max dates for all date inputs
            const dateInputs = document.querySelectorAll('input[type="month"]');
            dateInputs.forEach(input => {
                input.max = currentDate;
            });
        });

        // Function to unsave post from saved section
        function unsavePost(postId) {
            if (!confirm('Are you sure you want to remove this post from saved?')) {
                return;
            }

            const postElement = document.querySelector(`.saved-post-item[data-post-id="${postId}"]`);

            // Show loading state
            postElement.style.opacity = '0.5';
            postElement.style.pointerEvents = 'none';

            // Create form data
            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('save-post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the post element with animation
                        postElement.style.transition = 'all 0.3s ease';
                        postElement.style.height = postElement.offsetHeight + 'px';
                        postElement.style.margin = '0';
                        postElement.style.padding = '0';
                        postElement.style.opacity = '0';

                        setTimeout(() => {
                            postElement.remove();

                            // Update saved count
                            updateSavedCount();

                            // Show success message
                            showNotification('Post removed from saved', 'success');

                            // If no posts left, show empty state
                            const savedContainer = document.getElementById('savedPostsContainer');
                            const savedPosts = savedContainer.querySelectorAll('.saved-post-item');

                            if (savedPosts.length === 0) {
                                showEmptySavedState();
                            }
                        }, 300);
                    } else {
                        // Reset element state
                        postElement.style.opacity = '1';
                        postElement.style.pointerEvents = 'auto';
                        showNotification('Failed to remove post', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset element state
                    postElement.style.opacity = '1';
                    postElement.style.pointerEvents = 'auto';
                    showNotification('Error removing post', 'error');
                });
        }

        // Function to update saved count
        function updateSavedCount() {
            const savedContainer = document.getElementById('savedPostsContainer');
            const savedPosts = savedContainer.querySelectorAll('.saved-post-item');
            const savedCountElement = document.getElementById('savedCount');

            savedCountElement.textContent = savedPosts.length + ' saved';
        }

        // Function to show empty state when no saved posts
        function showEmptySavedState() {
            const savedContainer = document.getElementById('savedPostsContainer');
            savedContainer.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-bookmark text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No saved posts</h3>
            <p class="text-gray-500 mb-4">Save interesting posts to find them later</p>
            <a href="index.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-compass mr-2"></i>
                Explore Feed
            </a>
        </div>
    `;
        }

        // Function to show notification
        function showNotification(message, type = 'info') {
            // Remove existing notification
            const existingNotification = document.querySelector('.custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Initialize notification styles
        const style = document.createElement('style');
        style.textContent = `
    .custom-notification {
        transform: translateX(100%);
        opacity: 0;
    }
`;
        document.head.appendChild(style);
    </script>
</body>

</html>