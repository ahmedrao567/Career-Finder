<?php
include 'config.php';

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $company_id = $_SESSION['company_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND company_id = ?");
    $stmt->execute([$post_id, $company_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($post) {
        ?>
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Post</h3>
                    <button onclick="closeModal('editPostModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <form action="update-post.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Content *</label>
                    <textarea name="post_text" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus resize-none"
                              placeholder="Share updates about your company..."><?php echo htmlspecialchars($post['post_text']); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <?php if ($post['post_image']): ?>
                        <div class="mb-4">
                            <img src="../user-dashboard/assets/uploads/<?php echo $post['post_image']; ?>" 
                                 alt="Current post image" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                        </div>
                    <?php endif; ?>
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Change Image</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                            </div>
                            <input type="file" name="post_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="editIsPublished" 
                           <?php echo $post['is_published'] ? 'checked' : ''; ?>
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="editIsPublished" class="ml-2 block text-sm text-gray-700">
                        Publish post
                    </label>
                </div>
                
                <div class="flex justify-between items-center pt-4">
                    <button type="button" onclick="deletePost(<?php echo $post['id']; ?>)" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                        <i class="fas fa-trash mr-2"></i>Delete Post
                    </button>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeModal('editPostModal')"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                            Update Post
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <script>
            function deletePost(postId) {
                if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'posts.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'post_id';
                    input.value = postId;
                    form.appendChild(input);
                    
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_post';
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
        <?php
    }
}
?>