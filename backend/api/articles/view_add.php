<?php
require_once('../../config/db.php');
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$article_id = intval($data['id'] ?? 0);

if ($article_id <= 0) {
    echo json_encode(['code' => 1, 'msg' => '参数错误']);
    exit;
}

$sql = "UPDATE articles SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['code' => 1, 'msg' => '数据库预处理失败: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $article_id);
if ($stmt->execute()) {
    echo json_encode(['code' => 0, 'msg' => 'success']);
} else {
    echo json_encode(['code' => 1, 'msg' => '执行失败: ' . $stmt->error]);
}
$stmt->close();

