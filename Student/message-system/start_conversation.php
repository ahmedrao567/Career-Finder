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

$currentUserId = $session['user_id'];
$currentUserType = $session['user_type'];
$otherId = $_POST['other_id'];
$otherType = $_POST['other_type'];

// Generate conversation ID
$conversationId = md5($currentUserId . $currentUserType . $otherId . $otherType . time());

// Check if conversation already exists
$stmt = $pdo->prepare("SELECT conversation_id FROM conversations WHERE 
    ((participant1_id = ? AND participant1_type = ? AND participant2_id = ? AND participant2_type = ?) OR
     (participant1_id = ? AND participant1_type = ? AND participant2_id = ? AND participant2_type = ?))");
$stmt->execute([$currentUserId, $currentUserType, $otherId, $otherType, $otherId, $otherType, $currentUserId, $currentUserType]);
$existing = $stmt->fetch();

if ($existing) {
    echo json_encode(['success' => true, 'conversation_id' => $existing['conversation_id']]);
    exit;
}

// Create new conversation
$stmt = $pdo->prepare("INSERT INTO conversations (conversation_id, participant1_id, participant1_type, participant2_id, participant2_type) VALUES (?, ?, ?, ?, ?)");
$success = $stmt->execute([$conversationId, $currentUserId, $currentUserType, $otherId, $otherType]);

echo json_encode(['success' => $success, 'conversation_id' => $conversationId]);
?>