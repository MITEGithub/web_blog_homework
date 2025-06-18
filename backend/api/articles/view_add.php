<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => '只允许 POST 请求'], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$articleId = isset($data['id']) ? intval($data['id']) : 0;

if ($articleId <= 0) {
    echo json_encode(['error' => '文章 ID 无效'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('i', $articleId);

if ($stmt->execute()) {
    echo json_encode(['code' => 0, 'message' => '阅读数已更新']);
} else {
    echo json_encode(['error' => '更新失败', 'details' => $stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

