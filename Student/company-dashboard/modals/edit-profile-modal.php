<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">Edit Company Profile</h3>
                <button onclick="closeModal('editProfileModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="update-profile.php" method="POST" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($company['company_name']); ?>" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industry *</label>
                    <select name="industry" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                        <option value="">Select Industry</option>
                        <?php foreach ($industries as $industry): ?>
                            <option value="<?php echo $industry; ?>" <?php echo ($company['industry'] ?? '') == $industry ? 'selected' : ''; ?>>
                                <?php echo $industry; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($company['category'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., SaaS, FinTech, E-commerce">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., San Francisco, CA">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="https://">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio Link</label>
                    <input type="url" name="portfolio_link" value="<?php echo htmlspecialchars($company['portfolio_link'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="https://">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="+1 (555) 123-4567">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Size</label>
                    <select name="company_size" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                        <option value="">Select Size</option>
                        <option value="1-10" <?php echo ($company['company_size'] ?? '') == '1-10' ? 'selected' : ''; ?>>1-10 employees</option>
                        <option value="11-50" <?php echo ($company['company_size'] ?? '') == '11-50' ? 'selected' : ''; ?>>11-50 employees</option>
                        <option value="51-200" <?php echo ($company['company_size'] ?? '') == '51-200' ? 'selected' : ''; ?>>51-200 employees</option>
                        <option value="201-500" <?php echo ($company['company_size'] ?? '') == '201-500' ? 'selected' : ''; ?>>201-500 employees</option>
                        <option value="501-1000" <?php echo ($company['company_size'] ?? '') == '501-1000' ? 'selected' : ''; ?>>501-1000 employees</option>
                        <option value="1000+" <?php echo ($company['company_size'] ?? '') == '1000+' ? 'selected' : ''; ?>>1000+ employees</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                <input type="text" name="specialization" value="<?php echo htmlspecialchars($company['specialization'] ?? ''); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                       placeholder="e.g., Web Development, Mobile Apps, AI/ML (comma separated)">
                <p class="text-sm text-gray-500 mt-1">Separate multiple specializations with commas</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">About Company *</label>
                <textarea name="about" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                          placeholder="Tell your company's story, mission, and values..."><?php echo htmlspecialchars($company['about'] ?? ''); ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeModal('editProfileModal')" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>