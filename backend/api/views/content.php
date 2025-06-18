<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$article_id = intval($_GET['id'] ?? 0);

if (!$article_id) {
    echo json_encode(['error' => '缺少参数']);
    exit;
}

$sql = "SELECT a.id, a.title, a.content, a.created_at, a.likes, a.views, u.username AS author
        FROM articles a
        JOIN users u ON a.author_id = u.id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['code' => 0, 'data' => $row], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => '文章不存在']);
}

$stmt->close();
$conn->close();

