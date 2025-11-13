<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Edit Profile</h3>
                <button onclick="closeModal('editProfileModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form action="update-profile.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                <input type="text" name="designation" value="<?php echo htmlspecialchars($profile['designation'] ?? ''); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., Software Engineer">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., San Francisco, CA">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                <input type="url" name="website" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="https://">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">About</label>
                <textarea name="about" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                          placeholder="Tell us about yourself..."><?php echo htmlspecialchars($profile['about'] ?? ''); ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('editProfileModal')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>