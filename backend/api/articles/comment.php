<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$token = trim($data['token'] ?? '');
$content = trim($data['content'] ?? '');
$article_id = intval($data['article_id'] ?? 0);

if (!$token || !$content || !$article_id) {
    echo json_encode(['error' => '参数不完整'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 验证 token
$stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => '无效 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = $user['id'];

$stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $article_id, $user_id, $content);
$success = $stmt->execute();

echo json_encode($success ? ['code' => 0, 'message' => '评论成功'] : ['error' => '评论失败'], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

