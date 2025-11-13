<?php
include 'config.php';

$company_id = $_SESSION['company_id'];
$company = getCompanyProfile($pdo, $company_id);

// Handle post actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post'])) {
        $post_id = $_POST['post_id'];
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND company_id = ?");
        if ($stmt->execute([$post_id, $company_id])) {
            $_SESSION['success'] = "Post deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete post.";
        }
        header("Location: posts.php");
        exit();
    }
    
    if (isset($_POST['toggle_publish'])) {
        $post_id = $_POST['post_id'];
        $stmt = $pdo->prepare("UPDATE posts SET is_published = NOT is_published WHERE id = ? AND company_id = ?");
        if ($stmt->execute([$post_id, $company_id])) {
            $_SESSION['success'] = "Post status updated!";
        } else {
            $_SESSION['error'] = "Failed to update post status.";
        }
        header("Location: posts.php");
        exit();
    }
}

// Get company posts
function getCompanyPosts($pdo, $company_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM posts 
        WHERE company_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$posts = getCompanyPosts($pdo, $company_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .post-card {
            transition: all 0.3s ease;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manage Posts</h1>
                    <p class="text-gray-600 mt-2">Create and manage your company's posts</p>
                </div>
                <button onclick="openCreatePostModal()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Create New Post</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($posts); ?></p>
                        <p class="text-gray-600">Total Posts</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo count(array_filter($posts, function($post) { return $post['is_published']; })); ?>
                        </p>
                        <p class="text-gray-600">Published</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo count(array_filter($posts, function($post) { return !$post['is_published']; })); ?>
                        </p>
                        <p class="text-gray-600">Drafts</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $today = date('Y-m-d');
                            echo count(array_filter($posts, function($post) use ($today) { 
                                return date('Y-m-d', strtotime($post['created_at'])) === $today; 
                            })); 
                            ?>
                        </p>
                        <p class="text-gray-600">Today</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Your Posts</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search posts..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent w-64">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                        <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Posts List -->
            <div class="divide-y divide-gray-200">
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="p-6 post-card fade-in hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <?php if ($post['post_image']): ?>
                                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="../user-dashboard/assets/uploads/<?php echo $post['post_image']; ?>" 
                                                     alt="Post image" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                <?php echo htmlspecialchars($post['post_text'] ? substr($post['post_text'], 0, 100) . (strlen($post['post_text']) > 100 ? '...' : '') : 'No content'); ?>
                                            </h3>
                                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                                <span class="flex items-center space-x-1">
                                                    <i class="fas fa-calendar"></i>
                                                    <span><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    <!-- Toggle Publish -->
                                    <!-- <form method="POST" class="inline">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" name="toggle_publish" 
                                                class="p-2 rounded-lg transition <?php echo $post['is_published'] ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200'; ?>"
                                                title="<?php echo $post['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                            <i class="fas fa-<?php echo $post['is_published'] ? 'eye' : 'eye-slash'; ?>"></i>
                                        </button>
                                    </form> -->
                                    
                                    <!-- Edit Button -->
                                    <button onclick="openEditPostModal(<?php echo $post['id']; ?>)" 
                                            class="p-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                                            title="Edit Post">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" name="delete_post" 
                                                class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition"
                                                title="Delete Post">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No posts yet</h3>
                        <p class="text-gray-500 mb-4">Create your first post to engage with job seekers</p>
                        <button onclick="openCreatePostModal()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Create Your First Post</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="createPostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">Create New Post</h3>
                    <button onclick="closeModal('createPostModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <form action="create-post.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Content *</label>
                    <textarea name="post_text" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 input-focus resize-none"
                              placeholder="Share updates about your company, job opportunities, or industry insights..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Image</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                            </div>
                            <input id="postImage" type="file" name="post_image" class="hidden" accept="image/*" onchange="previewPostImage(this)">
                        </label>
                    </div>
                    <div id="postImagePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-700 mb-2">Preview:</p>
                        <img id="postPreview" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="isPublished" checked
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="isPublished" class="ml-2 block text-sm text-gray-700">
                        Publish immediately
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('createPostModal')"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        Create Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="editPostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
        <!-- Content will be loaded via AJAX -->
    </div>

    <script>
        // Modal Functions
        function openCreatePostModal() {
            document.getElementById('createPostModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function previewPostImage(input) {
            const preview = document.getElementById('postPreview');
            const previewContainer = document.getElementById('postImagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // AJAX function to load post data for editing
        function openEditPostModal(postId) {
            fetch('get-post-data.php?id=' + postId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editPostModal').innerHTML = html;
                    document.getElementById('editPostModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading post data');
                });
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[type="text"]');
            const statusFilter = document.querySelector('select');
            
            function filterPosts() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const posts = document.querySelectorAll('.post-card');
                
                posts.forEach(post => {
                    const text = post.textContent.toLowerCase();
                    const isPublished = post.querySelector('.fa-eye') !== null;
                    
                    const matchesSearch = text.includes(searchTerm);
                    const matchesStatus = !statusValue || 
                                         (statusValue === 'published' && isPublished) ||
                                         (statusValue === 'draft' && !isPublished);
                    
                    if (matchesSearch && matchesStatus) {
                        post.style.display = 'block';
                    } else {
                        post.style.display = 'none';
                    }
                });
            }
            
            searchInput.addEventListener('input', filterPosts);
            statusFilter.addEventListener('change', filterPosts);
        });
    </script>
</body>
</html>