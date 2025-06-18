<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require_once('../../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id'] ?? 0);
$title = trim($data['title'] ?? '');
$summary = trim($data['summary'] ?? '');
$content = trim($data['content'] ?? '');
$token = trim($data['token'] ?? '');

if (!$id || !$title || !$content || !$token) {
    echo json_encode(['error' => '缺少必要参数'], JSON_UNESCAPED_UNICODE);
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

$stmt = $conn->prepare("SELECT id FROM articles WHERE id = ? AND author_id = ?");
$stmt->bind_param("ii", $id, $author_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['error' => '文章不存在或无权限修改'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($summary)) {
    $summary = mb_substr(strip_tags($content), 0, 100);
}

$stmt = $conn->prepare("UPDATE articles SET title = ?, summary = ?, content = ? WHERE id = ?");
$stmt->bind_param("sssi", $title, $summary, $content, $id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['code' => 0, 'message' => '文章修改成功'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => '文章修改失败'], JSON_UNESCAPED_UNICODE);
}

