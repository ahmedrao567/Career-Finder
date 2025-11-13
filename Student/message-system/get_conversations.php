<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['session_token'])) {
    die(json_encode([]));
}

$db = new Database();
$pdo = $db->getConnection();

// Verify session
$stmt = $pdo->prepare("SELECT user_id, user_type FROM sessions WHERE session_token = ? AND expires_at > NOW()");
$stmt->execute([$_COOKIE['session_token']]);
$session = $stmt->fetch();

if (!$session) {
    die(json_encode([]));
}

$currentUserId = $session['user_id'];
$currentUserType = $session['user_type'];

// Get conversations (same query as in messages.php)
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

echo json_encode($conversations);
?>