<?php
// messages.php
require_once 'config.php';

// Check authentication
if (!isset($_COOKIE['session_token'])) {
    header("Location: signin.php");
    exit();
}

$db = new Database();
$pdo = $db->getConnection();

// Verify session
$stmt = $pdo->prepare("SELECT s.*, 
                      CASE s.user_type 
                          WHEN 'user' THEN u.full_name
                          WHEN 'company' THEN c.company_name  
                          WHEN 'university' THEN univ.university_name
                      END as display_name
                      FROM sessions s
                      LEFT JOIN users u ON s.user_type = 'user' AND s.user_id = u.id
                      LEFT JOIN companies c ON s.user_type = 'company' AND s.user_id = c.id
                      LEFT JOIN universities univ ON s.user_type = 'university' AND s.user_id = univ.id
                      WHERE s.session_token = ? AND s.expires_at > NOW()");
$stmt->execute([$_COOKIE['session_token']]);
$session = $stmt->fetch();

if (!$session) {
    header("Location: signin.php");
    exit();
}

$currentUserId = $session['user_id'];
$currentUserType = $session['user_type'];
$currentUserName = $session['display_name'];

// Get conversations
$stmt = $pdo->prepare("
    SELECT c.*,
    CASE 
        WHEN c.participant1_id = ? AND c.participant1_type = ? THEN 
            CASE c.participant2_type
                WHEN 'user' THEN u.full_name
                WHEN 'company' THEN comp.company_name
                WHEN 'university' THEN uni.university_name
            END
        ELSE
            CASE c.participant1_type
                WHEN 'user' THEN u2.full_name
                WHEN 'company' THEN comp2.company_name
                WHEN 'university' THEN uni2.university_name
            END
    END as other_party_name,
    CASE 
        WHEN c.participant1_id = ? AND c.participant1_type = ? THEN c.participant2_type
        ELSE c.participant1_type
    END as other_party_type
    FROM conversations c
    LEFT JOIN users u ON c.participant2_type = 'user' AND c.participant2_id = u.id
    LEFT JOIN companies comp ON c.participant2_type = 'company' AND c.participant2_id = comp.id
    LEFT JOIN universities uni ON c.participant2_type = 'university' AND c.participant2_id = uni.id
    LEFT JOIN users u2 ON c.participant1_type = 'user' AND c.participant1_id = u2.id
    LEFT JOIN companies comp2 ON c.participant1_type = 'company' AND c.participant1_id = comp2.id
    LEFT JOIN universities uni2 ON c.participant1_type = 'university' AND c.participant1_id = uni2.id
    WHERE (c.participant1_id = ? AND c.participant1_type = ?) 
       OR (c.participant2_id = ? AND c.participant2_type = ?)
    ORDER BY c.last_message_at DESC
");
$stmt->execute([$currentUserId, $currentUserType, $currentUserId, $currentUserType, $currentUserId, $currentUserType, $currentUserId, $currentUserType]);
$conversations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - ConnectHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 h-screen">
    <!-- Main Container -->
    <div class="flex h-full max-w-7xl mx-auto">
        <!-- Sidebar -->
        <div class="w-1/4 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
                    <div class="flex items-center space-x-3">
                        <button id="searchBtn" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <i class="fas fa-search text-gray-600"></i>
                        </button>
                        <div class="relative">
                            <button class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    <?php echo strtoupper(substr($currentUserName, 0, 1)); ?>
                                </div>
                                <span class="font-medium text-gray-700"><?php echo $currentUserName; ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Modal -->
            <div id="searchModal" class="hidden absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-2xl p-6 w-96 max-w-full mx-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Search Users</h3>
                        <button id="closeSearch" class="p-2 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Search Tabs -->
                    <div class="flex border-b border-gray-200 mb-4">
                        <button class="tab-btn flex-1 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-blue-600 transition-colors" data-type="user">
                            <i class="fas fa-user mr-2"></i>Users
                        </button>
                        <button class="tab-btn flex-1 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-blue-600 transition-colors" data-type="company">
                            <i class="fas fa-building mr-2"></i>Companies
                        </button>
                        <button class="tab-btn flex-1 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-blue-600 transition-colors" data-type="university">
                            <i class="fas fa-graduation-cap mr-2"></i>Universities
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative mb-4">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Search by name..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" class="max-h-64 overflow-y-auto space-y-2">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No conversations yet</p>
                        <p class="text-sm text-gray-400 mt-1">Start a new conversation by searching above</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                            data-conversation-id="<?php echo $conv['conversation_id']; ?>"
                            data-other-id="<?php echo $conv['participant1_id'] == $currentUserId ? $conv['participant2_id'] : $conv['participant1_id']; ?>"
                            data-other-type="<?php echo $conv['participant1_id'] == $currentUserId ? $conv['participant2_type'] : $conv['participant1_type']; ?>">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    <?php echo strtoupper(substr($conv['other_party_name'], 0, 1)); ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-800 truncate"><?php echo $conv['other_party_name']; ?></h4>
                                        <span class="text-xs text-gray-500">
                                            <?php
                                            if (!empty($conv['last_message_at'])) {
                                                echo date('H:i', strtotime($conv['last_message_at']));
                                            } else {
                                                echo '--'; // or 'No message', or leave blank
                                            }
                                            ?>
                                        </span>

                                    </div>
                                    <p class="text-sm text-gray-600 truncate"><?php echo $conv['last_message']; ?></p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-<?php
                                                                echo $conv['other_party_type'] == 'user' ? 'user' : ($conv['other_party_type'] == 'company' ? 'building' : 'graduation-cap');
                                                                ?> mr-1"></i>
                                            <?php echo ucfirst($conv['other_party_type']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Chat Header -->
            <div id="chatHeader" class="bg-white border-b border-gray-200 p-6 hidden">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold" id="chatAvatar">
                        U
                    </div>
                    <div>
                        <h2 id="chatUserName" class="font-semibold text-gray-800">Select a conversation</h2>
                        <p id="chatUserType" class="text-sm text-gray-600"></p>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messagesContainer" class="flex-1 bg-gray-50 p-6 overflow-y-auto">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No conversation selected</h3>
                        <p class="text-gray-500">Choose a conversation from the sidebar or start a new one</p>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div id="messageInputContainer" class="bg-white border-t border-gray-200 p-6 hidden">
                <div class="flex space-x-4">
                    <input type="text" id="messageInput" placeholder="Type your message..."
                        class="flex-1 border border-gray-300 rounded-full px-6 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <button id="sendMessage" class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentConversationId = null;
        let currentOtherId = null;
        let currentOtherType = null;

        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', () => {
            document.getElementById('searchModal').classList.remove('hidden');
            document.getElementById('searchInput').focus();
        });

        document.getElementById('closeSearch').addEventListener('click', () => {
            document.getElementById('searchModal').classList.add('hidden');
        });

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-blue-500', 'text-blue-600');
                });
                this.classList.add('border-blue-500', 'text-blue-600');
                performSearch();
            });
        });

        // Search input handler
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        function performSearch() {
            const query = document.getElementById('searchInput').value.trim();
            const activeTab = document.querySelector('.tab-btn.border-blue-500')?.dataset.type || 'user';

            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '<p class="text-gray-500 text-center py-4">Type at least 2 characters to search</p>';
                return;
            }

            fetch('search.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `query=${encodeURIComponent(query)}&type=${activeTab}`
                })
                .then(response => response.json())
                .then(data => {
                    const resultsContainer = document.getElementById('searchResults');

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No results found</p>';
                        return;
                    }

                    resultsContainer.innerHTML = data.map(item => `
                    <div class="search-result p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                         data-id="${item.id}"
                         data-type="${activeTab}"
                         data-name="${item.name}">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                ${item.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">${item.name}</h4>
                                <p class="text-sm text-gray-600 capitalize">${activeTab}</p>
                            </div>
                        </div>
                    </div>
                `).join('');

                    // Add click listeners to search results
                    document.querySelectorAll('.search-result').forEach(result => {
                        result.addEventListener('click', function() {
                            startConversation(
                                this.dataset.id,
                                this.dataset.type,
                                this.dataset.name
                            );
                            document.getElementById('searchModal').classList.add('hidden');
                        });
                    });
                });
        }

        function startConversation(otherId, otherType, otherName) {
            fetch('start_conversation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `other_id=${otherId}&other_type=${otherType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadConversation(data.conversation_id, otherId, otherType, otherName);
                        location.reload(); // Refresh to show new conversation in sidebar
                    }
                });
        }

        function loadConversation(conversationId, otherId, otherType, otherName) {
            currentConversationId = conversationId;
            currentOtherId = otherId;
            currentOtherType = otherType;

            // Update UI
            document.getElementById('chatHeader').classList.remove('hidden');
            document.getElementById('messageInputContainer').classList.remove('hidden');
            document.getElementById('chatUserName').textContent = otherName;
            document.getElementById('chatUserType').textContent = `${otherType.charAt(0).toUpperCase() + otherType.slice(1)}`;
            document.getElementById('chatAvatar').textContent = otherName.charAt(0).toUpperCase();

            // Load messages
            loadMessages(conversationId);
        }

        function loadMessages(conversationId) {
            fetch(`get_messages.php?conversation_id=${conversationId}`)
                .then(response => response.json())
                .then(messages => {
                    const container = document.getElementById('messagesContainer');
                    container.innerHTML = '';

                    if (messages.length === 0) {
                        container.innerHTML = `
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <i class="fas fa-comment-dots text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">No messages yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Start the conversation!</p>
                                </div>
                            </div>
                        `;
                        return;
                    }

                    messages.forEach(msg => {
                        const isMe = msg.sender_id == <?php echo $currentUserId; ?> && msg.sender_type == '<?php echo $currentUserType; ?>';
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'} mb-4`;
                        messageDiv.innerHTML = `
                            <div class="max-w-xs lg:max-w-md ${isMe ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200'} rounded-2xl px-4 py-2 shadow-sm">
                                <p class="text-sm">${msg.message}</p>
                                <p class="text-xs ${isMe ? 'text-blue-200' : 'text-gray-500'} mt-1 text-right">
                                    ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </p>
                            </div>
                        `;
                        container.appendChild(messageDiv);
                    });

                    container.scrollTop = container.scrollHeight;
                });
        }

        // Send message
        document.getElementById('sendMessage').addEventListener('click', sendMessage);
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message || !currentConversationId) return;

            // Create temporary message for immediate display
            const tempMessage = {
                message: message,
                sender_id: <?php echo $currentUserId; ?>,
                sender_type: '<?php echo $currentUserType; ?>',
                created_at: new Date().toISOString()
            };

            // Immediately show the message
            addNewMessages([tempMessage]);

            // Clear input
            messageInput.value = '';

            // Send to server
            fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `conversation_id=${currentConversationId}&receiver_id=${currentOtherId}&receiver_type=${currentOtherType}&message=${encodeURIComponent(message)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update last message time
                        lastMessageTime = data.message ? data.message.created_at : new Date().toISOString().slice(0, 19).replace('T', ' ');
                    }
                });
        }

        // Load conversation when clicking on conversation item
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function() {
                loadConversation(
                    this.dataset.conversationId,
                    this.dataset.otherId,
                    this.dataset.otherType,
                    this.querySelector('h4').textContent
                );
            });
        });

        // Set first tab as active initially
        document.querySelector('.tab-btn').click();

        // Real-time message polling
        let messagePollInterval;
        let lastMessageTime = null;

        function startMessagePolling() {
            if (messagePollInterval) {
                clearInterval(messagePollInterval);
            }

            messagePollInterval = setInterval(() => {
                if (currentConversationId) {
                    checkNewMessages();
                }
            }, 2000); // Check every 2 seconds
        }

        function checkNewMessages() {
            if (!currentConversationId) return;

            fetch(`check_new_messages.php?conversation_id=${currentConversationId}&last_time=${lastMessageTime}`)
                .then(response => response.json())
                .then(data => {
                    if (data.new_messages && data.new_messages.length > 0) {
                        // Add new messages to the chat
                        addNewMessages(data.new_messages);
                        // Update last message time
                        if (data.new_messages.length > 0) {
                            lastMessageTime = data.new_messages[data.new_messages.length - 1].created_at;
                        }
                    }
                })
                .catch(error => console.error('Error checking new messages:', error));
        }

        function addNewMessages(newMessages) {
            const container = document.getElementById('messagesContainer');

            newMessages.forEach(msg => {
                const isMe = msg.sender_id == <?php echo $currentUserId; ?> && msg.sender_type == '<?php echo $currentUserType; ?>';
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'} mb-4 message-item`;
                messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md ${isMe ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200'} rounded-2xl px-4 py-2 shadow-sm">
                <p class="text-sm">${msg.message}</p>
                <p class="text-xs ${isMe ? 'text-blue-200' : 'text-gray-500'} mt-1 text-right">
                    ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                </p>
            </div>
        `;
                container.appendChild(messageDiv);
            });

            // Scroll to bottom
            container.scrollTop = container.scrollHeight;

            // Update conversation list if needed
            updateConversationList();
        }

        function updateConversationList() {
            // Refresh conversations list to show latest message
            fetch('get_conversations.php')
                .then(response => response.json())
                .then(conversations => {
                    // Update the conversations sidebar
                    updateConversationsSidebar(conversations);
                });
        }

        function updateConversationsSidebar(conversations) {
            const sidebar = document.querySelector('.conversation-item');
            if (!sidebar) return;

            // This is a simplified update - you might want to implement a more sophisticated update
            conversations.forEach(conv => {
                const convElement = document.querySelector(`[data-conversation-id="${conv.conversation_id}"]`);
                if (convElement) {
                    const lastMessageElement = convElement.querySelector('p.text-sm');
                    if (lastMessageElement && lastMessageElement.textContent !== conv.last_message) {
                        lastMessageElement.textContent = conv.last_message;

                        // Update timestamp
                        const timeElement = convElement.querySelector('span.text-xs');
                        if (timeElement) {
                            timeElement.textContent = new Date(conv.last_message_at).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }

                        // Move to top if this is the active conversation
                        if (conv.conversation_id === currentConversationId) {
                            convElement.parentNode.prepend(convElement);
                        }
                    }
                }
            });
        }

        // Update the loadConversation function to start polling
        function loadConversation(conversationId, otherId, otherType, otherName) {
            currentConversationId = conversationId;
            currentOtherId = otherId;
            currentOtherType = otherType;

            // Update UI
            document.getElementById('chatHeader').classList.remove('hidden');
            document.getElementById('messageInputContainer').classList.remove('hidden');
            document.getElementById('chatUserName').textContent = otherName;
            document.getElementById('chatUserType').textContent = `${otherType.charAt(0).toUpperCase() + otherType.slice(1)}`;
            document.getElementById('chatAvatar').textContent = otherName.charAt(0).toUpperCase();

            // Reset last message time
            lastMessageTime = null;

            // Load messages and start polling
            loadMessages(conversationId);
            startMessagePolling();
        }

        // Update the sendMessage function to update lastMessageTime
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message || !currentConversationId) return;

            fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `conversation_id=${currentConversationId}&receiver_id=${currentOtherId}&receiver_type=${currentOtherType}&message=${encodeURIComponent(message)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        // Instead of reloading, just update the last message time and let polling handle it
                        lastMessageTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    }
                });
        }

        // Start polling when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startMessagePolling();
        });

        // Stop polling when leaving the page
        window.addEventListener('beforeunload', function() {
            if (messagePollInterval) {
                clearInterval(messagePollInterval);
            }
        });
    </script>
</body>

</html>