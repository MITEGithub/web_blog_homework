<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require_once('../../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$title = trim($data['title'] ?? '');
$content = trim($data['content'] ?? '');
$summary = trim($data['summary'] ?? '');
$token = trim($data['token'] ?? '');

if (!$title || !$content || !$token) {
    echo json_encode(['error' => '缺少参数'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => '无效的 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$author_id = $user['id'];
if (!$summary) {
    $summary = mb_substr(strip_tags($content), 0, 100);
}

$stmt = $conn->prepare("INSERT INTO articles (author_id, title, content, summary, created_at, views, likes) VALUES (?, ?, ?, ?, NOW(), 0, 0)");
$stmt->bind_param("isss", $author_id, $title, $content, $summary);
$success = $stmt->execute();

echo json_encode($success ? ['message' => '发布成功'] : ['error' => '发布失败'], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

