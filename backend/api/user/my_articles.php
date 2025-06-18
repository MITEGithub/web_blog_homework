<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require_once('../../config/db.php');

$token = $headers['Authorization']
    ?? $_SERVER['HTTP_AUTHORIZATION']
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
    ?? '';

if (!$token) {
    echo json_encode(['code' => 1, 'msg' => '未登录']);
    exit;
}

// 验证 token
$stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['code' => 1, 'msg' => '无效 token']);
    exit;
}

$user_id = $user['id'];

$sql = "SELECT id, title, created_at, views, likes,
        (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id) AS comment_count
        FROM articles
        WHERE author_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

echo json_encode([
    'code' => 0,
    'msg' => 'success',
    'data' => $articles
]);
?>

