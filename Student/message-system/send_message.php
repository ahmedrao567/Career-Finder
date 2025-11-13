<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['session_token'])) {
    die(json_encode(['success' => false]));
}

$db = new Database();
$pdo = $db->getConnection();

// Verify session
$stmt = $pdo->prepare("SELECT user_id, user_type FROM sessions WHERE session_token = ? AND expires_at > NOW()");
$stmt->execute([$_COOKIE['session_token']]);
$session = $stmt->fetch();

if (!$session) {
    die(json_encode(['success' => false]));
}

$senderId = $session['user_id'];
$senderType = $session['user_type'];
$conversationId = $_POST['conversation_id'];
$receiverId = $_POST['receiver_id'];
$receiverType = $_POST['receiver_type'];
$message = sanitizeInput($_POST['message']);

// Insert message
$stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, sender_type, receiver_id, receiver_type, message) VALUES (?, ?, ?, ?, ?, ?)");
$messageSuccess = $stmt->execute([$conversationId, $senderId, $senderType, $receiverId, $receiverType, $message]);

// Update conversation last message
if ($messageSuccess) {
    $stmt = $pdo->prepare("UPDATE conversations SET last_message = ?, last_message_at = NOW() WHERE conversation_id = ?");
    $stmt->execute([$message, $conversationId]);
    
    // Get the inserted message to return
    $messageId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$messageId]);
    $sentMessage = $stmt->fetch();
}

echo json_encode([
    'success' => $messageSuccess,
    'message' => $sentMessage ?? null
]);
?>