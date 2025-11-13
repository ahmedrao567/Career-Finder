<!-- Edit University Modal -->
<div id="editUniversityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900"><?php echo $profile['university'] ? 'Edit Education' : 'Add Education'; ?></h3>
                <button onclick="closeModal('editUniversityModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form action="update-university.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">University *</label>
                <input type="text" name="university" value="<?php echo htmlspecialchars($profile['university'] ?? ''); ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., Harvard University">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Degree</label>
                <select name="degree" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                    <option value="">Select Degree</option>
                    <option value="High School" <?php echo ($profile['degree'] ?? '') == 'High School' ? 'selected' : ''; ?>>High School</option>
                    <option value="Associate's Degree" <?php echo ($profile['degree'] ?? '') == 'Associate\'s Degree' ? 'selected' : ''; ?>>Associate's Degree</option>
                    <option value="Bachelor's Degree" <?php echo ($profile['degree'] ?? '') == 'Bachelor\'s Degree' ? 'selected' : ''; ?>>Bachelor's Degree</option>
                    <option value="Master's Degree" <?php echo ($profile['degree'] ?? '') == 'Master\'s Degree' ? 'selected' : ''; ?>>Master's Degree</option>
                    <option value="Doctorate" <?php echo ($profile['degree'] ?? '') == 'Doctorate' ? 'selected' : ''; ?>>Doctorate</option>
                    <option value="Professional Certificate" <?php echo ($profile['degree'] ?? '') == 'Professional Certificate' ? 'selected' : ''; ?>>Professional Certificate</option>
                    <option value="Other" <?php echo ($profile['degree'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Field of Study</label>
                <input type="text" name="field_of_study" value="<?php echo htmlspecialchars($profile['field_of_study'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., Computer Science">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
                <select name="graduation_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                    <option value="">Select Year</option>
                    <?php foreach ($graduation_years as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo ($profile['graduation_year'] ?? '') == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex justify-between space-x-3 pt-4">
                <?php if ($profile['university']): ?>
                    <button type="button" onclick="deleteUniversity()" 
                            class="px-4 py-2 text-red-600 hover:text-red-800 font-medium transition flex items-center space-x-2">
                        <i class="fas fa-trash"></i>
                        <span>Remove Education</span>
                    </button>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('editUniversityModal')" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                        <?php echo $profile['university'] ? 'Update Education' : 'Add Education'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>