<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST');

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);
$article_id = intval($data['id'] ?? 0);

if (!$article_id) {
    echo json_encode(['code' => 1, 'error' => '缺少文章 ID'], JSON_UNESCAPED_UNICODE);
    exit;
}

$conn->begin_transaction();

try {
    $stmt1 = $conn->prepare("DELETE FROM comments WHERE article_id = ?");
    $stmt1->bind_param("i", $article_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt2->bind_param("i", $article_id);
    $stmt2->execute();

    $conn->commit();

    echo json_encode(['code' => 0, 'message' => '删除成功'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['code' => 1, 'error' => '删除失败：' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

