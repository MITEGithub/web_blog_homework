<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$article_id = intval($_GET['article_id'] ?? 0);
if (!$article_id) {
    echo json_encode(['error' => '缺少文章ID']);
    exit;
}

$sql = "SELECT c.content, c.created_at, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.article_id = ?
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode(['code' => 0, 'data' => $comments], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

