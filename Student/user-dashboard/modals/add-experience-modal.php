<!-- Add Experience Modal -->
<div id="addExperienceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Add Experience</h3>
                <button onclick="closeModal('addExperienceModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form action="add-experience.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                <input type="text" name="position" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., Senior Software Engineer">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                <input type="text" name="company" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                       placeholder="e.g., Google Inc.">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input type="month" name="start_date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                           id="startDate">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="month" name="end_date" id="endDate"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus">
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="current_job" id="currentJob" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       onchange="toggleEndDate()">
                <label for="currentJob" class="ml-2 block text-sm text-gray-700">
                    I currently work here
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus"
                          placeholder="Describe your responsibilities and achievements..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('addExperienceModal')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                    Add Experience
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleEndDate() {
    const currentJobCheckbox = document.getElementById('currentJob');
    const endDateInput = document.getElementById('endDate');
    
    if (currentJobCheckbox.checked) {
        endDateInput.disabled = true;
        endDateInput.value = '';
        endDateInput.removeAttribute('required');
    } else {
        endDateInput.disabled = false;
    }
}

// Set current year as max for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    const currentMonth = (new Date().getMonth() + 1).toString().padStart(2, '0');
    const currentDate = `${currentYear}-${currentMonth}`;
    
    document.getElementById('startDate').max = currentDate;
    document.getElementById('endDate').max = currentDate;
    
    toggleEndDate();
});

// Validation for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const currentJob = document.getElementById('currentJob').checked;
        
        if (!currentJob && endDate && startDate > endDate) {
            e.preventDefault();
            alert('End date cannot be earlier than start date.');
            return false;
        }
        
        return true;
    });
});
</script>