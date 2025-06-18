<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require_once('../../config/db.php');

$headers = getallheaders();
$token = $headers['Authorization']
    ?? $_SERVER['HTTP_AUTHORIZATION']
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
    ?? '';


if (!$token) {
    echo json_encode(['code' => 1, 'msg' => '未登录']);
    exit;
}

// 验证 token，获取用户 id
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

// 查询文章数据
$sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN 1 THEN 1 ELSE 0 END) AS published,
            SUM(CASE WHEN summary IS NULL OR summary = '' THEN 1 ELSE 0 END) AS drafts,
            SUM(views) AS views,
            SUM(likes) AS likes
        FROM articles WHERE author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$article_stats = $stmt->get_result()->fetch_assoc();

// 查询评论数
$stmt2 = $conn->prepare("SELECT COUNT(*) AS comments FROM comments WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$comment_stats = $stmt2->get_result()->fetch_assoc();

echo json_encode([
    'code' => 0,
    'msg' => 'success',
    'data' => array_merge($article_stats, $comment_stats)
]);
?>

