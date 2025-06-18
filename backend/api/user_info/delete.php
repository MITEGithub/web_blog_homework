<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';

$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

if (!$token) {
    echo json_encode(['error' => '未提供 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE token = ?");
$stmt->bind_param("s", $token);

if ($stmt->execute()) {
    echo json_encode(['message' => '账号已删除'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => '删除失败'], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

