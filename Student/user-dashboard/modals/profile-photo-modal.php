<!-- Profile Photo Modal -->
<div id="profilePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Update Profile Photo</h3>
                <button onclick="closeModal('profilePhotoModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form action="profile-photo-upload.php" method="POST" enctype="multipart/form-data" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Choose Profile Photo</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                        </div>
                        <input id="profilePhotoInput" type="file" name="profile_photo" class="hidden" accept="image/*" onchange="previewProfilePhoto(this)" required>
                    </label>
                </div>
            </div>
            
            <div id="profilePhotoPreview" class="mb-4 hidden">
                <p class="text-sm text-gray-700 mb-2">Preview:</p>
                <div class="flex justify-center">
                    <img id="profilePreview" class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-lg">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('profilePhotoModal')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                    Upload Photo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewProfilePhoto(input) {
    const preview = document.getElementById('profilePreview');
    const previewContainer = document.getElementById('profilePhotoPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>