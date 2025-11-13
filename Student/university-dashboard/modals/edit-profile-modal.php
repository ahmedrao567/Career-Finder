<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Edit University Profile</h3>
                <button onclick="closeModal('editProfileModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <form action="update-profile.php" method="POST" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo ($university['category'] == $cat) ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Established Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Established Year</label>
                    <input type="number" name="established_year" 
                           value="<?php echo htmlspecialchars($university['established_year']); ?>"
                           min="1900" max="<?php echo date('Y'); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                           placeholder="<?php echo date('Y'); ?>">
                </div>

                <!-- Sector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sector</label>
                    <select name="sector"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus">
                        <option value="">Select Sector</option>
                        <option value="Public" <?php echo ($university['sector'] == 'Public') ? 'selected' : ''; ?>>Public</option>
                        <option value="Private" <?php echo ($university['sector'] == 'Private') ? 'selected' : ''; ?>>Private</option>
                    </select>
                </div>

                <!-- Chartered By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chartered By</label>
                    <input type="text" name="chartered_by"
                           value="<?php echo htmlspecialchars($university['chartered_by']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                           placeholder="e.g., HEC Pakistan">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city"
                           value="<?php echo htmlspecialchars($university['city']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                           placeholder="e.g., Lahore">
                </div>

                <!-- Province -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                    <select name="province"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus">
                        <option value="">Select Province</option>
                        <?php foreach ($provinces as $prov): ?>
                            <option value="<?php echo $prov; ?>" <?php echo ($university['province'] == $prov) ? 'selected' : ''; ?>>
                                <?php echo $prov; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Recognition -->
            <div class="flex items-center">
                <input type="checkbox" name="is_recognized" id="is_recognized"
                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                       <?php echo $university['is_recognized'] ? 'checked' : ''; ?>>
                <label for="is_recognized" class="ml-2 block text-sm text-gray-700">
                    This university is recognized by relevant authorities
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <button type="button" onclick="closeModal('editProfileModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>