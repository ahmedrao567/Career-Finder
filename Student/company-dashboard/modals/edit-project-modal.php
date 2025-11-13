<!-- Edit Project Modal -->
<div id="editProjectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">Edit Project</h3>
                <button onclick="closeModal('editProjectModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="editProjectForm" action="update-project.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <input type="hidden" id="editProjectId" name="project_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Title *</label>
                    <input type="text" id="editProjectTitle" name="project_title" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., E-commerce Platform Development">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Description</label>
                    <textarea id="editProjectDescription" name="project_description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                              placeholder="Describe the project, its features, and your role..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Link</label>
                    <input type="url" id="editProjectLink" name="project_link"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="https://github.com/your-project">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Date</label>
                    <input type="date" id="editProjectDate" name="project_date"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Technologies Used</label>
                    <input type="text" id="editTechnologies" name="technologies"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus transition"
                           placeholder="e.g., React, Node.js, MongoDB, AWS (comma separated)">
                    <p class="text-sm text-gray-500 mt-1">Separate technologies with commas</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Thumbnail</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Upload new thumbnail (optional)</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                            </div>
                            <input type="file" name="project_thumbnail" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Leave empty to keep current thumbnail</p>
                </div>
            </div>
            
            <div class="flex justify-between space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="deleteProject(document.getElementById('editProjectId').value)" 
                        class="px-6 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium transition flex items-center space-x-2">
                    <i class="fas fa-trash"></i>
                    <span>Delete Project</span>
                </button>
                
                <div class="flex space-x-4">
                    <button type="button" onclick="closeModal('editProjectModal')" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        Update Project
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>