<!-- Edit Job Modal -->
<div id="editJobModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">Edit Job</h3>
                <button onclick="closeModal('editJobModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="editJobForm" action="update-job.php" method="POST" class="p-6 space-y-6">
            <input type="hidden" id="editJobId" name="job_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                    <input type="text" id="editJobTitle" name="title" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., Senior Software Engineer">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type *</label>
                    <select id="editJobType" name="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                        <option value="">Select Type</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Contract">Contract</option>
                        <option value="Internship">Internship</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="editJobLocation" name="location"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., Remote, New York, NY">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                    <input type="text" id="editSalaryRange" name="salary_range"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., $80,000 - $120,000">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                <textarea id="editJobDescription" name="description" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                          placeholder="Describe the role, responsibilities, and what makes it exciting..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Requirements & Qualifications</label>
                <textarea id="editJobRequirements" name="requirements" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                          placeholder="List the required skills, experience, and qualifications..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                <input type="date" id="editApplicationDeadline" name="application_deadline"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                       min="<?php echo date('Y-m-d'); ?>">
                <p class="text-sm text-gray-500 mt-1">Leave empty for no deadline</p>
            </div>
            
            <div class="flex justify-between space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="deleteJob(document.getElementById('editJobId').value)" 
                        class="px-6 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium transition flex items-center space-x-2">
                    <i class="fas fa-trash"></i>
                    <span>Delete Job</span>
                </button>
                
                <div class="flex space-x-4">
                    <button type="button" onclick="closeModal('editJobModal')" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-save mr-2"></i>
                        Update Job
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function deleteJob(jobId) {
    if (confirm('Are you sure you want to delete this job? All applications will also be deleted.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'jobs.php';
        
        const jobIdInput = document.createElement('input');
        jobIdInput.type = 'hidden';
        jobIdInput.name = 'job_id';
        jobIdInput.value = jobId;
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete_job';
        
        form.appendChild(jobIdInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>