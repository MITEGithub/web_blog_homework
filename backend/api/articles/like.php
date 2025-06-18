<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require_once('../../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);
$article_id = isset($data['id']) ? intval($data['id']) : 0;

if (!$article_id) {
    echo json_encode(['code' => 1, 'error' => '缺少文章 ID'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "UPDATE articles SET likes = likes + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);

if ($stmt->execute()) {
    echo json_encode(['code' => 0, 'message' => '点赞成功'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['code' => 1, 'error' => '点赞失败'], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

