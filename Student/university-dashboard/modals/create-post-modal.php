<!-- Create Post Modal -->
<div id="createPostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Create Post</h3>
                <button onclick="closeModal('createPostModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <form action="create-post.php" method="POST" enctype="multipart/form-data" class="p-6">
            <!-- Poster Info -->
            <div class="flex items-center space-x-3 mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                    <?php echo strtoupper(substr($_SESSION['university_name'], 0, 1)); ?>
                </div>
                <div>
                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['university_name']); ?></p>
                    <p class="text-gray-500 text-sm">
                        <i class="fas fa-graduation-cap mr-1"></i>University
                    </p>
                </div>
            </div>

            <!-- Post Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">What's happening at your university?</label>
                <textarea name="post_text" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus resize-none"
                          placeholder="Share academic updates, events, achievements, or announcements..."></textarea>
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Image (Optional)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                        </div>
                        <input id="postImageInput" type="file" name="post_image" class="hidden" accept="image/*">
                    </label>
                </div>
                
                <div id="imagePreview" class="mt-4 hidden">
                    <p class="text-sm text-gray-700 mb-2">Preview:</p>
                    <div class="relative inline-block">
                        <img id="previewImg" class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                        <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('createPostModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition flex items-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Post</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Image preview functionality
    document.getElementById('postImageInput').addEventListener('change', function(e) {
        const input = e.target;
        const previewContainer = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    });

    function removeImage() {
        const input = document.getElementById('postImageInput');
        const previewContainer = document.getElementById('imagePreview');
        
        input.value = '';
        previewContainer.classList.add('hidden');
    }

    // Auto-resize textarea
    document.querySelector('textarea[name="post_text"]').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
</script>