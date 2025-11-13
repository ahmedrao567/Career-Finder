<?php
include 'config.php';

if (isset($_GET['id'])) {
    $application_id = $_GET['id'];
    $company_id = $_SESSION['company_id'];
    
    $stmt = $pdo->prepare("
        SELECT 
            ja.*,
            j.title as job_title,
            j.description as job_description,
            j.requirements as job_requirements,
            j.location as job_location,
            j.type as job_type,
            j.salary_range as job_salary,
            u.full_name as applicant_name,
            u.email as applicant_email,
            u.username as applicant_username,
            up.designation as applicant_designation,
            up.about as applicant_about,
            up.location as applicant_location,
            up.profile_photo as applicant_photo,
            GROUP_CONCAT(DISTINCT us.skill_name) as applicant_skills,
            GROUP_CONCAT(DISTINCT ue.position) as applicant_experience
        FROM job_applications ja
        INNER JOIN jobs j ON ja.job_id = j.id
        INNER JOIN users u ON ja.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id
        LEFT JOIN user_skills us ON u.id = us.user_id
        LEFT JOIN user_experiences ue ON u.id = ue.user_id
        WHERE ja.id = ? AND j.company_id = ?
        GROUP BY ja.id
    ");
    $stmt->execute([$application_id, $company_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($application) {
        ?>
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Application Details</h3>
                        <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($application['applicant_name']); ?> - <?php echo htmlspecialchars($application['job_title']); ?></p>
                    </div>
                    <button onclick="closeModal('applicationModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Applicant Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Applicant Profile -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <?php if (!empty($application['applicant_photo'])): ?>
                                <img src="../user-dashboard/assets/uploads/<?php echo $application['applicant_photo']; ?>" 
                                     alt="<?php echo htmlspecialchars($application['applicant_name']); ?>"
                                     class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                            <?php else: ?>
                                <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
                                    <?php echo strtoupper(substr($application['applicant_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($application['applicant_name']); ?></h4>
                            <?php if (!empty($application['applicant_designation'])): ?>
                                <p class="text-gray-600"><?php echo htmlspecialchars($application['applicant_designation']); ?></p>
                            <?php endif; ?>
                            <p class="text-gray-500 text-sm mt-2"><?php echo htmlspecialchars($application['applicant_email']); ?></p>
                            
                            <?php if (!empty($application['applicant_location'])): ?>
                                <p class="text-gray-500 text-sm flex items-center justify-center mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?php echo htmlspecialchars($application['applicant_location']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Application Details -->
                    <div class="md:col-span-2 space-y-4">
                        <!-- Job Information -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Job Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Position</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($application['job_title']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Location</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($application['job_location']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Type</p>
                                    <p class="font-medium"><?php echo ucfirst(str_replace('-', ' ', $application['job_type'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Salary</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($application['job_salary'] ?: 'Not specified'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Application Status</h4>
                            <div class="flex items-center space-x-4">
                                <span class="status-badge status-<?php echo $application['status']; ?> text-base">
                                    <?php echo ucfirst($application['status']); ?>
                                </span>
                                <span class="text-gray-600">
                                    Applied: <?php echo date('M j, Y g:i A', strtotime($application['applied_at'])); ?>
                                </span>
                            </div>
                        </div>

                        <!-- CV Download -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Curriculum Vitae</h4>
                            <?php if (!empty($application['cv_file'])): ?>
                                <?php
                                $cv_path = "../user-dashboard/assets/uploads/cvs/" . $application['cv_file'];
                                $cv_exists = file_exists($cv_path);
                                ?>
                                <a href="<?php echo $cv_exists ? $cv_path : '#'; ?>" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium <?php echo !$cv_exists ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                   <?php echo !$cv_exists ? 'onclick="return false;"' : ''; ?>>
                                    <i class="fas fa-download mr-2"></i>
                                    <?php echo $cv_exists ? 'Download CV' : 'CV Not Found'; ?>
                                </a>
                                <?php if (!$cv_exists): ?>
                                    <p class="text-red-600 text-sm mt-1">The CV file appears to be missing from the server.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No CV uploaded with this application.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter -->
                <?php if (!empty($application['cover_letter'])): ?>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Cover Letter</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Applicant Skills -->
                <?php if (!empty($application['applicant_skills'])): ?>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Skills</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $skills = explode(',', $application['applicant_skills']);
                        foreach ($skills as $skill):
                            if (trim($skill)):
                        ?>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                <?php echo htmlspecialchars(trim($skill)); ?>
                            </span>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Status Update Form -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Update Application Status</h4>
                    <form method="POST" action="job-applications.php" class="flex items-center space-x-4">
                        <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="reviewed" <?php echo $application['status'] === 'reviewed' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                            <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                        <button type="submit" name="update_status" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
?>