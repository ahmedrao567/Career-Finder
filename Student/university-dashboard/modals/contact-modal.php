<!-- Contact Information Modal -->
<div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Edit Contact Information</h3>
                <button onclick="closeModal('contactModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <form action="update-contact.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone" 
                       value="<?php echo !empty($contact_info['phone']) ? htmlspecialchars($contact_info['phone']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                       placeholder="(042) 123-4567">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                <input type="url" name="website"
                       value="<?php echo !empty($contact_info['website']) ? htmlspecialchars($contact_info['website']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                       placeholder="https://www.university.edu.pk">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                          placeholder="Full university address"><?php echo !empty($contact_info['address']) ? htmlspecialchars($contact_info['address']) : ''; ?></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('contactModal')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>