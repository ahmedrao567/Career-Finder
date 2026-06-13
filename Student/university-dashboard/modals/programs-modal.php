<!-- modals/programs-modal.php -->
<div id="programsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 modal-content">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900" id="programModalTitle">Add New Program</h3>
            <button type="button" onclick="closeModal('programsModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="programForm" class="p-6 space-y-6">
            <input type="hidden" id="program_id" name="program_id" value="">
            
            <!-- Program Name -->
            <div>
                <label for="program_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Program Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="program_name" name="program_name" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition"
                    placeholder="e.g., Bachelor of Computer Science">
            </div>

            <!-- Program Category -->
            <div>
                <label for="program_category" class="block text-sm font-medium text-gray-700 mb-2">
                    Program Category <span class="text-red-500">*</span>
                </label>
                <select id="program_category" name="program_category" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition">
                    <option value="">Select Category</option>
                    <?php 
                    // Get categories - make sure this function exists
                    include '../config.php';
                    function getProgramCategories() {
                        return [
                            'Engineering & Technology',
                            'Computer Science & IT',
                            'Business & Management',
                            'Medical & Health Sciences',
                            'Natural Sciences',
                            'Social Sciences',
                            'Arts & Humanities',
                            'Law & Legal Studies',
                            'Education',
                            'Agriculture',
                            'Architecture',
                            'Pharmacy',
                            'Other'
                        ];
                    }
                    
                    foreach (getProgramCategories() as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Closing Merit -->
            <div>
                <label for="closing_merit" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Closing Merit <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" id="closing_merit" name="closing_merit" step="0.01" min="0" max="100" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition pr-12"
                        placeholder="e.g., 85.50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-500">%</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-1">Enter the percentage score for last year's closing merit</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('programsModal')"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" id="saveProgramBtn"
                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Save Program
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteProgramModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 modal-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Program</h3>
            <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this program? This action cannot be undone.</p>
            
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeModal('deleteProgramModal')"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteProgram"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Program
                </button>
            </div>
        </div>
    </div>
</div>