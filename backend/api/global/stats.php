<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once('../../config/db.php');

$result1 = mysqli_query($conn, "SELECT COUNT(*) AS user_count FROM users");
$userCount = mysqli_fetch_assoc($result1)['user_count'];

$result2 = mysqli_query($conn, "SELECT COUNT(*) AS article_count FROM articles");
$articleCount = mysqli_fetch_assoc($result2)['article_count'];

$result3 = mysqli_query($conn, "SELECT COUNT(*) AS comment_count FROM comments");
$commentCount = mysqli_fetch_assoc($result3)['comment_count'];

$result4 = mysqli_query($conn, "SELECT SUM(likes) AS like_count FROM articles");
$likeCount = mysqli_fetch_assoc($result4)['like_count'] ?? 0;

echo json_encode([
    'users' => $userCount,
    'articles' => $articleCount,
    'comments' => $commentCount,
    'likes' => $likeCount
], JSON_UNESCAPED_UNICODE);

