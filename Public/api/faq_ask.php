<?php
session_start();
require_once __DIR__ . '/../../Include/db.php';
require_once __DIR__ . '/../../Include/auth.php';

if (!is_logged_in()) {
    echo json_encode(['success'=>false,'message'=>'Login required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$faq_id = (int)($data['faq_id'] ?? 0);
$rating = (int)($data['rating'] ?? 0);

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success'=>false,'message'=>'Invalid rating']);
    exit;
}

$db = getDB();
$stmt = $db->prepare("
INSERT INTO faq_ratings (faq_id, user_id, rating)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE rating = VALUES(rating)
");
$stmt->bind_param("iii", $faq_id, $_SESSION['user_id'], $rating);
$stmt->execute();

echo json_encode(['success'=>true,'message'=>'Rating saved']);
