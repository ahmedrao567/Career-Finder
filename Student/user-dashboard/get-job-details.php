<?php
include 'config.php';

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT j.*, c.company_name, c.logo as company_logo, c.about as company_about,
               EXISTS(SELECT 1 FROM job_applications WHERE user_id = ? AND job_id = j.id) as has_applied
        FROM jobs j
        LEFT JOIN companies c ON j.company_id = c.id
        WHERE j.id = ? AND j.is_active = 1
    ");
    $stmt->execute([$user_id, $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($job) {
        ?>
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <div class="flex items-center space-x-4 text-gray-600">
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-building"></i>
                                <span><?php echo htmlspecialchars($job['company_name']); ?></span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($job['location']); ?></span>
                            </span>
                            <span class="badge badge-<?php echo $job['type']; ?>">
                                <?php echo ucfirst(str_replace('-', ' ', $job['type'])); ?>
                            </span>
                        </div>
                    </div>
                    <button onclick="closeModal('jobDetailsModal')" class="text-gray-400 hover:text-gray-600 ml-4">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Job Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Salary</p>
                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($job['salary_range'] ?: 'Not specified'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Deadline</p>
                                <p class="font-semibold text-gray-900">
                                    <?php echo $job['application_deadline'] ? date('M j, Y', strtotime($job['application_deadline'])) : 'Not specified'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Posted</p>
                                <p class="font-semibold text-gray-900">
                                    <?php echo date('M j, Y', strtotime($job['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h4>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($job['description']); ?></p>
                    </div>
                </div>

                <!-- Requirements -->
                <?php if ($job['requirements']): ?>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Requirements</h4>
                    <div class="prose max-w-none">
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <?php
                            $requirements = explode("\n", $job['requirements']);
                            foreach ($requirements as $requirement):
                                if (trim($requirement)):
                            ?>
                                <li><?php echo htmlspecialchars(trim($requirement)); ?></li>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Company Info -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">About <?php echo htmlspecialchars($job['company_name']); ?></h4>
                    <div class="flex items-start space-x-4">
                        <?php if ($job['company_logo']): ?>
                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <img src="../user-dashboard/assets/uploads/<?php echo $job['company_logo']; ?>" 
                                     alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <p class="text-gray-700"><?php echo htmlspecialchars($job['company_about'] ?: 'No company description available.'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-6 border-t border-gray-200">
                    <button onclick="closeModal('jobDetailsModal')" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-medium transition">
                        Close
                    </button>
                    <?php if ($job['has_applied']): ?>
                        <button class="flex-1 bg-green-100 text-green-700 py-3 px-6 rounded-lg font-medium cursor-default">
                            <i class="fas fa-check mr-2"></i>Already Applied
                        </button>
                    <?php else: ?>
                        <button onclick="closeModal('jobDetailsModal'); openApplyModal(<?php echo $job['id']; ?>);" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition">
                            Apply Now
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>