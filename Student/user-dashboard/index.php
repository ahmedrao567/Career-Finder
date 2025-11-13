<?php include 'config.php'; 

// Handle post saving
if (isset($_POST['save_post'])) {
    $post_id = $_POST['post_id'];
    if (toggleSavePost($pdo, $_SESSION['user_id'], $post_id)) {
        $_SESSION['success'] = "Post updated!";
    } else {
        $_SESSION['error'] = "Failed to update post.";
    }
    header("Location: index.php");
    exit();
}

// Get posts with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5; // Posts per page
$offset = ($page - 1) * $limit;
$posts = getPosts($pdo, $limit, $offset);

// Check if there are more posts
$next_page_posts = getPosts($pdo, $limit, $page * $limit);
$has_more = count($next_page_posts) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - ProfessionalHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-2xl mx-auto py-8 px-4">
        <!-- Create Post (Disabled for now) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                </div>
                <div class="flex-1">
                    <input type="text" 
                           placeholder="Share an update..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 cursor-not-allowed"
                           disabled>
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Only companies and universities can create posts
                    </p>
                </div>
            </div>
        </div>

        <!-- Posts Feed -->
        <div id="postsContainer">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 post-card" data-post-id="<?php echo $post['id']; ?>">
                        <!-- Post Header -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                                    <?php if ($post['poster_avatar']): ?>
                                        <img src="assets/uploads/<?php echo $post['poster_avatar']; ?>" 
                                             alt="<?php echo htmlspecialchars($post['poster_name']); ?>" 
                                             class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($post['poster_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="#" class="font-semibold text-gray-900 hover:text-blue-600 transition hover:underline">
                                        <?php echo htmlspecialchars($post['poster_name']); ?>
                                    </a>
                                    <p class="text-gray-500 text-sm">
                                        <?php 
                                        $post_time = strtotime($post['created_at']);
                                        $time_diff = time() - $post_time;
                                        
                                        if ($time_diff < 60) {
                                            echo 'Just now';
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
                                        • <span class="capitalize"><?php echo $post['poster_type']; ?></span>
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
                                         class="w-full h-auto max-h-96 object-cover lazy-load"
                                         data-src="assets/uploads/<?php echo $post['post_image']; ?>">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Actions -->
                        <div class="px-4 py-3 border-t border-gray-100">
                            <div class="flex items-center justify-between text-gray-600">
                                <div class="flex items-center space-x-1 text-sm">
                                    <span class="text-blue-600">
                                        <i class="fas fa-bookmark"></i>
                                    </span>
                                    <span><?php echo $post['save_count']; ?> saves</span>
                                </div>
                                
                                <form method="POST" class="inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="save_post" 
                                            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition <?php echo $post['is_saved'] ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-100 text-gray-600'; ?>">
                                        <i class="fas fa-bookmark <?php echo $post['is_saved'] ? 'text-blue-600' : 'text-gray-400'; ?>"></i>
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No posts yet</h3>
                    <p class="text-gray-500">When companies and universities post updates, they'll appear here.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More Button -->
        <?php if ($has_more): ?>
            <div class="text-center mt-8">
                <button id="loadMoreBtn" 
                        class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-spinner fa-spin hidden mr-2"></i>
                    Load More Posts
                </button>
            </div>
        <?php endif; ?>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading more posts...</p>
        </div>
    </div>

    <script>
        let currentPage = <?php echo $page; ?>;
        let isLoading = false;

        // Lazy loading for images
        const lazyLoadImages = () => {
            const lazyImages = document.querySelectorAll('.lazy-load');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy-load');
                        imageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        };

        // Load more posts
        document.getElementById('loadMoreBtn')?.addEventListener('click', function() {
            if (isLoading) return;
            
            isLoading = true;
            const loadBtn = this;
            const spinner = loadBtn.querySelector('.fa-spinner');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            loadBtn.disabled = true;
            spinner.classList.remove('hidden');
            loadingSpinner.classList.remove('hidden');
            
            currentPage++;
            
            fetch(`load-posts.php?page=${currentPage}`)
                .then(response => response.text())
                .then(html => {
                    if (html.trim()) {
                        document.getElementById('postsContainer').insertAdjacentHTML('beforeend', html);
                        lazyLoadImages(); // Initialize lazy loading for new images
                        
                        // Check if there are more posts
                        return fetch(`load-posts.php?page=${currentPage + 1}&check_only=1`);
                    } else {
                        loadBtn.style.display = 'none';
                        throw new Error('No more posts');
                    }
                })
                .then(response => response.text())
                .then(html => {
                    if (!html.trim()) {
                        loadBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadBtn.style.display = 'none';
                })
                .finally(() => {
                    isLoading = false;
                    spinner.classList.add('hidden');
                    loadingSpinner.classList.add('hidden');
                    loadBtn.disabled = false;
                });
        });

        // Initialize lazy loading
        document.addEventListener('DOMContentLoaded', function() {
            lazyLoadImages();
            
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

        // Handle save post with AJAX
        document.addEventListener('submit', function(e) {
            if (e.target.name === 'save_post') {
                e.preventDefault();
                
                const form = e.target;
                const button = form.querySelector('button');
                const icon = button.querySelector('i');
                const text = button.querySelector('span');
                const postCard = form.closest('.post-card');
                
                const formData = new FormData(form);
                
                fetch('save-post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update button appearance
                        if (data.saved) {
                            button.classList.add('bg-blue-50', 'text-blue-600');
                            button.classList.remove('hover:bg-gray-100', 'text-gray-600');
                            icon.classList.add('text-blue-600');
                            icon.classList.remove('text-gray-400');
                            text.textContent = 'Saved';
                        } else {
                            button.classList.remove('bg-blue-50', 'text-blue-600');
                            button.classList.add('hover:bg-gray-100', 'text-gray-600');
                            icon.classList.remove('text-blue-600');
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
                });
            }
        });
    </script>
</body>
</html>