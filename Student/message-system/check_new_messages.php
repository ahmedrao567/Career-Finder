<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['session_token'])) {
    die(json_encode(['new_messages' => []]));
}

$db = new Database();
$pdo = $db->getConnection();

$conversationId = $_GET['conversation_id'];
$lastTime = $_GET['last_time'] ?? null;

if ($lastTime) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? AND created_at > ? ORDER BY created_at ASC");
    $stmt->execute([$conversationId, $lastTime]);
} else {
    // If no lastTime provided, get messages from last 5 minutes
    $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? AND created_at > ? ORDER BY created_at ASC");
    $stmt->execute([$conversationId, $fiveMinutesAgo]);
}

$newMessages = $stmt->fetchAll();

echo json_encode(['new_messages' => $newMessages]);
?>