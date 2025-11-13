<?php 
include 'config.php';

// Get university data
$university_id = $_SESSION['university_id'];
$university_name = $_SESSION['university_name'];

// Handle post saving
if (isset($_POST['save_post'])) {
    $post_id = $_POST['post_id'];
    if (toggleSavePostUniversity($pdo, $university_id, $post_id)) {
        $_SESSION['success'] = "Post updated!";
    } else {
        $_SESSION['error'] = "Failed to update post.";
    }
    header("Location: index.php");
    exit();
}

// Get posts with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$posts = getUniversityFeedPosts($pdo, $limit, $offset);

// Check if there are more posts
$next_page_posts = getUniversityFeedPosts($pdo, $limit, $page * $limit);
$has_more = count($next_page_posts) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Feed - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
        }
        .post-card {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s ease forwards;
        }
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Display Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg text-white p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome to Your Feed</h1>
                    <p class="text-purple-100">Stay updated with posts from companies and other universities</p>
                </div>
                <div class="text-4xl">
                    <i class="fas fa-newspaper"></i>
                </div>
            </div>
        </div>

        <!-- Create Post Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                    <?php echo strtoupper(substr($university_name, 0, 1)); ?>
                </div>
                <div class="flex-1">
                    <input type="text"
                           placeholder="Share an academic update, event, or announcement..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus bg-gray-50 cursor-pointer"
                           onclick="openCreatePostModal()">
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click to create a post and share with the community
                    </p>
                </div>
            </div>
        </div>

        <!-- Posts Feed -->
        <div id="postsContainer">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $index => $post): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 post-card" data-post-id="<?php echo $post['id']; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <!-- Post Header -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0
                                    <?php echo $post['poster_type'] == 'company' ? 'bg-gradient-to-r from-green-400 to-blue-500' : 'bg-gradient-to-r from-purple-400 to-pink-500'; ?>">
                                    <?php if ($post['poster_avatar']): ?>
                                        <img src="assets/uploads/<?php echo $post['poster_avatar']; ?>" 
                                             alt="<?php echo htmlspecialchars($post['poster_name']); ?>"
                                             class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($post['poster_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($post['poster_name']); ?>
                                    </div>
                                    <p class="text-gray-500 text-sm">
                                        <?php
                                        $post_time = strtotime($post['created_at']);
                                        $time_diff = time() - $post_time;
                                        
                                        if ($time_diff < 60) {
                                            echo 'just now';
                                        } elseif ($time_diff < 3600) {
                                            echo floor($time_diff / 60) . 'm ago';
                                        } elseif ($time_diff < 86400) {
                                            echo floor($time_diff / 3600) . 'h ago';
                                        } elseif ($time_diff < 604800) {
                                            echo floor($time_diff / 86400) . 'd ago';
                                        } else {
                                            echo date('M j, Y', $post_time);
                                        }
                                        ?>
                                        • 
                                        <span class="capitalize">
                                            <?php if ($post['poster_type'] == 'company'): ?>
                                                <i class="fas fa-building mr-1"></i>Company
                                            <?php else: ?>
                                                <i class="fas fa-graduation-cap mr-1"></i>University
                                            <?php endif; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <div class="p-4">
                            <p class="text-gray-800 whitespace-pre-line leading-relaxed">
                                <?php echo nl2br(htmlspecialchars($post['post_text'])); ?>
                            </p>

                            <?php if ($post['post_image']): ?>
                                <div class="mt-4 rounded-lg overflow-hidden border border-gray-200">
                                    <img src="assets/uploads/<?php echo $post['post_image']; ?>"
                                         alt="Post image"
                                         class="w-full h-auto max-h-96 object-cover">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Actions -->
                        <div class="px-4 py-3 border-t border-gray-100">
                            <div class="flex items-center justify-between text-gray-600">
                                <div class="flex items-center space-x-1 text-sm">
                                    <span class="text-purple-600">
                                        <i class="fas fa-bookmark"></i>
                                    </span>
                                    <span><?php echo $post['save_count']; ?> saves</span>
                                </div>
                                
                                <form method="POST" class="inline save-post-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="save_post"
                                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition <?php echo $post['is_saved'] ? 'bg-purple-50 text-purple-600' : 'hover:bg-gray-100 text-gray-600'; ?>">
                                        <i class="fas fa-bookmark <?php echo $post['is_saved'] ? 'text-purple-600' : 'text-gray-400'; ?>"></i>
                                        <span class="text-sm font-medium">
                                            <?php echo $post['is_saved'] ? 'Saved' : 'Save'; ?>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <i class="fas fa-newspaper text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-2">No posts yet</h3>
                    <p class="text-gray-500 mb-6">When companies and universities post updates, they will appear here.</p>
                    <button onclick="openCreatePostModal()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition inline-flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Create First Post</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More Button -->
        <?php if ($has_more): ?>
            <div class="text-center mt-8">
                <button id="loadMoreBtn"
                        class="bg-white border border-purple-600 text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-lg transition inline-flex items-center space-x-2">
                    <i class="fas fa-spinner fa-spin hidden"></i>
                    <span>Load More Posts</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Create Post Modal -->
    <?php include 'modals/create-post-modal.php'; ?>

    <script>
        // Modal function for create post
        function openCreatePostModal() {
            document.getElementById('createPostModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Handle save post with AJAX
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('save-post-form')) {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button');
                const icon = button.querySelector('i');
                const text = button.querySelector('span');
                const postCard = form.closest('.post-card');

                const formData = new FormData(form);

                // Add loading state
                button.disabled = true;
                const originalText = text.textContent;
                text.textContent = '...';

                fetch('save-post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update button appearance
                        if (data.saved) {
                            button.classList.add('bg-purple-50', 'text-purple-600');
                            button.classList.remove('hover:bg-gray-100', 'text-gray-600');
                            icon.classList.add('text-purple-600');
                            icon.classList.remove('text-gray-400');
                            text.textContent = 'Saved';
                        } else {
                            button.classList.remove('bg-purple-50', 'text-purple-600');
                            button.classList.add('hover:bg-gray-100', 'text-gray-600');
                            icon.classList.remove('text-purple-600');
                            icon.classList.add('text-gray-400');
                            text.textContent = 'Save';
                        }

                        // Update save count
                        const saveCountElement = postCard.querySelector('.fa-bookmark').closest('div').querySelector('span:last-child');
                        const currentCount = parseInt(saveCountElement.textContent) || 0;
                        saveCountElement.textContent = (currentCount + (data.saved ? 1 : -1)) + ' saves';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    text.textContent = originalText;
                })
                .finally(() => {
                    button.disabled = false;
                });
            }
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.add('hidden');
            }
        });

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth appearance animation for posts
            const posts = document.querySelectorAll('.post-card');
            posts.forEach((post, index) => {
                post.style.opacity = '0';
                post.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    post.style.transition = 'all 0.5s ease';
                    post.style.opacity = '1';
                    post.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>