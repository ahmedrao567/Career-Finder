<?php
include 'config.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$check_only = isset($_GET['check_only']);
$limit = 5;
$offset = ($page - 1) * $limit;

if ($check_only) {
    $posts = getPosts($pdo, 1, $offset);
    echo count($posts) > 0 ? '1' : '';
    exit();
}

$posts = getPosts($pdo, $limit, $offset);

if (count($posts) === 0) {
    exit(); // No more posts
}

foreach ($posts as $post): ?>
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
                    <img src="assets/uploads/placeholder.jpg" 
                         data-src="assets/uploads/<?php echo $post['post_image']; ?>" 
                         alt="Post image" 
                         class="w-full h-auto max-h-96 object-cover lazy-load">
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