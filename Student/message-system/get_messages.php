<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['session_token'])) {
    die(json_encode([]));
}

$db = new Database();
$pdo = $db->getConnection();

$conversationId = $_GET['conversation_id'];

$stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
$stmt->execute([$conversationId]);
$messages = $stmt->fetchAll();

echo json_encode($messages);
?>